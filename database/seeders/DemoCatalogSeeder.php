<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Admin;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use App\Models\Refund;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\Shared\PricingService;
use App\Support\DemoEnvironment;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * A believable demo storefront: categories, brands, variant products with
 * images, banners, customers, a spread of orders in every status, reviews,
 * a coupon and a refund.
 *
 * Kept out of DatabaseSeeder on purpose. Normal development seeding stays
 * untouched; run this one explicitly:
 *
 *     php artisan db:seed --class=DemoCatalogSeeder
 *
 * Seed it with `php artisan demo:seed` (or the db:seed line above) and undo
 * that run, and only that run, with `php artisan demo:clear`.
 *
 * Relative dates anchor to "now" unless `DEMO_DATE=YYYY-MM-DD` is set, which
 * pins the history so screenshots and tests reproduce exactly.
 *
 * Re-running it is safe: every row is found by a stable slug, SKU, order
 * number or email and updated in place. Every identifier a real store also
 * issues for itself — order numbers, refund numbers, coupon codes, catalog
 * slugs and variant SKUs — is namespaced with DEMO-/demo-, so seeding beside
 * live data can never adopt or rewrite a real order, refund, coupon, category,
 * brand or product. Store settings are only created where the shop has no
 * value yet. Images live in public/images/demo and are copied into
 * storage/app/public on first run, so a fresh checkout only needs
 * `php artisan storage:link`.
 */
class DemoCatalogSeeder extends Seeder
{
    public const ADMIN_EMAIL = 'demo-admin@example.com';

    public const CUSTOMER_EMAIL = 'demo-customer@example.com';

    /**
     * Marker on every identifier the application generates for real business
     * records. Those generators build `<settings prefix><digits>`, so a demo
     * identifier can neither equal one of theirs nor be picked up by the
     * "latest number" lookups that derive the next sequence.
     */
    public const ID_PREFIX = 'DEMO-';

    /**
     * Marker on catalog slugs. A real shop may legitimately own the slug
     * "stationery", so the demo category is "demo-stationery" instead and the
     * two can never be matched to each other.
     */
    public const SLUG_PREFIX = 'demo-';

    /** @var array<string, Category> */
    private array $categories = [];

    /** @var array<string, Brand> */
    private array $brands = [];

    /** @var array<string, Product> */
    private array $products = [];

    /** @var array<string, ProductVariant> */
    private array $variants = [];

    /** @var array<string, User> */
    private array $customers = [];

    /** @var array<string, array<string, string>> */
    private array $writtenSettings = [];

    private ?Admin $admin = null;

    private ?Coupon $coupon = null;

    private ?string $passwordHash = null;

    public function run(): void
    {
        // A public demo runs under APP_ENV=production, so demo mode — not the
        // environment name — is the switch. The password guard below still
        // refuses to create the well-known accounts without SEED_DEMO_PASSWORD.
        if (app()->environment('production') && ! DemoEnvironment::enabled()) {
            $this->command?->warn('DemoCatalogSeeder skipped: demo accounts are disabled in production unless DEMO_MODE=true.');

            return;
        }

        // Outside a developer's own machine the demo's well-known password
        // would be a live super-admin, so an explicit password is required.
        if (! app()->environment(['local', 'development', 'testing']) && blank(config('app.demo_password'))) {
            $this->command?->warn('DemoCatalogSeeder skipped: set SEED_DEMO_PASSWORD to seed demo accounts outside local/testing.');

            return;
        }

        // Roles, permissions and the color/size attribute keys this catalog
        // filters on. Both seeders are idempotent.
        $this->call([RolePermissionSeeder::class, AttributeSeeder::class]);

        $this->publishImages();
        $this->seedSettings();
        $this->seedAdmin();

        $this->seedCategories();
        $this->seedBrands();
        $this->seedProducts();
        $this->seedTags();
        $this->seedRelatedProducts();
        $this->seedBanners();
        $this->seedCustomers();
        $this->seedCoupon();

        $this->seedOrders();
        $this->seedCart();
        $this->seedWishlist();
        $this->seedReviews();
        $this->refreshCatalogStats();

        $orderIds = Order::whereIn('order_number', self::orderNumbers())->pluck('id');

        $this->command?->info('Demo catalog seeded: '
            .Product::whereIn('slug', self::productSlugs())->count().' products, '
            .ProductVariant::whereIn('sku', self::variantSkus())->count().' variants, '
            .$orderIds->count().' orders, '
            .Review::whereIn('order_id', $orderIds)->count().' reviews.');
        $this->command?->info('Admin login: '.self::ADMIN_EMAIL.' / '.$this->password());
        $this->command?->info('Customer login: '.self::CUSTOMER_EMAIL.' / '.$this->password());
    }

    private function password(): string
    {
        return (string) (config('app.demo_password') ?: 'Demo#12345');
    }

    /**
     * The demo password, hashed once per run. The admin and the demo customer
     * sign in with the same password, and bcrypt is deliberately expensive, so
     * both rows get this one hash instead of paying a full work factor each.
     * The `hashed` cast on both models recognises it as already hashed and
     * stores it untouched.
     */
    private function passwordHash(): string
    {
        return $this->passwordHash ??= Hash::make($this->password());
    }

    /**
     * The reference point for every relative date this seeder writes. Normally
     * "now"; `DEMO_DATE=YYYY-MM-DD` pins it so re-running produces the same
     * history, which keeps screenshots and tests stable.
     */
    private function demoDate(): CarbonInterface
    {
        $anchor = config('app.demo_date');

        return $anchor ? Carbon::parse($anchor) : now();
    }

    // ─────────────────────────────────────────────
    // ASSETS
    // ─────────────────────────────────────────────

    /**
     * Copy the committed demo photos into the public storage disk. The
     * seeder references storage paths, so the files have to exist there;
     * public/images/demo is the tracked source of truth.
     */
    private function publishImages(): void
    {
        foreach (['products', 'banners', 'categories'] as $folder) {
            $source = public_path("images/demo/{$folder}");
            $target = storage_path("app/public/{$folder}/demo");

            if (! File::isDirectory($source)) {
                continue;
            }

            File::ensureDirectoryExists($target);

            foreach (File::files($source) as $file) {
                $destination = $target.'/'.$file->getFilename();

                if (! File::exists($destination) || File::size($destination) !== $file->getSize()) {
                    File::copy($file->getPathname(), $destination);
                }
            }
        }

        if (! File::exists(public_path('storage'))) {
            $this->command?->warn('public/storage is missing — run `php artisan storage:link` so the demo images load.');
        }
    }

    // ─────────────────────────────────────────────
    // SETTINGS / ACCOUNTS
    // ─────────────────────────────────────────────

