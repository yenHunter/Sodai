<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Admin;
use App\Models\Banner;
use App\Models\Brand;
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
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
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
 * Re-running it is safe: every row is found by a stable slug, SKU, order
 * number or email and updated in place. Images live in public/images/demo
 * and are copied into storage/app/public on first run, so a fresh checkout
 * only needs `php artisan storage:link`.
 */
class DemoCatalogSeeder extends Seeder
{
    public const ADMIN_EMAIL = 'demo-admin@example.com';

    public const CUSTOMER_EMAIL = 'demo-customer@example.com';

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

    private ?Admin $admin = null;

    private ?Coupon $coupon = null;

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('DemoCatalogSeeder skipped: demo accounts are disabled in production.');

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
        $this->seedBanners();
        $this->seedCustomers();
        $this->seedCoupon();

        $this->seedOrders();
        $this->seedReviews();
        $this->refreshCatalogStats();

        $this->command?->info('Demo catalog seeded: '
            .Product::count().' products, '
            .ProductVariant::count().' variants, '
            .Order::count().' orders, '
            .Review::count().' reviews.');
        $this->command?->info('Admin login: '.self::ADMIN_EMAIL.' / '.$this->password());
        $this->command?->info('Customer login: '.self::CUSTOMER_EMAIL.' / '.$this->password());
    }

    private function password(): string
    {
        return (string) env('SEED_DEMO_PASSWORD', 'Demo#12345');
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
     * Store identity and the checkout/shipping rules the seeded orders were
     * priced against, so the storefront and the order history agree.
     */
    private function seedSettings(): void
    {
        Setting::setMany('company', [
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

        Setting::setMany('shipping', [
            'operation_areas' => json_encode(['Dhaka']),
            'inside_area_charge' => '60',
            'outside_area_charge' => '120',
            'enable_free_shipping' => '1',
            'free_shipping_threshold' => '7500',
        ], [
            'operation_areas' => 'json',
            'enable_free_shipping' => 'boolean',
        ]);

        Setting::setMany('order', [
            'allow_guest_checkout' => '1',
            'order_number_prefix' => 'ORD-',
        ], [
            'allow_guest_checkout' => 'boolean',
        ]);
    }

    private function seedAdmin(): void
    {
        $this->admin = Admin::updateOrCreate(
            ['email' => self::ADMIN_EMAIL],
            [
                'name' => 'Mahmudul Hasan',
                'phone' => '+880 1711 223344',
                'password' => $this->password(),
                'is_active' => true,
            ],
        );

        $this->admin->syncRoles(['super-admin']);
    }

    // ─────────────────────────────────────────────
    // CATALOG
    // ─────────────────────────────────────────────

    private function seedCategories(): void
    {
        // Flat on purpose: the home page counts a category's own products
        // (not its children's), so leaf-only categories keep those counts
        // truthful. Every product below belongs to exactly one of these.
        $categories = [
            ['slug' => 'kitchen-dining', 'name' => 'Kitchen & Dining', 'image' => 'kitchen-dining.webp', 'sort' => 1,
                'description' => 'Everyday ceramics, cypress boards and tableware made to be used, not saved for guests.'],
            ['slug' => 'tea-coffee', 'name' => 'Tea & Coffee', 'image' => 'tea-coffee.webp', 'sort' => 2,
                'description' => 'Teapots, kettles and matcha tools for a slower cup, chosen for how they pour and how they feel.'],
            ['slug' => 'home-living', 'name' => 'Home & Living', 'image' => 'home-living.webp', 'sort' => 3,
                'description' => 'Washed linen, canvas and quiet textiles that soften a room without asking for attention.'],
            ['slug' => 'stationery', 'name' => 'Stationery', 'image' => 'stationery.webp', 'sort' => 4,
                'description' => 'Linen-bound notebooks and desk pieces for lists, sketches and plans worth keeping.'],
        ];

        foreach ($categories as $category) {
            $this->categories[$category['slug']] = Category::updateOrCreate(
                ['slug' => $category['slug']],
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

    private function seedBrands(): void
    {
        $brands = [
            ['slug' => 'sodai-studio', 'name' => 'Sodai Studio', 'description' => 'Our own line, designed in Dhaka and made in small batches.'],
            ['slug' => 'kizen-ceramics', 'name' => 'Kizen Ceramics', 'description' => 'A family pottery studio working in speckled stoneware.'],
            ['slug' => 'aoyama', 'name' => 'Aoyama', 'description' => 'Tea ware and bamboo tools from a workshop in Shiga, Japan.'],
        ];

        foreach ($brands as $index => $brand) {
            $this->brands[$brand['slug']] = Brand::updateOrCreate(
                ['slug' => $brand['slug']],
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
        foreach ($this->productDefinitions() as $definition) {
            $this->seedProduct($definition);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function productDefinitions(): array
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
        $createdAt = now()->subDays($definition['days_ago'])->setTime(10, 0);

        $product = Product::updateOrCreate(
            ['slug' => $definition['slug']],
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
                ['sku' => $variantData['sku']],
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

        foreach ($productOptions as $optionName => $values) {
            $option = ProductOption::updateOrCreate(
                ['slug' => Str::slug($optionName)],
                ['name' => $optionName, 'sort_order' => $optionName === 'Size' ? 2 : 1],
            );

            foreach ($values as $index => [$value, $swatch]) {
                $optionValue = ProductOptionValue::updateOrCreate(
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

    private function seedBanners(): void
    {
        $banners = [
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
                'button_url' => '/category/home-living',
                'image' => 'banners/demo/linen-stack.webp',
                'text_position' => 'left',
                'text_color' => 'dark',
                'sort_order' => 2,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(
                ['title' => $banner['title'], 'position' => 'home_slider'],
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
    // CUSTOMERS & COUPON
    // ─────────────────────────────────────────────

    private function seedCustomers(): void
    {
        $customers = [
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

        foreach ($customers as $data) {
            $isDemo = $data['key'] === 'demo';

            $customer = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => $isDemo ? $this->password() : Str::random(32),
                    'status' => 'active',
                    'email_verified_at' => now()->subDays(60),
                ],
            );

            $customer->forceFill([
                'email_verified_at' => now()->subDays(60),
                'created_at' => now()->subDays(75),
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

            $customer->forceFill(['created_at' => now()->subDays(75)])->save();

            $this->customers[$data['key']] = $customer;
        }
    }

    private function seedCoupon(): void
    {
        $this->coupon = Coupon::updateOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percentage',
                'value' => 10,
                'minimum_order_amount' => 2000,
                'maximum_discount' => 700,
                'usage_limit' => 500,
                'usage_per_user' => 1,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => now()->subDays(30),
                'expires_at' => now()->addDays(60),
            ],
        );
    }

    // ─────────────────────────────────────────────
    // ORDERS
    // ─────────────────────────────────────────────

    private function seedOrders(): void
    {
        // Customer key, status, how long ago it was placed, and the basket.
        // Prices/totals are derived from the seeded variants, never hardcoded.
        $orders = [
            ['number' => 'ORD-100001', 'customer' => 'tanvir', 'status' => 'delivered', 'days_ago' => 45, 'items' => [['SOD-MUG-SAGE-350', 2], ['SOD-BOARD-M', 1]]],
            ['number' => 'ORD-100002', 'customer' => 'farhana', 'status' => 'delivered', 'days_ago' => 38, 'items' => [['SOD-TEAPOT-600', 1]]],
            ['number' => 'ORD-100003', 'customer' => 'rifat', 'status' => 'delivered', 'days_ago' => 31, 'items' => [['SOD-THROW-SAGE', 1], ['SOD-NOTE-DOTTED', 1]], 'coupon' => true],
            ['number' => 'ORD-100004', 'customer' => 'sadia', 'status' => 'delivered', 'days_ago' => 24, 'items' => [['SOD-KETTLE-BLACK', 1]]],
            ['number' => 'ORD-100005', 'customer' => 'tanvir', 'status' => 'delivered', 'days_ago' => 18, 'items' => [['SOD-MATCHA-SET', 1], ['SOD-MUG-CHAR-350', 1]]],
            ['number' => 'ORD-100006', 'customer' => 'imran', 'status' => 'shipped', 'days_ago' => 8, 'items' => [['SOD-TOTE-NAT', 1]]],
            ['number' => 'ORD-100007', 'customer' => 'farhana', 'status' => 'shipped', 'days_ago' => 6, 'items' => [['SOD-BOARD-L', 1], ['SOD-NOTE-RULED', 2]]],
            ['number' => 'ORD-100008', 'customer' => 'rifat', 'status' => 'processing', 'days_ago' => 4, 'items' => [['SOD-TEAPOT-900', 1], ['SOD-MATCHA-SET', 1]]],
            ['number' => 'ORD-100009', 'customer' => 'sadia', 'status' => 'processing', 'days_ago' => 3, 'items' => [['SOD-MUG-CHAR-450', 3]]],
            ['number' => 'ORD-100010', 'customer' => 'imran', 'status' => 'confirmed', 'days_ago' => 2, 'items' => [['SOD-THROW-OAT', 1]]],
            ['number' => 'ORD-100011', 'customer' => 'farhana', 'status' => 'pending', 'days_ago' => 1, 'items' => [['SOD-KETTLE-STEEL', 1], ['SOD-NOTE-PLAIN', 2]]],
            ['number' => 'ORD-100012', 'customer' => 'demo', 'status' => 'pending', 'days_ago' => 0, 'items' => [['SOD-MUG-SAGE-450', 1]]],
            ['number' => 'ORD-100013', 'customer' => 'sadia', 'status' => 'cancelled', 'days_ago' => 12, 'items' => [['SOD-TOTE-CHAR', 1]]],
            ['number' => 'ORD-100014', 'customer' => 'rifat', 'status' => 'refunded', 'days_ago' => 20, 'items' => [['SOD-BOARD-S', 1]]],
        ];

        foreach ($orders as $plan) {
            $customer = $this->customers[$plan['customer']];
            $placedAt = now()->subDays($plan['days_ago'])->setTime(11, 30);

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

            $discount = 0.0;
            if (! empty($plan['coupon'])) {
                $discount = round($subtotal * 0.10, 2);
            }

            $shipping = $this->shippingCharge($customer, $subtotal);

            $order = Order::updateOrCreate(
                ['order_number' => $plan['number']],
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
                        ? 'SPX-'.substr(str_replace('ORD-', '', $plan['number']), -4).'-BD'
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

    private function shippingCharge(User $customer, float $subtotal): float
    {
        if ($subtotal >= 7500) {
            return 0.0;
        }

        return ($customer->defaultAddress?->city === 'Dhaka') ? 60.0 : 120.0;
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
        Refund::updateOrCreate(
            ['refund_number' => 'INV-REF-000001'],
            [
                'order_id' => $order->id,
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
            ['sku' => 'SOD-MUG-SAGE-350', 'order' => 'ORD-100001', 'customer' => 'tanvir', 'rating' => 5, 'status' => 'approved',
                'comment' => 'The glaze is even better in person and it holds heat for ages. Bought a second one for the office.'],
            ['sku' => 'SOD-BOARD-M', 'order' => 'ORD-100001', 'customer' => 'tanvir', 'rating' => 4, 'status' => 'pending',
                'comment' => 'Lovely grain and very light. Slightly smaller than I pictured, but it fits our counter.'],
            ['sku' => 'SOD-TEAPOT-600', 'order' => 'ORD-100002', 'customer' => 'farhana', 'rating' => 5, 'status' => 'approved',
                'comment' => 'No drip at all, which is why I bought it. Packaging was careful too.'],
            ['sku' => 'SOD-THROW-SAGE', 'order' => 'ORD-100003', 'customer' => 'rifat', 'rating' => 4, 'status' => 'approved',
                'comment' => 'Soft after the first wash and the colour is exactly as shown. A little lighter than expected.'],
            ['sku' => 'SOD-NOTE-DOTTED', 'order' => 'ORD-100003', 'customer' => 'rifat', 'rating' => 5, 'status' => 'approved',
                'comment' => 'Fountain pen ink does not bleed through. Already ordered a second set.'],
            ['sku' => 'SOD-KETTLE-BLACK', 'order' => 'ORD-100004', 'customer' => 'sadia', 'rating' => 4, 'status' => 'approved',
                'comment' => 'Excellent control on the pour. The handle gets slightly warm if you leave it on the hob too long.'],
            ['sku' => 'SOD-MATCHA-SET', 'order' => 'ORD-100005', 'customer' => 'tanvir', 'rating' => 5, 'status' => 'approved',
                'comment' => 'A proper starter set. The whisk has held its shape after two months of daily use.'],
            ['sku' => 'SOD-MUG-CHAR-350', 'order' => 'ORD-100005', 'customer' => 'tanvir', 'rating' => 5, 'status' => 'approved',
                'comment' => 'Charcoal is a deep matte black. Feels sturdy without being heavy.'],
        ];

        foreach ($reviews as $review) {
            $variant = $this->variants[$review['sku']];
            $order = Order::where('order_number', $review['order'])->first();

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
