<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])
            ->select(
                'id',
                'name',
                'sku',
                'thumbnail',
                'short_description',
                'category_id',
                'brand_id',
                'price',
                'stock_quantity',
                'is_active',
                'is_featured',
                'average_rating',
                'review_count',
                'total_sales'
            );

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Status filter
        if ($status = $request->input('status')) {
            switch ($status) {
                case 'active':
                    $query->where('is_active', true)->where('stock_quantity', '>', 0);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'out_of_stock':
                    $query->where('is_active', true)->where('stock_quantity', '<=', 0);
                    break;
            }
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = $this->productService->getAssignableCategories();

        return view('admin.ecommerce.product.index', compact('products', 'categories'));
    }

    // ─────────────────────────────────────────────
    // CREATE (data for Add modal, if needed separately)
    // ─────────────────────────────────────────────

    public function create()
    {
        $categories = $this->productService->getAssignableCategories();
        $brands     = $this->productService->getActiveBrands();
        $products   = $this->productService->getProductsForRelation();

        return view(
            'admin.ecommerce.product.create',
            compact('categories', 'brands', 'products')
        );
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────

    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->productService->store($request->validated());

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('success', "Product \"{$product->name}\" created successfully. SKU: {$product->sku}");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', 'Failed to create product: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────

    public function edit(Product $product)
    {
        $product = $this->productService->getProductWithRelations($product);

        $categories = $this->productService->getAssignableCategories();
        $brands     = $this->productService->getActiveBrands();
        $products   = $this->productService->getProductsForRelation($product->id);

        return view(
            'admin.ecommerce.product.edit',
            compact('product', 'categories', 'brands', 'products')
        );
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $this->productService->update($product, $request->validated());

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', 'Failed to update product: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────

    public function destroy(Product $product)
    {
        try {
            $this->productService->delete($product);

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'string'],
        ]);

        $ids = array_filter(explode(',', $request->input('ids')));

        if (empty($ids)) {
            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', 'No products selected.');
        }

        $successCount = 0;
        $failedNames  = [];

        foreach ($ids as $id) {
            $product = Product::find($id);
            if (!$product) continue;

            try {
                $this->productService->delete($product);
                $successCount++;
            } catch (\Exception $e) {
                $failedNames[] = $product->name;
            }
        }

        $message = "{$successCount} product" .
            ($successCount === 1 ? '' : 's') .
            " deleted successfully.";

        if (!empty($failedNames)) {
            $message .= ' Failed: ' . implode(', ', $failedNames) . '.';
            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', $message);
        }

        return redirect()
            ->route('admin.ecommerce.product.index')
            ->with('success', $message);
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Product $product)
    {
        try {
            $updated = $this->productService->toggleStatus($product);
            $status  = $updated->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('success', "Product {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', 'Failed to update status.');
        }
    }

    // ─────────────────────────────────────────────
    // TOGGLE FEATURED
    // ─────────────────────────────────────────────

    public function toggleFeatured(Product $product)
    {
        try {
            $updated = $this->productService->toggleFeatured($product);
            $status  = $updated->is_featured ? 'marked as featured' : 'removed from featured';

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('success', "Product {$status}.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', 'Failed to update featured status.');
        }
    }

    // ─────────────────────────────────────────────
    // IMAGE MANAGEMENT (AJAX endpoints)
    // ─────────────────────────────────────────────

    /**
     * Delete a single gallery image via AJAX (used in Edit screen
     * for instant removal without waiting for full form submit).
     */
    public function deleteImage(Product $product, ProductImage $image)
    {
        try {
            if ($image->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image does not belong to this product.',
                ], 403);
            }

            $this->productService->deleteSingleImage($product, $image);

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set a gallery image as the primary image via AJAX.
     */
    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        try {
            if ($image->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image does not belong to this product.',
                ], 403);
            }

            $this->productService->setPrimaryImage($product, $image->id);

            return response()->json([
                'success' => true,
                'message' => 'Primary image updated.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reorder gallery images via AJAX (drag-and-drop sortable UI).
     */
    public function reorderImages(Request $request, Product $product)
    {
        $request->validate([
            'ordered_ids'   => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'exists:product_images,id'],
        ]);

        try {
            $this->productService->reorderImages($product, $request->input('ordered_ids'));

            return response()->json([
                'success' => true,
                'message' => 'Image order updated.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────
    // TAG AUTOCOMPLETE (AJAX)
    // ─────────────────────────────────────────────

    public function searchTags(Request $request)
    {
        $search = $request->input('q', '');

        $tags = \App\Models\Tag::where('name', 'like', "%{$search}%")
            ->limit(10)
            ->pluck('name');

        return response()->json($tags);
    }

    // ─────────────────────────────────────────────
    // STOCK QUICK UPDATE (AJAX - optional inline edit)
    // ─────────────────────────────────────────────

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $updated = $this->productService->updateStock(
                $product,
                $request->input('stock_quantity')
            );

            return response()->json([
                'success'       => true,
                'message'       => 'Stock updated successfully.',
                'stock_status'  => $updated->stock_status,
                'is_low_stock'  => $updated->is_low_stock,
                'is_out_of_stock' => $updated->is_out_of_stock,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search products for Select2 dropdown (Related Products selection)
     */
    public function search(Request $request)
    {
        $search = $request->input('q', '');
        $exclude = $request->input('exclude'); // Exclude current product in edit mode

        $products = Product::active()
            ->select('id', 'name', 'sku')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->when($exclude, function ($query) use ($exclude) {
                $query->where('id', '!=', $exclude);
            })
            ->limit(20)
            ->get();

        return response()->json($products);
    }
}
