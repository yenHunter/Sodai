<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Attribute;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Refund;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

/**
 * Admin-guard mirror of GuestAuthRedirectSweepTest: every route carrying
 * auth.admin middleware must send unauthenticated visitors to the admin
 * login page (302), never leak data or blow up with an exception.
 * Iterates the router at runtime, so new admin routes are swept automatically.
 */
class AdminAuthRedirectSweepTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    private string $loginUrl;

    /**
     * Real rows for every route parameter that must bind (SubstituteBindings
     * runs before the auth middleware, so unauthenticated requests would 404
     * without them and the middleware would never be exercised).
     */
    private array $bindings = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->loginUrl = route('admin.login.view');

        $order = Order::factory()->create();
        $product = Product::factory()->create();
        $variant = $product->defaultVariant;

        $this->bindings = [
            'admin' => Admin::factory()->create()->id,
            'attribute' => Attribute::factory()->create()->id,
            'banner' => Banner::factory()->create()->id,
            'brand' => Brand::factory()->create()->id,
            'cart' => Cart::factory()->create()->id,
            'category' => Category::factory()->create()->id,
            'coupon' => Coupon::factory()->create()->id,
            'customer' => User::factory()->create()->id,
            'faq' => Faq::factory()->create()->id,
            'faq_category' => FaqCategory::factory()->create()->id,
            'image' => ProductImage::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'image_path' => 'products/sweep-test.webp',
                'is_primary' => true,
                'sort_order' => 0,
            ])->id,
            'offer' => Offer::factory()->create()->id,
            'order' => $order->id,
            'product' => $product->id,
            'refund' => Refund::factory()->create()->id,
            'review' => Review::factory()->create()->id,
            'role' => Role::firstOrCreate(['name' => 'sweep-role', 'guard_name' => 'admin'])->id,
            'slug' => 'about', // must be a valid CmsPage::SLUGS value
            'variant' => $variant->id,
        ];
    }

    /**
     * All routes carrying the auth.admin middleware, as
     * [method, uri, name] tuples with parameters pre-bound.
     */
    private function adminAuthRoutes(): array
    {
        return collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $route) => in_array('auth.admin', $route->middleware(), true))
            ->flatMap(fn (Route $route) => collect($route->methods())
                ->reject(fn (string $method) => $method === 'HEAD')
                ->map(fn (string $method) => [$method, $this->bindParameters($route), $route->getName()]))
            ->values()
            ->all();
    }

    /**
     * Replaces {param} placeholders with fixture IDs by parameter name.
     */
    private function bindParameters(Route $route): string
    {
        return preg_replace_callback(
            '/\{(\w+)(\?)?\}/',
            fn (array $m) => (string) ($this->bindings[$m[1]] ?? '1'),
            $route->uri()
        );
    }

    public function test_critical_admin_routes_exist_and_are_swept(): void
    {
        $names = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $route) => in_array('auth.admin', $route->middleware(), true))
            ->map(fn (Route $route) => $route->getName())
            ->all();

        // If any of these disappear (renamed, admin prefix dropped from
        // bootstrap/app.php, middleware alias renamed), this fails loudly
        // instead of the sweep silently covering nothing.
        foreach ([
            'admin.dashboard',
            'admin.ecommerce.category.store',
            'admin.ecommerce.product.store',
            'admin.ecommerce.product.images.delete',
            'admin.ecommerce.order.store',
            'admin.ecommerce.order.status.update',
            'admin.ecommerce.refund.approve',
            'admin.ecommerce.review.approve',
            'admin.cms.banner.store',
            'admin.settings.company.update',
            'admin.users.store',
            'admin.users.roles.store',
        ] as $required) {
            $this->assertContains($required, $names, "Expected auth.admin route [{$required}] to exist.");
        }

        $this->assertGreaterThanOrEqual(40, count($names), 'auth.admin route coverage unexpectedly shrank.');
    }

    public function test_every_admin_auth_route_redirects_unauthenticated_users_to_login(): void
    {
        $routes = $this->adminAuthRoutes();

        $this->assertNotEmpty($routes, 'No auth.admin routes discovered — the sweep is covering nothing.');

        foreach ($routes as [$method, $uri, $name]) {
            $response = $this->call($method, $uri);

            $this->assertSame(
                302,
                $response->status(),
                "Unauthenticated [{$method} {$uri}] ({$name}) should redirect, got HTTP {$response->status()}."
            );

            $this->assertSame(
                $this->loginUrl,
                $response->headers->get('Location'),
                "Unauthenticated hit on [{$method} {$uri}] ({$name}) must land on the admin login page."
            );
        }
    }
}
