<?php

namespace App\Services\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Product catalog CSV import/export.
 *
 * The CSV format is one row per variant. Rows that share a product name
 * belong to the same product (product-level columns are read from the
 * first row of the group), and variant rows are matched by SKU:
 *
 *   - SKU already on the product  -> variant updated
 *   - SKU on another product      -> row rejected (SKUs are unique)
 *   - blank SKU                   -> variant created with a generated SKU
 *
 * This means an export can be edited and imported back as a full snapshot
 * of product + variant fields without duplicating anything.
 */
class ProductCsvService
{
    public const COLUMNS = [
        'product_name',
        'category',
        'brand',
        'short_description',
        'description',
        'is_active',
        'is_featured',
        'tags',
        'variant_sku',
        'price',
        'purchase_price',
        'discount_type',
        'discount_value',
        'stock_quantity',
        'low_stock_threshold',
        'weight',
        'weight_unit',
        'variant_active',
        'is_default',
        'options',
    ];

    /** Hard cap so one upload cannot exhaust memory or a request timeout. */
    private const MAX_ROWS = 5000;

    public function __construct(private ProductService $productService) {}

    // ─────────────────────────────────────────────
    // EXPORT
    // ─────────────────────────────────────────────

    /**
     * Writes the full catalog (products + variants) to an open stream.
     * Chunked so a large catalog does not load entirely into memory.
     */
    public function writeExport($handle): void
    {
        $this->writeHeader($handle);

        Product::query()
            ->with(['category.parent', 'brand', 'tags', 'variants.optionValues.option'])
            ->orderBy('products.id')
            ->chunk(200, function ($products) use ($handle) {
                foreach ($products as $product) {
                    $variants = $product->variants
                        ->sortBy([['is_default', 'desc'], ['id', 'asc']]);

                    foreach ($variants as $variant) {
                        fputcsv($handle, $this->exportRow($product, $variant), ',', '"', '');
                    }
                }
            });
    }

    /** Downloadable sample that matches the export format exactly. */
    public function writeTemplate($handle): void
    {
        $this->writeHeader($handle);

        fputcsv($handle, [
            'Classic Cotton T-Shirt',
            'Apparel > T-Shirts',
            'Acme',
            'Soft combed cotton tee.',
            '<p>Everyday crew-neck tee in a relaxed fit.</p>',
            '1',
            '0',
            'summer, cotton',
            'TSHIRT-BLK-M',
            '19.99',
            '9.50',
            '',
            '',
            '25',
            '5',
            '0.20',
            'kg',
            '1',
            '1',
            'Color: Black | Size: M',
        ], ',', '"', '');
    }

    // ─────────────────────────────────────────────
    // IMPORT
    // ─────────────────────────────────────────────

    /**
     * @return array{
     *     stats: array{products_created:int, products_updated:int, variants_created:int, variants_updated:int, rows:int},
     *     errors: array<int, array{row:int, message:string}>
     */
    public function import(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            throw new \RuntimeException('The uploaded file could not be read.');
        }

