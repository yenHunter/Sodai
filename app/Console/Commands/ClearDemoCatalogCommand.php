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
use App\Models\Refund;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Undo a DemoCatalogSeeder run. Rows are found only by the namespaces the
 * seeder writes (DEMO-/demo-/@example.com), so a real order, refund, coupon,
 * product, category, brand, banner or account is never matched, and each
 * deletion is guarded against rows that have since been referenced by real
 * data. Store settings are left exactly as they are.
 */
class ClearDemoCatalogCommand extends Command
{
    protected $signature = 'demo:clear
        {--force : Delete without asking for confirmation}';

    protected $description = 'Delete only the rows created by the demo catalog seeder';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->components->error('demo:clear is disabled in production, same as the demo seeder.');

            return self::FAILURE;
        }

        $orderIds = $this->demoOrderIds();
        $productIds = $this->ids(Product::withTrashed(), 'slug');
        $bannerIds = Banner::withTrashed()->where('image', 'like', 'banners/demo/%')->pluck('id')->all();
        $categoryIds = $this->ids(Category::withTrashed(), 'slug');
        $brandIds = $this->ids(Brand::withTrashed(), 'slug');
        $couponIds = Coupon::withTrashed()
            ->where('code', DemoCatalogSeeder::ID_PREFIX.'WELCOME10')
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

        $total = count($orderIds) + count($productIds) + count($bannerIds) + count($categoryIds)
            + count($brandIds) + count($couponIds) + count($userIds) + count($adminIds);

        if ($total === 0) {
            $this->components->info('No demo catalog rows found.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Delete {$total} demo row(s)? Real records are never touched.", false)) {
            $this->components->info('Nothing deleted.');

            return self::SUCCESS;
        }

        $removed = [];
        $kept = [];

        DB::transaction(function () use ($orderIds, $productIds, $bannerIds, $categoryIds, $brandIds, $couponIds, $userIds, $adminIds, &$removed, &$kept) {
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

        $this->components->info('Store settings were left untouched.');

        return self::SUCCESS;
    }

    /**
     * Demo catalog rows carry the seeder's demo- slug namespace; nothing a
     * real shop creates starts with it.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<*>  $query
     * @return array<int, int>
     */
    private function ids($query, string $column): array
    {
        return $query->where($column, 'like', DemoCatalogSeeder::SLUG_PREFIX.'%')->pluck('id')->all();
    }

    /** @return array<int, int> */
    private function demoOrderIds(): array
    {
        return Order::withTrashed()
            ->where('order_number', 'like', DemoCatalogSeeder::ID_PREFIX.'ORD-%')
            ->pluck('id')
            ->all();
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