    /**
     * Store identity and the checkout/shipping defaults the seeded orders
     * were priced against. Only written into a group that is still empty:
     * a demo run beside a live shop must leave that shop's name, currency,
     * shipping charges and order prefix exactly as they are.
     */
    private function seedSettings(): void
    {
        $this->writtenSettings = [];

        $this->seedMissingSettings('company', [
            'name' => 'Sodai',
            'email' => 'hello@example.com',
            'phone' => '+880 1700 000000',
            'address' => 'House 12, Road 7, Dhanmondi, Dhaka 1205',
            'currency' => 'BDT',
            'currency_symbol_position' => 'before',
            'currency_decimals' => '0',
        ], [
            'currency_decimals' => 'integer',
        ]);

        $this->seedMissingSettings('shipping', [
            'operation_areas' => json_encode(['Dhaka']),
            'inside_area_charge' => '60',
            'outside_area_charge' => '120',
            'enable_free_shipping' => '1',
            'free_shipping_threshold' => '7500',
        ], [
            'operation_areas' => 'json',
            'enable_free_shipping' => 'boolean',
        ]);

        $this->seedMissingSettings('order', [
            'allow_guest_checkout' => '1',
            'order_number_prefix' => 'ORD-',
        ], [
            'allow_guest_checkout' => 'boolean',
        ]);

        $this->rememberWrittenSettings();
    }

    /**
     * Create the given keys only where the group has no row yet.
     * Setting::setMany() upserts, so it would rewrite a live shop's values;
     * the settings it does keep are reported instead of silently ignored.
     *
     * @param  array<string, string>  $values
     * @param  array<string, string>  $types
     */
    private function seedMissingSettings(string $group, array $values, array $types = []): void
    {
        $existing = Setting::group($group);
        $missing = [];
        $kept = [];

        foreach ($values as $key => $value) {
            if (! array_key_exists($key, $existing)) {
                $missing[$key] = $value;

                continue;
            }

            if ((string) $existing[$key] !== (string) $value) {
                $kept[] = $key;
            }
        }

        if ($missing !== []) {
            Setting::setMany($group, $missing, array_intersect_key($types, $missing));
            $this->writtenSettings[$group] = $missing;
        }

        if ($kept !== []) {
            $this->command?->warn('Kept the store\'s existing '.$group.' settings: '.implode(', ', $kept).'.');
        }
    }

    /**
     * Remember the settings this run created, so `demo:clear` can remove
     * exactly those values later — and only while they still hold what the
     * demo wrote. Entries recorded by earlier runs are merged, not replaced.
     */
    private function rememberWrittenSettings(): void
    {
        $written = [];

        foreach ($this->writtenSettings as $group => $keys) {
            foreach ($keys as $key => $value) {
                $written[$group.'.'.$key] = (string) $value;
            }
        }

        if ($written === []) {
            return;
        }

        Setting::setMany('demo', [
            'written_settings' => json_encode(array_replace(self::recordedSettings(), $written)),
        ], [
            'written_settings' => 'json',
        ]);
    }

    /**
     * Settings the demo seeder created, as "group.key" => value it wrote.
     * Values a shop has since changed are filtered out by `demo:clear`.
     *
     * @return array<string, string>
     */
    public static function recordedSettings(): array
    {
        $recorded = json_decode((string) Setting::get('demo', 'written_settings', '{}'), true);

        return is_array($recorded) ? $recorded : [];
    }

    private function seedAdmin(): void
    {
        $this->admin = Admin::updateOrCreate(
            ['email' => self::ADMIN_EMAIL],
            [
                'name' => 'Mahmudul Hasan',
                'phone' => '+880 1711 223344',
                'password' => $this->passwordHash(),
                'is_active' => true,
            ],
        );

        $this->admin->syncRoles(['super-admin']);
    }

    // ─────────────────────────────────────────────
    // CATALOG
    // ─────────────────────────────────────────────

    /**
     * @return array<int, array<string, string>>
     */
    private static function categoryDefinitions(): array
    {
        // Flat on purpose: the home page counts a category's own products
        // (not its children's), so leaf-only categories keep those counts
        // truthful. Every product below belongs to exactly one of these.
        return [
            ['slug' => 'kitchen-dining', 'name' => 'Kitchen & Dining', 'image' => 'kitchen-dining.webp', 'sort' => 1,
                'description' => 'Everyday ceramics, cypress boards and tableware made to be used, not saved for guests.'],
            ['slug' => 'tea-coffee', 'name' => 'Tea & Coffee', 'image' => 'tea-coffee.webp', 'sort' => 2,
                'description' => 'Teapots, kettles and matcha tools for a slower cup, chosen for how they pour and how they feel.'],
            ['slug' => 'home-living', 'name' => 'Home & Living', 'image' => 'home-living.webp', 'sort' => 3,
                'description' => 'Washed linen, canvas and quiet textiles that soften a room without asking for attention.'],
            ['slug' => 'stationery', 'name' => 'Stationery', 'image' => 'stationery.webp', 'sort' => 4,
                'description' => 'Linen-bound notebooks and desk pieces for lists, sketches and plans worth keeping.'],
        ];
    }