        try {
            $header = $this->readHeader($handle);

            $required = ['product_name', 'category', 'price'];
            foreach ($required as $column) {
                if (! array_key_exists($column, $header)) {
                    throw new \RuntimeException(
                        "The CSV is missing the \"{$column}\" column. Download the template to see the expected format."
                    );
                }
            }

            $stats = [
                'products_created' => 0,
                'products_updated' => 0,
                'variants_created' => 0,
                'variants_updated' => 0,
                'rows' => 0,
            ];
            $errors = [];
            $groups = [];
            $rowNumber = 1;

            while (($row = fgetcsv($handle, null, ',', '"', '')) !== false) {
                $rowNumber++;

                if ($this->isBlankRow($row)) {
                    continue;
                }

                if (++$stats['rows'] > self::MAX_ROWS) {
                    throw new \RuntimeException('The CSV has more than '.self::MAX_ROWS.' data rows. Split it into smaller files.');
                }

                $assoc = $this->associate($header, $row);
                $name = trim($assoc['product_name'] ?? '');

                if ($name === '') {
                    $errors[] = ['row' => $rowNumber, 'message' => 'product_name is required.'];

                    continue;
                }

                $key = mb_strtolower($name);

                try {
                    $variant = $this->normalizeVariantRow($assoc);
                } catch (\InvalidArgumentException $e) {
                    $errors[] = ['row' => $rowNumber, 'message' => $e->getMessage()];
                    $groups[$key]['invalid'] = true;
                    $groups[$key]['name'] = $name;
                    $groups[$key]['rows'] ??= [];

                    continue;
                }

                $groups[$key]['name'] = $name;
                $groups[$key]['invalid'] = $groups[$key]['invalid'] ?? false;
                $groups[$key]['rows'][] = [
                    'row' => $rowNumber,
                    'product' => $assoc,
                    'variant' => $variant,
                ];
            }

            if ($stats['rows'] === 0) {
                throw new \RuntimeException('The CSV has no data rows.');
            }
        } finally {
            fclose($handle);
        }

        foreach ($groups as $group) {
            // A group with any invalid row is skipped whole: a half-imported
            // product is harder to repair than one that was never touched.
            if (($group['invalid'] ?? false) || empty($group['rows'])) {
                continue;
            }

            $before = $stats;

            try {
                DB::transaction(function () use ($group, &$stats) {
                    $this->importGroup($group, $stats);
                });
            } catch (\Throwable $e) {
                $stats = $before;
                $errors[] = ['row' => $group['rows'][0]['row'], 'message' => $e->getMessage()];

                Log::warning('Product CSV import skipped a product.', [
                    'product' => $group['name'],
                    'exception' => $e,
                ]);
            }
        }

        Log::info('Product CSV import finished.', $stats + ['errors' => count($errors)]);

