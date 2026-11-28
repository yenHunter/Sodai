<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Refund;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Support\DemoEnvironment;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Undo a DemoCatalogSeeder run. Rows are found by the exact identifier lists
 * the seeder publishes, so a real order, refund, coupon, product, category,
 * brand, banner or account is never matched, and each deletion is guarded
 * against rows that have since been referenced by real data.
 *
 * Store settings the seeder created are removed too, but only while they still
 * hold the value the demo wrote; a setting a shop has changed is kept and
 * reported. This command never touches settings it cannot attribute to a demo
 * run. Demo images copied into storage are deleted with the rows.
 */
class ClearDemoCatalogCommand extends Command
{
    protected $signature = 'demo:clear
        {--dry-run : Report what would be removed without deleting anything}
        {--force : Delete without asking for confirmation}';

    protected $description = 'Delete only the rows created by the demo catalog seeder';

    public function handle(): int
    {
        if (app()->environment('production') && ! DemoEnvironment::enabled()) {
            $this->components->error('demo:clear is disabled in production unless DEMO_MODE=true (see docs/demo-mode.md).');

            return self::FAILURE;
        }

        $orderIds = Order::withTrashed()
            ->whereIn('order_number', DemoCatalogSeeder::orderNumbers())
            ->pluck('id')
            ->all();
        $productIds = Product::withTrashed()
            ->whereIn('slug', DemoCatalogSeeder::productSlugs())
            ->pluck('id')
            ->all();
        $variantIds = ProductVariant::withTrashed()
            ->whereIn('sku', DemoCatalogSeeder::variantSkus())
            ->pluck('id')
            ->all();
        $categoryIds = Category::withTrashed()
            ->whereIn('slug', DemoCatalogSeeder::categorySlugs())
            ->pluck('id')
            ->all();
        $brandIds = Brand::withTrashed()
            ->whereIn('slug', DemoCatalogSeeder::brandSlugs())
            ->pluck('id')
            ->all();
        $bannerIds = Banner::withTrashed()
            ->whereIn('image', DemoCatalogSeeder::bannerImages())
            ->pluck('id')
            ->all();
        $couponIds = Coupon::withTrashed()
            ->whereIn('code', [DemoCatalogSeeder::couponCode()])
            ->pluck('id')
            ->all();
        $userIds = User::withTrashed()
            ->whereIn('email', DemoCatalogSeeder::customerEmails())
            ->pluck('id')
            ->all();
        $adminIds = Admin::withTrashed()
            ->where('email', DemoCatalogSeeder::ADMIN_EMAIL)
            ->pluck('id')
            ->all();
        $recordedSettings = DemoCatalogSeeder::recordedSettings();

        $counts = [
            'orders' => count($orderIds),
            'products' => count($productIds),
            'variants' => count($variantIds),
            'banners' => count($bannerIds),
            'categories' => count($categoryIds),
            'brands' => count($brandIds),
            'coupons' => count($couponIds),
            'customers' => count($userIds),
            'admins' => count($adminIds),
            'settings' => count($recordedSettings),
        ];

        if (array_sum($counts) === 0) {
            $this->components->info('No demo catalog rows found.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->components->info('Would remove these demo rows:');

            foreach ($counts as $label => $count) {
                $this->components->twoColumnDetail($label, (string) $count);
            }

            $this->components->info('Nothing was deleted.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('Delete '.array_sum($counts).' demo row(s)? Real records are never touched.', false)) {
            $this->components->info('Nothing deleted.');

            return self::SUCCESS;
        }

        $removed = [];
        $kept = [];
        $visitorCarts = 0;
        $visitorWishlists = 0;

        DB::transaction(function () use ($orderIds, $productIds, $variantIds, $bannerIds, $categoryIds, $brandIds, $couponIds, $userIds, $adminIds, $recordedSettings, &$removed, &$kept, &$visitorCarts, &$visitorWishlists) {
            // Placed in real visitors' carts or wishlists, these rows disappear
            // through the schema's cascade; count them so the side effect is
            // reported rather than silent.
            $visitorCarts = DB::table('cart_items')
                ->join('carts', 'carts.id', '=', 'cart_items.cart_id')
                ->whereIn('cart_items.product_variant_id', $variantIds)
                ->where(function ($query) use ($userIds) {
                    $query->whereNull('carts.user_id')->orWhereNotIn('carts.user_id', $userIds);
                })
                ->count();

            $visitorWishlists = DB::table('wishlists')
                ->whereIn('product_id', $productIds)
                ->when($userIds !== [], fn ($query) => $query->whereNotIn('user_id', $userIds))
                ->count();

            // Orders first: everything that hangs off them goes with them, and
            // the accounts/coupon below are then free of references.
            $removed['order items'] = OrderItem::whereIn('order_id', $orderIds)->delete();
            $removed['status history entries'] = OrderStatusHistory::whereIn('order_id', $orderIds)->delete();
            $removed['refunds'] = Refund::withTrashed()->whereIn('order_id', $orderIds)->forceDelete();
            $removed['reviews'] = Review::whereIn('order_id', $orderIds)->delete();
            $removed['orders'] = $this->forceDeleteAll(Order::withTrashed()->whereIn('id', $orderIds)->get());

            // Catalog. Variants, images, option links, carts and wishlists fall
            // away with their product through the schema's cascade rules.
            $removedProducts = 0;

            foreach (Product::withTrashed()->whereIn('id', $productIds)->get() as $product) {
                if (OrderItem::where('product_id', $product->id)->exists()) {
                    $kept[] = "product \"{$product->slug}\" (still on a non-demo order)";

                    continue;
                }

                $product->forceDelete();
                $removedProducts++;
            }

            $removed['products'] = $removedProducts;

            $removed['banners'] = $this->forceDeleteAll(Banner::withTrashed()->whereIn('id', $bannerIds)->get());

            $removedCoupons = 0;

            foreach (Coupon::withTrashed()->whereIn('id', $couponIds)->get() as $coupon) {
                if (Order::withTrashed()->where('coupon_id', $coupon->id)->exists()) {
                    $kept[] = "coupon {$coupon->code} (used by a non-demo order)";

                    continue;
                }

                $coupon->forceDelete();
                $removedCoupons++;
            }

            $removed['coupons'] = $removedCoupons;

            $removedCategories = 0;

            foreach (Category::withTrashed()->whereIn('id', $categoryIds)->get() as $category) {
                if (Product::withTrashed()->where('category_id', $category->id)->exists()
                    || Category::withTrashed()->where('parent_id', $category->id)->exists()) {
                    $kept[] = "category \"{$category->slug}\" (still holds real catalog rows)";

                    continue;
                }

                $category->forceDelete();
                $removedCategories++;
            }

            $removed['categories'] = $removedCategories;

            $removedBrands = 0;

            foreach (Brand::withTrashed()->whereIn('id', $brandIds)->get() as $brand) {
                if (Product::withTrashed()->where('brand_id', $brand->id)->exists()) {
                    $kept[] = "brand \"{$brand->slug}\" (still on a real product)";

                    continue;
                }

                $brand->forceDelete();
                $removedBrands++;
            }

            $removed['brands'] = $removedBrands;

            // Settings the seeder created, only while untouched by the shop.
            $removedSettings = 0;

            foreach ($recordedSettings as $path => $value) {
                [$group, $key] = explode('.', $path, 2);

                if ((string) Setting::get($group, $key) !== (string) $value) {
                    $kept[] = "setting {$path} (changed since the demo seeded it)";

                    continue;
                }

                Setting::where('group', $group)->where('key', $key)->delete();
                Setting::forgetGroupCache($group);
                $removedSettings++;
            }

            Setting::where('group', 'demo')->delete();
            Setting::forgetGroupCache('demo');

            $removed['demo-created settings'] = $removedSettings;

            // Accounts last: addresses, carts, reviews and sessions cascade.
            $removedUsers = 0;

            foreach (User::withTrashed()->whereIn('id', $userIds)->get() as $user) {
                if (Order::withTrashed()->where('user_id', $user->id)->exists()) {
                    $kept[] = "customer {$user->email} (has an order outside the demo)";

                    continue;
                }

                $user->forceDelete();
                $removedUsers++;
            }

            $removed['customers'] = $removedUsers;

            $removedAdmins = 0;

            foreach (Admin::withTrashed()->whereIn('id', $adminIds)->get() as $admin) {
                if (OrderStatusHistory::where('changed_by', $admin->id)->exists()
                    || Refund::withTrashed()->where('processed_by', $admin->id)->exists()) {
                    $kept[] = "admin {$admin->email} (still credited on real records)";

                    continue;
                }

                if (Schema::hasTable('model_has_roles')) {
                    DB::table('model_has_roles')
                        ->where('model_type', $admin->getMorphClass())
                        ->where('model_id', $admin->id)
                        ->delete();
                }

                $admin->forceDelete();
                $removedAdmins++;
            }

            $removed['admins'] = $removedAdmins;
        });

        $this->components->info('Demo catalog removed.');

        foreach ($removed as $label => $count) {
            $this->components->twoColumnDetail($label, (string) $count);
        }

        foreach ($kept as $line) {
            $this->components->warn('Kept '.$line.'.');
        }

        if ($visitorCarts > 0 || $visitorWishlists > 0) {
            $this->components->warn("Removed {$visitorCarts} cart item(s) and {$visitorWishlists} wishlist row(s) that non-demo visitors had pointing at demo products.");
        }

        // A kept demo row still needs the photo it points at.
        if (Product::withTrashed()->whereIn('id', $productIds)->exists()
            || Banner::withTrashed()->whereIn('id', $bannerIds)->exists()
            || Category::withTrashed()->whereIn('id', $categoryIds)->exists()) {
            $this->components->warn('Demo images were left in storage because some demo rows were kept.');
        } else {
            $this->deleteImages();
        }

        return self::SUCCESS;
    }

    /**
     * The seeder copies its photos into storage so the storefront can serve
     * them; the rows are gone, so the copies go too.
     */
    private function deleteImages(): void
    {
        foreach (['products', 'banners', 'categories'] as $folder) {
            File::deleteDirectory(storage_path("app/public/{$folder}/demo"));
        }
    }

    /**
     * @param  Collection<int, Model>  $models
     */
    private function forceDeleteAll($models): int
    {
        $count = 0;

        foreach ($models as $model) {
            $model->forceDelete();
            $count++;
        }

        return $count;
    }
}