    private function seedCategories(): void
    {
        foreach (self::categoryDefinitions() as $category) {
            $this->categories[$category['slug']] = Category::updateOrCreate(
                ['slug' => $this->demoSlug($category['slug'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'image' => 'categories/demo/'.$category['image'],
                    'parent_id' => null,
                    'is_active' => true,
                    'sort_order' => $category['sort'],
                ],
            );
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function brandDefinitions(): array
    {
        return [
            ['slug' => 'sodai-studio', 'name' => 'Sodai Studio', 'description' => 'Our own line, designed in Dhaka and made in small batches.'],
            ['slug' => 'kizen-ceramics', 'name' => 'Kizen Ceramics', 'description' => 'A family pottery studio working in speckled stoneware.'],
            ['slug' => 'aoyama', 'name' => 'Aoyama', 'description' => 'Tea ware and bamboo tools from a workshop in Shiga, Japan.'],
        ];
    }

    private function seedBrands(): void
    {
        foreach (self::brandDefinitions() as $index => $brand) {
            $this->brands[$brand['slug']] = Brand::updateOrCreate(
                ['slug' => $this->demoSlug($brand['slug'])],
                [
                    'name' => $brand['name'],
                    'description' => $brand['description'],
                    'website' => null,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }

    private function seedProducts(): void
    {
        foreach (self::productDefinitions() as $definition) {
            $this->seedProduct($definition);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function productDefinitions(): array
    {
        return [
            [
                'name' => 'Kizen Stoneware Mug',
                'slug' => 'kizen-stoneware-mug',
                'category' => 'kitchen-dining',
                'brand' => 'kizen-ceramics',
                'image' => 'kizen-mug.webp',
                'days_ago' => 2,
                'featured' => true,
                'short_description' => 'A hand-thrown stoneware mug with a satin glaze and a raw clay foot.',
                'description' => '<p>Thrown in small batches at the Kizen studio, each mug carries a slightly different speckle and a soft satin glaze that keeps the coffee warm a little longer.</p><p>The unglazed foot is left bare, so the clay reads warm against a table. Dishwasher and microwave safe.</p><ul><li>Stoneware, food-safe satin glaze</li><li>Raw clay foot, hand-finished rim</li><li>Holds 350 ml or 450 ml</li></ul>',
                'options' => [
                    'Color' => [['Sage', '#A9B7A2'], ['Clay', '#C08A63'], ['Charcoal', '#3C3A38']],
                    'Size' => [['350 ml', null], ['450 ml', null]],
                ],
                'variants' => [
                    ['sku' => 'SOD-MUG-SAGE-350', 'price' => 1850, 'purchase_price' => 980, 'stock' => 34, 'weight' => 380, 'options' => ['Color' => 'Sage', 'Size' => '350 ml'], 'default' => true, 'discount' => ['percentage', 15]],
                    ['sku' => 'SOD-MUG-SAGE-450', 'price' => 1950, 'purchase_price' => 1040, 'stock' => 18, 'weight' => 460, 'options' => ['Color' => 'Sage', 'Size' => '450 ml']],
                    ['sku' => 'SOD-MUG-CLAY-350', 'price' => 1850, 'purchase_price' => 980, 'stock' => 12, 'weight' => 380, 'options' => ['Color' => 'Clay', 'Size' => '350 ml']],
                    ['sku' => 'SOD-MUG-CLAY-450', 'price' => 1950, 'purchase_price' => 1040, 'stock' => 0, 'weight' => 460, 'options' => ['Color' => 'Clay', 'Size' => '450 ml']],
                    ['sku' => 'SOD-MUG-CHAR-350', 'price' => 1850, 'purchase_price' => 980, 'stock' => 26, 'weight' => 380, 'options' => ['Color' => 'Charcoal', 'Size' => '350 ml']],
                    ['sku' => 'SOD-MUG-CHAR-450', 'price' => 1950, 'purchase_price' => 1040, 'stock' => 9, 'weight' => 460, 'options' => ['Color' => 'Charcoal', 'Size' => '450 ml']],
                ],
            ],
            [
                'name' => 'Hinoki Serving Board',
                'slug' => 'hinoki-serving-board',
                'category' => 'kitchen-dining',
                'brand' => 'sodai-studio',
                'image' => 'hinoki-board.webp',
                'days_ago' => 13,
                'featured' => false,
                'short_description' => 'A light cypress board with a soft grain, cut and oiled by hand.',
                'description' => '<p>Hinoki is the wood Japanese kitchens reach for: light, closed-grain and gently antibacterial. Ours is cut from a single plank and finished with food-safe oil.</p><p>Use it for bread, cheese and fruit, then wipe it dry and stand it on edge. Never soak it or put it in the dishwasher.</p><ul><li>Japanese hinoki cypress</li><li>Food-safe oil finish</li><li>Three sizes, 1.8 cm thick</li></ul>',
                'options' => [
                    'Size' => [['Small', null], ['Medium', null], ['Large', null]],
                ],
                'variants' => [
                    ['sku' => 'SOD-BOARD-S', 'price' => 2150, 'purchase_price' => 1180, 'stock' => 15, 'weight' => 420, 'options' => ['Size' => 'Small']],
                    ['sku' => 'SOD-BOARD-M', 'price' => 2750, 'purchase_price' => 1520, 'stock' => 22, 'weight' => 640, 'options' => ['Size' => 'Medium'], 'default' => true],
                    ['sku' => 'SOD-BOARD-L', 'price' => 3450, 'purchase_price' => 1960, 'stock' => 8, 'weight' => 900, 'options' => ['Size' => 'Large']],
                ],
            ],
            [
                'name' => 'Aoyama Ceramic Teapot',
                'slug' => 'aoyama-ceramic-teapot',
                'category' => 'tea-coffee',
                'brand' => 'aoyama',
                'image' => 'aoyama-teapot.webp',
                'days_ago' => 5,
                'featured' => true,
                'short_description' => 'A speckled ceramic teapot with a bamboo handle and a clean pour.',
                'description' => '<p>Glazed in a warm off-white speckle, this pot is built around its spout: a short, sharp lip that stops the drip before it reaches the table.</p><p>The bamboo handle stays cool, and the wide lid opening makes it easy to rinse the leaves out afterwards.</p><ul><li>Ceramic body, bamboo handle</li><li>Removable stainless infuser</li><li>600 ml or 900 ml</li></ul>',
                'options' => [
                    'Size' => [['600 ml', null], ['900 ml', null]],
                ],
                'variants' => [
                    ['sku' => 'SOD-TEAPOT-600', 'price' => 4900, 'purchase_price' => 2750, 'stock' => 14, 'weight' => 720, 'options' => ['Size' => '600 ml'], 'default' => true],
                    ['sku' => 'SOD-TEAPOT-900', 'price' => 5700, 'purchase_price' => 3200, 'stock' => 7, 'weight' => 940, 'options' => ['Size' => '900 ml']],
                ],
            ],
            [
                'name' => 'Kōyō Pour-Over Kettle',
                'slug' => 'koyo-pour-over-kettle',
                'category' => 'tea-coffee',
                'brand' => 'sodai-studio',
                'image' => 'koyo-kettle.webp',
                'days_ago' => 24,
                'featured' => false,
                'short_description' => 'A slim gooseneck kettle with a walnut handle and a slow, steady flow.',
                'description' => '<p>The narrow gooseneck gives you a thin, controllable stream, which is the whole trick behind an even pour-over. The body is powder-coated steel; the handle is solid walnut.</p><p>Works on gas, electric and induction hobs. Hand wash only.</p><ul><li>1.0 L capacity</li><li>Gooseneck spout, walnut handle</li><li>Matte black or brushed steel</li></ul>',
                'options' => [
                    'Color' => [['Matte Black', '#2F2F2F'], ['Brushed Steel', '#B9BDC1']],
                ],
                'variants' => [
                    ['sku' => 'SOD-KETTLE-BLACK', 'price' => 5200, 'purchase_price' => 3100, 'stock' => 11, 'weight' => 780, 'options' => ['Color' => 'Matte Black'], 'default' => true, 'discount' => ['fixed', 400]],
                    ['sku' => 'SOD-KETTLE-STEEL', 'price' => 5600, 'purchase_price' => 3350, 'stock' => 6, 'weight' => 760, 'options' => ['Color' => 'Brushed Steel']],
                ],
            ],
            [
                'name' => 'Matcha Whisk Set',
                'slug' => 'matcha-whisk-set',
                'category' => 'tea-coffee',
                'brand' => 'aoyama',
                'image' => 'matcha-set.webp',
                'days_ago' => 28,
                'featured' => false,
                'short_description' => 'A pale ceramic bowl, bamboo chasen and scoop — everything for a bowl of matcha.',
                'description' => '<p>A simple starter set: a wide-mouthed bowl for whisking, a 80-tine bamboo chasen and a bamboo scoop for measuring out the powder.</p><p>Rinse the whisk in warm water before each use and stand it to dry on its base — never in the dishwasher.</p><ul><li>Ceramic bowl, 500 ml</li><li>80-tine bamboo chasen</li><li>Bamboo chashaku scoop</li></ul>',
                'options' => [],
                'variants' => [
                    ['sku' => 'SOD-MATCHA-SET', 'price' => 2300, 'purchase_price' => 1250, 'stock' => 19, 'weight' => 620, 'options' => [], 'default' => true],
                ],
            ],
            [
                'name' => 'Sakura Linen Throw',
                'slug' => 'sakura-linen-throw',
                'category' => 'home-living',
                'brand' => 'sodai-studio',
                'image' => 'sakura-throw.webp',
                'days_ago' => 9,
                'featured' => true,
                'short_description' => 'A washed linen throw with a soft fringe, warm in winter and breathable in summer.',
                'description' => '<p>Stone-washed European linen, woven in a plain weave and finished with a hand-knotted fringe. It starts crisp and softens with every wash.</p><p>Large enough to cover a two-seater sofa, light enough to fold over the end of a bed.</p><ul><li>100% washed linen</li><li>130 × 180 cm plus fringe</li><li>Machine wash cold, tumble low</li></ul>',
                'options' => [
                    'Color' => [['Sage', '#A9B7A2'], ['Oatmeal', '#D9CDB8'], ['Charcoal', '#3C3A38']],
                ],
                'variants' => [
                    ['sku' => 'SOD-THROW-SAGE', 'price' => 6400, 'purchase_price' => 3600, 'stock' => 9, 'weight' => 900, 'options' => ['Color' => 'Sage'], 'default' => true],
                    ['sku' => 'SOD-THROW-OAT', 'price' => 6400, 'purchase_price' => 3600, 'stock' => 13, 'weight' => 900, 'options' => ['Color' => 'Oatmeal']],
                    ['sku' => 'SOD-THROW-CHAR', 'price' => 6400, 'purchase_price' => 3600, 'stock' => 5, 'weight' => 900, 'options' => ['Color' => 'Charcoal']],
                ],
            ],
            [
                'name' => 'Nori Canvas Tote',
                'slug' => 'nori-canvas-tote',
                'category' => 'home-living',
                'brand' => 'sodai-studio',
                'image' => 'nori-tote.webp',
                'days_ago' => 16,
                'featured' => false,
                'short_description' => 'A heavy cotton canvas tote that holds a laptop, a market run or both.',
                'description' => '<p>Cut from 16 oz cotton canvas with bar-tacked handles and a flat base that lets the bag stand up on its own.</p><p>Inside there is one slip pocket for keys and a phone, and nothing else to get in the way.</p><ul><li>16 oz cotton canvas</li><li>Reinforced handles, flat base</li><li>38 × 40 cm, 12 cm gusset</li></ul>',
                'options' => [
                    'Color' => [['Natural', '#D8CBB4'], ['Charcoal', '#3C3A38']],
                ],
                'variants' => [
                    ['sku' => 'SOD-TOTE-NAT', 'price' => 3200, 'purchase_price' => 1750, 'stock' => 24, 'weight' => 480, 'options' => ['Color' => 'Natural'], 'default' => true],
                    ['sku' => 'SOD-TOTE-CHAR', 'price' => 3200, 'purchase_price' => 1750, 'stock' => 16, 'weight' => 480, 'options' => ['Color' => 'Charcoal']],
                ],
            ],
            [
                'name' => 'Sumi Pocket Notebooks',
                'slug' => 'sumi-pocket-notebooks',
                'category' => 'stationery',
                'brand' => 'sodai-studio',
                'image' => 'sumi-notebooks.webp',
                'days_ago' => 20,
                'featured' => false,
                'short_description' => 'A set of three linen-bound pocket notebooks with 80 gsm cream paper.',
                'description' => '<p>Three slim notebooks, one in forest green, one ochre and one cream, each with a sewn spine that opens flat and a ribbon marker.</p><p>80 gsm cream paper takes fountain ink without feathering. 48 pages each, 9 × 14 cm.</p><ul><li>Set of three, linen covers</li><li>Sewn binding, opens flat</li><li>Ruled, dotted or plain</li></ul>',
                'options' => [
                    'Style' => [['Ruled', null], ['Dotted', null], ['Plain', null]],
                ],
                'variants' => [
                    ['sku' => 'SOD-NOTE-RULED', 'price' => 950, 'purchase_price' => 480, 'stock' => 60, 'weight' => 210, 'options' => ['Style' => 'Ruled'], 'default' => true],
                    ['sku' => 'SOD-NOTE-DOTTED', 'price' => 950, 'purchase_price' => 480, 'stock' => 60, 'weight' => 210, 'options' => ['Style' => 'Dotted']],
                    ['sku' => 'SOD-NOTE-PLAIN', 'price' => 950, 'purchase_price' => 480, 'stock' => 45, 'weight' => 210, 'options' => ['Style' => 'Plain']],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function seedProduct(array $definition): void
    {
        $createdAt = $this->demoDate()->subDays($definition['days_ago'])->setTime(10, 0);

        $product = Product::updateOrCreate(
            ['slug' => $this->demoSlug($definition['slug'])],
            [
                'name' => $definition['name'],
                'short_description' => $definition['short_description'],
                'description' => $definition['description'],
                'category_id' => $this->categories[$definition['category']]->id,
                'brand_id' => $this->brands[$definition['brand']]->id,
                'is_active' => true,
                'is_featured' => $definition['featured'],
                'meta' => [
                    'title' => $definition['name'].' — Sodai',
                    'description' => $definition['short_description'],
                ],
            ],
        );

        $product->forceFill(['created_at' => $createdAt])->save();

        $imagePath = 'products/demo/'.$definition['image'];
        $defaultVariant = null;

        foreach ($definition['variants'] as $variantData) {
            $variant = ProductVariant::updateOrCreate(
                ['sku' => $this->demoSku($variantData['sku'])],
                [
                    'product_id' => $product->id,
                    'price' => $variantData['price'],
                    'purchase_price' => $variantData['purchase_price'],
                    'discount_type' => $variantData['discount'][0] ?? null,
                    'discount_value' => $variantData['discount'][1] ?? null,
                    'stock_quantity' => $variantData['stock'],
                    'low_stock_threshold' => 5,
                    'weight' => $variantData['weight'],
                    'weight_unit' => 'g',
                    'thumbnail' => $imagePath,
                    'is_default' => $variantData['default'] ?? false,
                    'is_active' => true,
                ],
            );

            $variant->optionValues()->sync($this->optionValueIds($definition['options'], $variantData['options']));

            $this->variants[$variantData['sku']] = $variant;

            if ($variant->is_default) {
                $defaultVariant = $variant;
            }
        }

        // One gallery image, owned by the default variant. Every variant points
        // its thumbnail at the same photo, so the PDP still has an image when
        // another colour/size is picked.
        if ($defaultVariant) {
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'product_variant_id' => $defaultVariant->id],
                ['image_path' => $imagePath, 'is_primary' => true, 'sort_order' => 0],
            );
        }

        $product->refreshPriceAndStockCache();
        $product->forceFill(['created_at' => $createdAt])->save();

        $this->products[$definition['slug']] = $product;
    }

    /**
     * Resolve (and create on first use) the option values a variant carries.
     *
     * @param  array<string, array<int, array{0: string, 1?: string|null}>>  $productOptions
     * @param  array<string, string>  $selected  option name => value
     * @return array<int, int>
     */
    private function optionValueIds(array $productOptions, array $selected): array
    {
        $ids = [];

        // Colors and sizes are shared vocabulary: a live shop's "Sage" swatch
        // is its own, so reuse it read-only and only add what is missing.
        foreach ($productOptions as $optionName => $values) {
            $option = ProductOption::firstOrCreate(
                ['slug' => Str::slug($optionName)],
                ['name' => $optionName, 'sort_order' => $optionName === 'Size' ? 2 : 1],
            );

            foreach ($values as $index => [$value, $swatch]) {
                $optionValue = ProductOptionValue::firstOrCreate(
                    ['product_option_id' => $option->id, 'slug' => Str::slug($value)],
                    ['value' => $value, 'swatch' => $swatch, 'sort_order' => $index + 1],
                );

                if (($selected[$optionName] ?? null) === $value) {
                    $ids[] = $optionValue->id;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function bannerDefinitions(): array
    {
        return [
            [
                'title' => 'The Quiet Morning Collection',
                'subtitle' => 'New season, made in small batches',
                'description' => 'Ceramics, linen and hinoki for the hour before the day starts.',
                'button_text' => 'Shop the collection',
                'button_url' => '/products',
                'image' => 'banners/demo/morning-table.webp',
                'text_position' => 'left',
                'text_color' => 'dark',
                'sort_order' => 1,
            ],
            [
                'title' => 'Linen, softened by hand',
                'subtitle' => 'Sakura throws and linen notebooks',
                'description' => 'Stone-washed linen that starts crisp and gets better with every wash.',
                'button_text' => 'Explore Home & Living',
                'button_url' => '/category/'.self::SLUG_PREFIX.'home-living',
                'image' => 'banners/demo/linen-stack.webp',
                'text_position' => 'left',
                'text_color' => 'dark',
                'sort_order' => 2,
            ],
        ];
    }

    private function seedBanners(): void
    {
        // Keyed by their demo image path, not their title: a live banner that
        // happens to share a headline must keep its own copy and layout.
        foreach (self::bannerDefinitions() as $banner) {
            Banner::updateOrCreate(
                ['position' => 'home_slider', 'image' => $banner['image']],
                $banner + [
                    'button_target' => '_self',
                    'mobile_image' => null,
                    'is_active' => true,
                    'starts_at' => null,
                    'expires_at' => null,
                ],
            );
        }
    }

    // ─────────────────────────────────────────────
    // TAGS, RELATIONS, CART & WISHLIST
    // ─────────────────────────────────────────────

    /**
     * Shared product tags. Like the Color/Size vocabulary these are not
     * demo-owned rows: an existing tag is reused and never renamed.
     */
    private function seedTags(): void
    {
        $tags = [
            'kizen-stoneware-mug' => ['Ceramics', 'Everyday'],
            'hinoki-serving-board' => ['Wood', 'Kitchen'],
            'aoyama-ceramic-teapot' => ['Tea', 'Ceramics'],
            'koyo-pour-over-kettle' => ['Coffee', 'Kitchen'],
            'matcha-whisk-set' => ['Tea', 'Everyday'],
            'sakura-linen-throw' => ['Linen', 'Home'],
            'nori-canvas-tote' => ['Everyday', 'Home'],
            'sumi-pocket-notebooks' => ['Paper', 'Stationery'],
        ];

        foreach ($tags as $slug => $names) {
            $this->products[$slug]->tags()->syncWithoutDetaching(
                array_map(fn (string $name): int => Tag::findOrCreateByName($name)->id, $names),
            );
        }
    }

    /**
     * Cross-sells, so product pages have a related-products rail without
     * anyone filling it in by hand.
     */
    private function seedRelatedProducts(): void
    {
        $relations = [
            'kizen-stoneware-mug' => ['aoyama-ceramic-teapot', 'matcha-whisk-set'],
            'aoyama-ceramic-teapot' => ['matcha-whisk-set', 'kizen-stoneware-mug'],
            'hinoki-serving-board' => ['kizen-stoneware-mug', 'sakura-linen-throw'],
            'koyo-pour-over-kettle' => ['matcha-whisk-set', 'aoyama-ceramic-teapot'],
            'sakura-linen-throw' => ['nori-canvas-tote', 'sumi-pocket-notebooks'],
        ];

        foreach ($relations as $slug => $related) {
            $this->products[$slug]->relatedProducts()->syncWithoutDetaching(
                array_map(fn (string $relatedSlug): int => $this->products[$relatedSlug]->id, $related),
            );
        }
    }

    /**
     * A saved cart for the demo customer, old enough for the abandoned-cart
     * reminder to find, so cart and reminder screens have real content.
     */
    private function seedCart(): void
    {
        $cart = Cart::updateOrCreate(
            ['user_id' => $this->customers['demo']->id],
            ['session_id' => null],
        );

        $cart->items()->delete();

        foreach ([['SOD-TOTE-NAT', 1], ['SOD-MATCHA-SET', 2]] as [$sku, $quantity]) {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $this->variants[$sku]->id,
                'quantity' => $quantity,
            ]);
        }

        $cart->forceFill([
            'created_at' => $this->demoDate()->subDays(5),
            'updated_at' => $this->demoDate()->subDays(3),
            'reminder_sent_at' => null,
        ])->save();
    }

    private function seedWishlist(): void
    {
        $wishlists = [
            ['customer' => 'demo', 'sku' => 'SOD-TEAPOT-600'],
            ['customer' => 'tanvir', 'sku' => 'SOD-KETTLE-BLACK'],
            ['customer' => 'sadia', 'sku' => 'SOD-THROW-OAT'],
        ];

        foreach ($wishlists as $entry) {
            $variant = $this->variants[$entry['sku']];

            Wishlist::updateOrCreate(
                [
                    'user_id' => $this->customers[$entry['customer']]->id,
                    'product_id' => $variant->product_id,
                ],
                ['product_variant_id' => $variant->id],
            );
        }
    }

    // ─────────────────────────────────────────────
    // CUSTOMERS & COUPON
    // ─────────────────────────────────────────────

    /**
     * @return array<int, array<string, string>>
     */
    public static function customerDefinitions(): array
    {
        return [
            ['key' => 'demo', 'name' => 'Nusrat Jahan', 'email' => self::CUSTOMER_EMAIL, 'phone' => '+880 1811 445566',
                'address' => 'House 12, Road 7, Dhanmondi', 'city' => 'Dhaka', 'state' => 'Dhaka', 'zip' => '1205'],
            ['key' => 'tanvir', 'name' => 'Tanvir Ahmed', 'email' => 'tanvir.ahmed@example.com', 'phone' => '+880 1712 334455',
                'address' => 'Flat 4B, 27 Kemal Ataturk Avenue, Banani', 'city' => 'Dhaka', 'state' => 'Dhaka', 'zip' => '1213'],
            ['key' => 'farhana', 'name' => 'Farhana Islam', 'email' => 'farhana.islam@example.com', 'phone' => '+880 1913 556677',
                'address' => '14 Nasirabad Housing Society', 'city' => 'Chattogram', 'state' => 'Chattogram', 'zip' => '4000'],
            ['key' => 'rifat', 'name' => 'Rifat Chowdhury', 'email' => 'rifat.chowdhury@example.com', 'phone' => '+880 1614 778899',
                'address' => 'House 3, Road 2, Shahjalal Uposhohor', 'city' => 'Sylhet', 'state' => 'Sylhet', 'zip' => '3100'],
            ['key' => 'sadia', 'name' => 'Sadia Karim', 'email' => 'sadia.karim@example.com', 'phone' => '+880 1515 990011',
                'address' => 'Apartment 6A, 92 Gulshan Avenue', 'city' => 'Dhaka', 'state' => 'Dhaka', 'zip' => '1212'],
            ['key' => 'imran', 'name' => 'Imran Hossain', 'email' => 'imran.hossain@example.com', 'phone' => '+880 1316 223344',
                'address' => 'House 21, Sector 4, Uttara', 'city' => 'Rajshahi', 'state' => 'Rajshahi', 'zip' => '6000'],
        ];
    }

    /**
     * Emails of every seeded shopper. `demo:clear` matches these exact
     * addresses, so it can never remove a real account that merely looks
     * like a demo one.
     *
     * @return array<int, string>
     */
    public static function customerEmails(): array
    {
        return array_column(self::customerDefinitions(), 'email');
    }

    private function seedCustomers(): void
    {
        foreach (self::customerDefinitions() as $data) {
            $isDemo = $data['key'] === 'demo';

            $customer = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => $isDemo ? $this->passwordHash() : Str::random(32),
                    'status' => 'active',
                    'email_verified_at' => $this->demoDate()->subDays(60),
                ],
            );

            $customer->forceFill([
                'email_verified_at' => $this->demoDate()->subDays(60),
                'created_at' => $this->demoDate()->subDays(75),
            ])->save();

            Address::updateOrCreate(
                ['user_id' => $customer->id, 'label' => 'Home'],
                [
                    'recipient_name' => $data['name'],
                    'recipient_phone' => $data['phone'],
                    'address_line_1' => $data['address'],
                    'address_line_2' => null,
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'zip_code' => $data['zip'],
                    'country' => 'Bangladesh',
                    'is_default' => true,
                ],
            );

            $customer->forceFill(['created_at' => $this->demoDate()->subDays(75)])->save();

            $this->customers[$data['key']] = $customer;
        }
    }

    private function seedCoupon(): void
    {
        $this->coupon = Coupon::updateOrCreate(
            ['code' => self::couponCode()],
            [
                'type' => 'percentage',
                'value' => 10,
                'minimum_order_amount' => 2000,
                'maximum_discount' => 700,
                'usage_limit' => 500,
                'usage_per_user' => 1,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => $this->demoDate()->subDays(30),
                'expires_at' => $this->demoDate()->addDays(60),
            ],
        );
    }

    // ─────────────────────────────────────────────
    // ORDERS
    // ─────────────────────────────────────────────

    /**
     * Sequence (the number after DEMO-ORD-), customer key, status, how long
     * ago it was placed, and the basket. Prices and totals are derived from
     * the seeded variants, never hardcoded.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function orderDefinitions(): array
    {
        return [
            ['sequence' => 100001, 'customer' => 'tanvir', 'status' => 'delivered', 'days_ago' => 45, 'items' => [['SOD-MUG-SAGE-350', 2], ['SOD-BOARD-M', 1]]],
            ['sequence' => 100002, 'customer' => 'farhana', 'status' => 'delivered', 'days_ago' => 38, 'items' => [['SOD-TEAPOT-600', 1]]],
            ['sequence' => 100003, 'customer' => 'rifat', 'status' => 'delivered', 'days_ago' => 31, 'items' => [['SOD-THROW-SAGE', 1], ['SOD-NOTE-DOTTED', 1]], 'coupon' => true],
            ['sequence' => 100004, 'customer' => 'sadia', 'status' => 'delivered', 'days_ago' => 24, 'items' => [['SOD-KETTLE-BLACK', 1]]],
            ['sequence' => 100005, 'customer' => 'tanvir', 'status' => 'delivered', 'days_ago' => 18, 'items' => [['SOD-MATCHA-SET', 1], ['SOD-MUG-CHAR-350', 1]]],
            ['sequence' => 100006, 'customer' => 'imran', 'status' => 'shipped', 'days_ago' => 8, 'items' => [['SOD-TOTE-NAT', 1]]],
            ['sequence' => 100007, 'customer' => 'farhana', 'status' => 'shipped', 'days_ago' => 6, 'items' => [['SOD-BOARD-L', 1], ['SOD-NOTE-RULED', 2]]],
            ['sequence' => 100008, 'customer' => 'rifat', 'status' => 'processing', 'days_ago' => 4, 'items' => [['SOD-TEAPOT-900', 1], ['SOD-MATCHA-SET', 1]]],
            ['sequence' => 100009, 'customer' => 'sadia', 'status' => 'processing', 'days_ago' => 3, 'items' => [['SOD-MUG-CHAR-450', 3]]],
            ['sequence' => 100010, 'customer' => 'imran', 'status' => 'confirmed', 'days_ago' => 2, 'items' => [['SOD-THROW-OAT', 1]]],
            ['sequence' => 100011, 'customer' => 'farhana', 'status' => 'pending', 'days_ago' => 1, 'items' => [['SOD-KETTLE-STEEL', 1], ['SOD-NOTE-PLAIN', 2]]],
            ['sequence' => 100012, 'customer' => 'demo', 'status' => 'pending', 'days_ago' => 0, 'items' => [['SOD-MUG-SAGE-450', 1]]],
            ['sequence' => 100013, 'customer' => 'sadia', 'status' => 'cancelled', 'days_ago' => 12, 'items' => [['SOD-TOTE-CHAR', 1]]],
            ['sequence' => 100014, 'customer' => 'rifat', 'status' => 'refunded', 'days_ago' => 20, 'items' => [['SOD-BOARD-S', 1]]],
        ];
    }

    private function seedOrders(): void
    {
        $pricing = app(PricingService::class);

        foreach (self::orderDefinitions() as $plan) {
            $customer = $this->customers[$plan['customer']];
            $placedAt = $this->demoDate()->subDays($plan['days_ago'])->setTime(11, 30);
            $number = self::orderNumber($plan['sequence']);

            $subtotal = 0.0;
            $lines = [];

            foreach ($plan['items'] as [$sku, $quantity]) {
                $variant = $this->variants[$sku];
                $lineTotal = (float) $variant->price * $quantity;
                $subtotal += $lineTotal;

                $lines[] = [
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'unit_price' => (float) $variant->price,
                    'total_price' => $lineTotal,
                ];
            }

            // Priced by the same coupon rule checkout applies, including the
            // coupon's maximum_discount cap.
            $discount = ! empty($plan['coupon'])
                ? $pricing->calculateCouponDiscount($this->coupon, $subtotal)
                : 0.0;

            $shipping = $this->shippingCharge($pricing, $customer, $subtotal);

            $order = Order::updateOrCreate(
                ['order_number' => $number],
                [
                    'user_id' => $customer->id,
                    'status' => $plan['status'],
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'shipping_charge' => $shipping,
                    'tax_amount' => 0,
                    'total_amount' => round($subtotal - $discount + $shipping, 2),
                    'coupon_code' => ! empty($plan['coupon']) ? $this->coupon->code : null,
                    'coupon_id' => ! empty($plan['coupon']) ? $this->coupon->id : null,
                    'payment_method' => 'cod',
                    'shipping_name' => $customer->name,
                    'shipping_email' => $customer->email,
                    'shipping_phone' => $customer->phone,
                    'shipping_address' => $customer->defaultAddress?->address_line_1 ?? 'House 12, Road 7, Dhanmondi',
                    'shipping_city' => $customer->defaultAddress?->city ?? 'Dhaka',
                    'shipping_state' => $customer->defaultAddress?->state ?? 'Dhaka',
                    'shipping_zip' => $customer->defaultAddress?->zip_code ?? '1205',
                    'shipping_country' => 'Bangladesh',
                    'tracking_number' => in_array($plan['status'], ['shipped', 'delivered'], true)
                        ? 'SPX-'.$plan['sequence'].'-BD'
                        : null,
                    'shipped_at' => in_array($plan['status'], ['shipped', 'delivered'], true) ? $placedAt->copy()->addDays(2) : null,
                    'delivered_at' => $plan['status'] === 'delivered' ? $placedAt->copy()->addDays(4) : null,
                    'cancelled_at' => $plan['status'] === 'cancelled' ? $placedAt->copy()->addDay() : null,
                    'cancel_reason' => $plan['status'] === 'cancelled' ? 'Customer changed their mind before dispatch.' : null,
                ],
            );

            $order->forceFill(['created_at' => $placedAt, 'updated_at' => $placedAt])->save();

            $order->items()->delete();

            foreach ($lines as $line) {
                /** @var ProductVariant $variant */
                $variant = $line['variant'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'product_sku' => $variant->sku,
                    'product_image' => $variant->thumbnail,
                    'variant_options' => $variant->options_label ?: null,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'total_price' => $line['total_price'],
                ]);
            }

            $this->seedStatusHistory($order, $placedAt);

            if ($plan['status'] === 'refunded') {
                $this->seedRefund($order);
            }
        }

        $this->coupon->update([
            'used_count' => Order::where('coupon_id', $this->coupon->id)->count(),
        ]);
    }

    /**
     * Demo order numbers live in their own namespace, "DEMO-ORD-100001".
     * PricingService::generateUniqueOrderNumber() emits
     * `<order prefix><six digits>` from the newest matching number, so these
     * neither collide with real orders nor push the real sequence forward.
     */
    public static function orderNumber(int $sequence): string
    {
        return self::ID_PREFIX.'ORD-'.$sequence;
    }

    /**
     * Catalog lookups key on the namespaced identifier, so a real row with
     * the bare slug can never be adopted and rewritten by a demo run.
     */
    public static function demoSlug(string $slug): string
    {
        return self::SLUG_PREFIX.$slug;
    }

    /**
     * Same idea for variant SKUs: "SOD-NOTE-RULED" is a number a real store
     * could well have issued, so the demo variant is "DEMO-SOD-NOTE-RULED".
     */
    public static function demoSku(string $sku): string
    {
        return self::ID_PREFIX.$sku;
    }

    /**
     * Every identifier this seeder owns. `demo:clear` removes exactly these
     * rows, so a real record is never matched by a shared prefix alone.
     *
     * @return array<int, string>
     */
    public static function categorySlugs(): array
    {
        return self::demoSlugs(self::categoryDefinitions());
    }

    /** @return array<int, string> */
    public static function brandSlugs(): array
    {
        return self::demoSlugs(self::brandDefinitions());
    }

    /** @return array<int, string> */
    public static function productSlugs(): array
    {
        return self::demoSlugs(self::productDefinitions());
    }

    /** @return array<int, string> */
    public static function variantSkus(): array
    {
        $skus = [];

        foreach (self::productDefinitions() as $definition) {
            foreach ($definition['variants'] as $variant) {
                $skus[] = self::demoSku($variant['sku']);
            }
        }

        return $skus;
    }

    /** @return array<int, string> */
    public static function bannerImages(): array
    {
        return array_column(self::bannerDefinitions(), 'image');
    }

    /** @return array<int, string> */
    public static function orderNumbers(): array
    {
        return array_map(
            fn (array $plan): string => self::orderNumber((int) $plan['sequence']),
            self::orderDefinitions(),
        );
    }

    public static function couponCode(): string
    {
        return self::ID_PREFIX.'WELCOME10';
    }

    /**
     * @param  array<int, array<string, mixed>>  $definitions
     * @return array<int, string>
     */
    private static function demoSlugs(array $definitions): array
    {
        return array_map(fn (array $definition): string => self::demoSlug($definition['slug']), $definitions);
    }

    /**
     * Priced with the store's effective shipping rules — free-shipping
     * threshold, in-area and out-of-area charge — so the seeded history agrees
     * with Settings → Shipping. On a fresh database those are the demo defaults
     * written just above; beside a live shop they are the charges it already
     * had, because seedMissingSettings() left them alone.
     */
    private function shippingCharge(PricingService $pricing, User $customer, float $subtotal): float
    {
        return $pricing->resolveShippingCharge($customer->defaultAddress?->city, $subtotal);
    }

    private function seedStatusHistory(Order $order, $placedAt): void
    {
        $paths = [
            'pending' => ['pending'],
            'confirmed' => ['pending', 'confirmed'],
            'processing' => ['pending', 'confirmed', 'processing'],
            'shipped' => ['pending', 'confirmed', 'processing', 'shipped'],
            'delivered' => ['pending', 'confirmed', 'processing', 'shipped', 'delivered'],
            'cancelled' => ['pending', 'cancelled'],
            'refunded' => ['pending', 'confirmed', 'shipped', 'refunded'],
        ];

        $notes = [
            'pending' => 'Order placed by customer.',
            'confirmed' => 'Order confirmed and stock reserved.',
            'processing' => 'Packing in progress at the Dhaka studio.',
            'shipped' => 'Handed to the courier.',
            'delivered' => 'Delivered and signed for.',
            'cancelled' => 'Cancelled before dispatch; stock released.',
            'refunded' => 'Refund approved; stock returned to inventory.',
        ];

        OrderStatusHistory::where('order_id', $order->id)->delete();

        $previous = null;
        $step = 0;

        foreach ($paths[$order->status] as $status) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $previous,
                'to_status' => $status,
                'changed_by' => $previous === null ? null : $this->admin?->id,
                'note' => $notes[$status],
            ])->forceFill([
                'created_at' => $placedAt->copy()->addHours($step * 26),
                'updated_at' => $placedAt->copy()->addHours($step * 26),
            ])->save();

            $previous = $status;
            $step++;
        }
    }

    private function seedRefund(Order $order): void
    {
        // Found by the demo order, not by its number: a refund that belongs to
        // any other order is real data and must never be rewritten. The DEMO-
        // prefix additionally keeps the number out of the generator's
        // `<invoice prefix>-REF-<digits>` namespace, so a fresh refund issued
        // by the admin UI can never reuse or overwrite it.
        Refund::updateOrCreate(
            ['order_id' => $order->id],
            [
                'refund_number' => self::ID_PREFIX.'REF-000001',
                'user_id' => $order->user_id,
                'amount' => $order->total_amount,
                'reason' => 'Board arrived with a split along the grain.',
                'status' => 'approved',
                'admin_note' => 'Replacement declined at customer request; full refund issued.',
                'processed_by' => $this->admin?->id,
                'processed_at' => $order->created_at->copy()->addDays(5),
            ],
        );
    }

    // ─────────────────────────────────────────────
    // REVIEWS
    // ─────────────────────────────────────────────

    private function seedReviews(): void
    {
        $reviews = [
            ['sku' => 'SOD-MUG-SAGE-350', 'sequence' => 100001, 'customer' => 'tanvir', 'rating' => 5, 'status' => 'approved',
                'comment' => 'The glaze is even better in person and it holds heat for ages. Bought a second one for the office.'],
            ['sku' => 'SOD-BOARD-M', 'sequence' => 100001, 'customer' => 'tanvir', 'rating' => 4, 'status' => 'pending',
                'comment' => 'Lovely grain and very light. Slightly smaller than I pictured, but it fits our counter.'],
            ['sku' => 'SOD-TEAPOT-600', 'sequence' => 100002, 'customer' => 'farhana', 'rating' => 5, 'status' => 'approved',
                'comment' => 'No drip at all, which is why I bought it. Packaging was careful too.'],
            ['sku' => 'SOD-THROW-SAGE', 'sequence' => 100003, 'customer' => 'rifat', 'rating' => 4, 'status' => 'approved',
                'comment' => 'Soft after the first wash and the colour is exactly as shown. A little lighter than expected.'],
            ['sku' => 'SOD-NOTE-DOTTED', 'sequence' => 100003, 'customer' => 'rifat', 'rating' => 5, 'status' => 'approved',
                'comment' => 'Fountain pen ink does not bleed through. Already ordered a second set.'],
            ['sku' => 'SOD-KETTLE-BLACK', 'sequence' => 100004, 'customer' => 'sadia', 'rating' => 4, 'status' => 'approved',
                'comment' => 'Excellent control on the pour. The handle gets slightly warm if you leave it on the hob too long.'],
            ['sku' => 'SOD-MATCHA-SET', 'sequence' => 100005, 'customer' => 'tanvir', 'rating' => 5, 'status' => 'approved',
                'comment' => 'A proper starter set. The whisk has held its shape after two months of daily use.'],
            ['sku' => 'SOD-MUG-CHAR-350', 'sequence' => 100005, 'customer' => 'tanvir', 'rating' => 5, 'status' => 'approved',
                'comment' => 'Charcoal is a deep matte black. Feels sturdy without being heavy.'],
        ];

        foreach ($reviews as $review) {
            $variant = $this->variants[$review['sku']];
            $order = Order::where('order_number', self::orderNumber($review['sequence']))->first();

            if (! $order) {
                continue;
            }

            $reviewedAt = $order->created_at->copy()->addDays(6);

            $row = Review::updateOrCreate(
                [
                    'product_id' => $variant->product_id,
                    'user_id' => $this->customers[$review['customer']]->id,
                    'order_id' => $order->id,
                ],
                [
                    'rating' => $review['rating'],
                    'comment' => $review['comment'],
                    'status' => $review['status'],
                ],
            );

            $row->forceFill(['created_at' => $reviewedAt, 'updated_at' => $reviewedAt])->save();
        }
    }

    // ─────────────────────────────────────────────
    // AGGREGATES
    // ─────────────────────────────────────────────

    /**
     * Rebuild the denormalized counters the storefront sorts and badges on:
     * approved-review averages, and units sold across live orders.
     */
    private function refreshCatalogStats(): void
    {
        foreach ($this->products as $product) {
            $approved = Review::where('product_id', $product->id)->approved();

            $sold = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', fn ($q) => $q->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']))
                ->sum('quantity');

            $product->forceFill([
                'average_rating' => round($approved->avg('rating') ?? 0, 2),
                'review_count' => $approved->count(),
                'total_sales' => (int) $sold,
            ])->save();
        }
    }
}