        return ['stats' => $stats, 'errors' => $errors];
    }

    /**
     * @param  array{name:string, rows:array<int, array{row:int, product:array, variant:array}>}  $group
     */
    private function importGroup(array $group, array &$stats): void
    {
        $rows = $group['rows'];
        $name = $group['name'];
        $productFields = $rows[0]['product'];

        $category = $this->resolveCategory($productFields['category'] ?? '');

        if (! $category) {
            throw new \RuntimeException(
                'Category "'.(trim($productFields['category'] ?? '') ?: '—').'" was not found. Create it before importing.'
            );
        }

        $brand = $this->resolveBrand($productFields['brand'] ?? null);

        $product = Product::where('slug', Str::slug($name))->first()
            ?? Product::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        $variants = [];

        foreach ($rows as $entry) {
            $variant = $entry['variant'];
            $sku = $variant['sku'] ?? '';

            if ($sku !== '') {
                $existing = ProductVariant::withTrashed()->where('sku', $sku)->first();

                if ($existing && $existing->trashed()) {
                    throw new \RuntimeException("SKU \"{$sku}\" belongs to a deleted variant and cannot be reused.");
                }

                if ($existing && (! $product || $existing->product_id !== $product->id)) {
                    throw new \RuntimeException("SKU \"{$sku}\" already belongs to another product.");
                }

                if ($existing) {
                    $variant['id'] = $existing->id;
                }
            }

            $variants[] = $variant;
        }

        $data = [
            'name' => $name,
            'category_id' => $category->id,
            'brand_id' => $brand?->id,
            'short_description' => $this->nullableString($productFields['short_description'] ?? ''),
            'description' => $this->nullableString($productFields['description'] ?? ''),
            'is_active' => $this->parseBool($productFields['is_active'] ?? '', true),
            'is_featured' => $this->parseBool($productFields['is_featured'] ?? '', false),
            'tags' => $this->parseTags($productFields['tags'] ?? ''),
            'variants' => $variants,
        ];

        if ($product) {
            $this->productService->update($product, $data);
            $stats['products_updated']++;
        } else {
            $this->productService->store($data);
            $stats['products_created']++;
        }

        foreach ($variants as $variant) {
            if (! empty($variant['id'])) {
                $stats['variants_updated']++;
            } else {
                $stats['variants_created']++;
            }
        }
    }

    // ─────────────────────────────────────────────
    // PARSING
    // ─────────────────────────────────────────────

    /**
     * @return array<string, int> normalized column name => index
     */
    private function readHeader($handle): array
    {
        $raw = fgetcsv($handle, null, ',', '"', '');

        if ($raw === false || $raw === [null]) {
            throw new \RuntimeException('The CSV file is empty.');
        }

        $header = [];

        foreach ($raw as $index => $column) {
            $normalized = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '_', (string) $column), '_'));
            // Strip the UTF-8 BOM Excel sometimes prepends to the first cell.
            $normalized = ltrim($normalized, "\xEF\xBB\xBF");

            if ($normalized !== '') {
                $header[$normalized] = $index;
            }
        }

        return $header;
    }

    /**
     * @param  array<string, int>  $header
     * @return array<string, string>
     */
    private function associate(array $header, array $row): array
    {
        $values = [];

        foreach (self::COLUMNS as $column) {
            $values[$column] = isset($header[$column])
                ? trim((string) ($row[$header[$column]] ?? ''))
                : '';
        }

        return $values;
    }

    /**
     * @param  array<string, string>  $row
     * @return array<string, mixed>
     */
    private function normalizeVariantRow(array $row): array
    {
        $price = $this->parseDecimal($row['price'] ?? '');

        if ($price === null || $price < 0) {
            throw new \InvalidArgumentException('price must be a number of 0 or more.');
        }

        $stock = $row['stock_quantity'] === '' ? 0 : $this->parseInt($row['stock_quantity']);

        if ($stock === null || $stock < 0) {
            throw new \InvalidArgumentException('stock_quantity must be a whole number of 0 or more.');
        }

        $threshold = $row['low_stock_threshold'] === '' ? 5 : $this->parseInt($row['low_stock_threshold']);

        if ($threshold === null || $threshold < 0) {
            throw new \InvalidArgumentException('low_stock_threshold must be a whole number of 0 or more.');
        }

        $discountType = strtolower($row['discount_type'] ?? '');

        if ($discountType !== '' && ! in_array($discountType, ['percentage', 'fixed'], true)) {
            throw new \InvalidArgumentException('discount_type must be "percentage", "fixed", or blank.');
        }

        $discountValue = null;
        if (($row['discount_value'] ?? '') !== '') {
            $discountValue = $this->parseDecimal($row['discount_value']);

            if ($discountValue === null || $discountValue < 0) {
                throw new \InvalidArgumentException('discount_value must be a number of 0 or more.');
            }
        }

        $purchasePrice = null;
        if (($row['purchase_price'] ?? '') !== '') {
            $purchasePrice = $this->parseDecimal($row['purchase_price']);

            if ($purchasePrice === null || $purchasePrice < 0) {
                throw new \InvalidArgumentException('purchase_price must be a number of 0 or more.');
            }
        }

        $weight = null;
        if (($row['weight'] ?? '') !== '') {
            $weight = $this->parseDecimal($row['weight']);

            if ($weight === null || $weight < 0) {
                throw new \InvalidArgumentException('weight must be a number of 0 or more.');
            }
        }

        $variant = [
            'price' => $price,
            'purchase_price' => $purchasePrice,
            'discount_type' => $discountType ?: null,
            'discount_value' => $discountValue,
            'stock_quantity' => $stock,
            'low_stock_threshold' => $threshold,
            'weight' => $weight,
            'weight_unit' => $this->nullableString($row['weight_unit'] ?? ''),
            'is_active' => $this->parseBool($row['variant_active'] ?? '', true),
            'is_default' => $this->parseBool($row['is_default'] ?? '', false),
            'option_values' => $this->parseOptions($row['options'] ?? ''),
        ];

        if (($row['variant_sku'] ?? '') !== '') {
            $variant['sku'] = $row['variant_sku'];
        }

        return $variant;
    }

    /**
     * "Color: Red | Size: M" -> [['option' => 'Color', 'value' => 'Red'], ...]
     *
     * @return array<int, array{option:string, value:string}>
     */
    private function parseOptions(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        return collect(preg_split('/\s*\|\s*/', $value))
            ->filter()
            ->map(function ($pair) {
                $parts = explode(':', $pair, 2);

                if (count($parts) !== 2) {
                    return null;
                }

                $option = trim($parts[0]);
                $optionValue = trim($parts[1]);

                return ($option === '' || $optionValue === '')
                    ? null
                    : ['option' => $option, 'value' => $optionValue];
            })
            ->filter()
            ->values()
            ->all();
    }

    // ─────────────────────────────────────────────
    // LOOKUPS
    // ─────────────────────────────────────────────

    /**
     * Accepts an id, a name/slug, or an exported "Parent > Child" full name.
     */
    private function resolveCategory(string $value): ?Category
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (ctype_digit($value)) {
            return Category::find((int) $value);
        }

        if (str_contains($value, '>')) {
            [$parentName, $childName] = array_map('trim', explode('>', $value, 2));

            $parent = Category::whereNull('parent_id')
                ->where(function ($query) use ($parentName) {
                    $query->where('name', $parentName)->orWhere('slug', Str::slug($parentName));
                })
                ->first();

            if (! $parent) {
                return null;
            }

            return Category::where('parent_id', $parent->id)
                ->where(function ($query) use ($childName) {
                    $query->where('name', $childName)->orWhere('slug', Str::slug($childName));
                })
                ->first();
        }

        return Category::where('name', $value)->orWhere('slug', Str::slug($value))->first();
    }

    /**
     * Brands are flat, so an unknown name is created instead of failing the
     * whole product (unlike categories, which have a parent/child structure).
     */
    private function resolveBrand(?string $value): ?Brand
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (ctype_digit($value)) {
            return Brand::find((int) $value);
        }

        return Brand::firstOrCreate(
            ['slug' => Str::slug($value)],
            ['name' => $value, 'is_active' => true]
        );
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    private function exportRow(Product $product, ProductVariant $variant): array
    {
        return [
            $product->name,
            $product->category?->full_name ?? '',
            $product->brand?->name ?? '',
            $product->short_description ?? '',
            $product->description ?? '',
            $product->is_active ? '1' : '0',
            $product->is_featured ? '1' : '0',
            $product->tags->pluck('name')->implode(', '),
            $variant->sku,
            number_format((float) $variant->price, 2, '.', ''),
            $variant->purchase_price !== null ? number_format((float) $variant->purchase_price, 2, '.', '') : '',
            $variant->discount_type ?? '',
            $variant->discount_value !== null ? number_format((float) $variant->discount_value, 2, '.', '') : '',
            $variant->stock_quantity,
            $variant->low_stock_threshold,
            $variant->weight !== null ? (string) $variant->weight : '',
            $variant->weight_unit ?? '',
            $variant->is_active ? '1' : '0',
            $variant->is_default ? '1' : '0',
            $variant->optionValues
                ->map(fn ($value) => ($value->option?->name ?? 'Option').': '.$value->value)
                ->implode(' | '),
        ];
    }

    private function writeHeader($handle): void
    {
        fputcsv($handle, self::COLUMNS, ',', '"', '');
    }

    private function isBlankRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function nullableString(string $value): ?string
    {
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /** @return array<int, string> */
    private function parseTags(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function parseBool(string $value, bool $default): bool
    {
        $value = strtolower(trim($value));

        if ($value === '') {
            return $default;
        }

        return in_array($value, ['1', 'true', 'yes', 'on', 'active'], true);
    }

    private function parseDecimal(string $value): ?float
    {
        $value = trim($value);

        if ($value === '' || ! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private function parseInt(string $value): ?int
    {
        $value = trim($value);

        if ($value === '' || ! preg_match('/^-?\d+$/', $value)) {
            return null;
        }

        return (int) $value;
    }
}
