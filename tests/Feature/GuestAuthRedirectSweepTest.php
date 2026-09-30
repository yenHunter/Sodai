<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Tests\TestCase;

/**
 * Integration sweep guarding the CustomerAuthenticated middleware's
 * login redirect. It used to redirect to route('login') — a route that
 * does not exist in this app (everything is prefixed with `visitor.`),
 * so every guest hitting an account page got a 500 RouteNotFoundException
 * instead of a redirect. This test iterates the router at runtime, so any
 * route added under auth.customer in the future is swept automatically.
 */
class GuestAuthRedirectSweepTest extends TestCase
{
    use RefreshDatabase;

    private string $loginUrl;

    /**
     * Real rows for every route parameter that must bind (SubstituteBindings
     * runs before the auth middleware, so guests would 404 without them).
     */
    private array $bindings = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->loginUrl = route('visitor.login');

        $user = User::factory()->create();

        $this->bindings = [
            'address' => Address::create([
                'user_id' => $user->id,
                'recipient_name' => 'Sweep Test',
                'recipient_phone' => '01700000000',
                'address_line_1' => '1 Test Street',
                'address_line_2' => '',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'zip_code' => '1200',
                'country' => 'Bangladesh',
            ])->id,
            'cartItem' => CartItem::factory()->create()->id,
            'order' => Order::factory()->create()->id,
            'review' => Review::factory()->create()->id,
            'product' => Product::factory()->create()->id,
        ];
    }

    /**
     * All routes carrying the auth.customer middleware, as
     * [method, uri, name] tuples with parameters pre-bound.
     */
    private function customerAuthRoutes(): array
    {
        return collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $route) => in_array('auth.customer', $route->middleware(), true))
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

    public function test_critical_account_routes_exist_and_are_swept(): void
    {
        $names = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $route) => in_array('auth.customer', $route->middleware(), true))
            ->map(fn (Route $route) => $route->getName())
            ->all();

        // If any of these disappear (renamed, prefix dropped from
        // bootstrap/app.php, middleware renamed), this fails loudly
        // instead of the sweep silently covering nothing.
        foreach ([
            'visitor.account.show',
            'visitor.account.orders.index',
            'visitor.account.orders.show',
            'visitor.account.orders.cancel',
            'visitor.account.cart.add',
            'visitor.account.addresses.store',
            'visitor.account.wishlist.toggle',
            'visitor.account.reviews.store',
        ] as $required) {
            $this->assertContains($required, $names, "Expected auth.customer route [{$required}] to exist.");
        }

        $this->assertGreaterThanOrEqual(15, count($names), 'auth.customer route coverage unexpectedly shrank.');
    }

    public function test_every_customer_auth_route_redirects_guests_to_login(): void
    {
        $routes = $this->customerAuthRoutes();

        $this->assertNotEmpty($routes, 'No auth.customer routes discovered — the sweep is covering nothing.');

        foreach ($routes as [$method, $uri, $name]) {
            $response = $this->call($method, $uri);

            $this->assertSame(
                302,
                $response->status(),
                "Guest GET/POST to [{$method} {$uri}] ({$name}) should redirect, got HTTP {$response->status()}."
            );

            $this->assertSame(
                $this->loginUrl,
                $response->headers->get('Location'),
                "Guest hitting [{$method} {$uri}] ({$name}) must land on the login page."
            );
        }
    }
}
