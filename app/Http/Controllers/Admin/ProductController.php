<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductVariant;
use App\Models\Tag;
use App\Services\Admin\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $query = Product::with(['category', 'brand', 'defaultVariant'])
            ->select(
                'id',
                'name',
                'slug',
                'thumbnail',
                'short_description',
                'category_id',
                'brand_id',
                'is_active',
                'is_featured',
                'average_rating',
                'review_count',
                'total_sales',
                'min_price',
                'max_price',
                'total_stock'
            );

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('variants', fn ($v) => $v->where('sku', 'like', "%{$search}%"));
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = $request->input('status')) {
            switch ($status) {
                case 'active':
                    $query->where('is_active', true)->where('total_stock', '>', 0);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'out_of_stock':
                    $query->where('is_active', true)->where('total_stock', '<=', 0);
                    break;
            }
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = $this->productService->getAssignableCategories();

        return view('admin.ecommerce.product.index', compact('products', 'categories'));
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────

    public function show(Product $product)
    {
        $product = $this->productService->getProductForDetails($product);
        $activeAttrs = $this->productService->getActiveAttributeKeys();

        return view('admin.ecommerce.product.details', compact('product', 'activeAttrs'));
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function create()
    {
        $categories = $this->productService->getAssignableCategories();
        $brands = $this->productService->getActiveBrands();
        $products = $this->productService->getProductsForRelation();
        $activeAttrs = $this->productService->getActiveAttributeKeys();
        $productOptions = $this->productService->getProductOptions();

        return view(
            'admin.ecommerce.product.create',
            compact('categories', 'brands', 'products', 'activeAttrs', 'productOptions')
        );
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────

    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->productService->store($request->validated());

            if ($request->expectsJson()) {
                return response()->json([
                    'redirect' => route('admin.ecommerce.product.index'),
                    'message' => "Product \"{$product->name}\" created successfully.",
                ]);
            }

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('success', "Product \"{$product->name}\" created successfully with {$product->variants()->count()} variant(s).");
        } catch (\Throwable $e) {          // ← was \Exception — broadened so real PHP errors are also caught & logged
            Log::error('Admin failed to create product.', [
                'exception' => $e,
                'admin_id' => Auth::guard('admin')->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Failed to create product: '.$e->getMessage()], 500);
            }

            return redirect()
                ->route('admin.ecommerce.product.create')
                ->with('error', 'Failed to create product: '.$e->getMessage())
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
        $brands = $this->productService->getActiveBrands();
        $products = $this->productService->getProductsForRelation($product->id);
        $productOptions = $this->productService->getProductOptions();

        return view(
            'admin.ecommerce.product.edit',
            compact('product', 'categories', 'brands', 'products', 'productOptions')
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
            Log::error('Admin failed to update product.', [
                'exception' => $e,
                'admin_id' => Auth::guard('admin')->id(),
                'product_id' => $product->id,
            ]);

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', 'Failed to update product: '.$e->getMessage())
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
            Log::error('Admin failed to delete product.', [
                'exception' => $e,
                'admin_id' => Auth::guard('admin')->id(),
                'product_id' => $product->id,
            ]);

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => ['required', 'string']]);

        $ids = array_filter(explode(',', $request->input('ids')));

        if (empty($ids)) {
            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', 'No products selected.');
        }

        $successCount = 0;
        $failedNames = [];

        foreach ($ids as $id) {
            $product = Product::find($id);
            if (! $product) {
                continue;
            }

            try {
                $this->productService->delete($product);
                $successCount++;
            } catch (\Exception $e) {
                $failedNames[] = $product->name;

                Log::warning('Bulk delete: product skipped due to error.', [
                    'exception' => $e,
                    'admin_id' => Auth::guard('admin')->id(),
                    'product_id' => $product->id,
                    'name' => $product->name,
                ]);
            }
        }

        $message = "{$successCount} product".($successCount === 1 ? '' : 's').' deleted successfully.';

        if (! empty($failedNames)) {
            $message .= ' Failed: '.implode(', ', $failedNames).'.';

            return redirect()->route('admin.ecommerce.product.index')->with('error', $message);
        }

        return redirect()->route('admin.ecommerce.product.index')->with('success', $message);
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS / FEATURED — unchanged (product-level)
    // ─────────────────────────────────────────────

    public function toggleStatus(Product $product)
    {
        try {
            $updated = $this->productService->toggleStatus($product);
            $status = $updated->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('success', "Product {$status} successfully.");
        } catch (\Exception $e) {
            Log::error('Admin failed to toggle product status.', [
                'exception' => $e,
                'admin_id' => Auth::guard('admin')->id(),
                'product_id' => $product->id,
            ]);

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', 'Failed to update status.');
        }
    }

    public function toggleFeatured(Product $product)
    {
        try {
            $updated = $this->productService->toggleFeatured($product);
            $status = $updated->is_featured ? 'marked as featured' : 'removed from featured';

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('success', "Product {$status}.");
        } catch (\Exception $e) {
            Log::error('Admin failed to toggle product featured status.', [
                'exception' => $e,
                'admin_id' => Auth::guard('admin')->id(),
                'product_id' => $product->id,
            ]);

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', 'Failed to update featured status.');
        }
    }

    // ─────────────────────────────────────────────
    // IMAGE MANAGEMENT (AJAX endpoints) — unchanged, ProductImage
    // is variant-aware already via ProductService
    // ─────────────────────────────────────────────

    public function deleteImage(Product $product, ProductImage $image)
    {
        try {
            if ($image->product_id !== $product->id) {
                return response()->json(['success' => false, 'message' => 'Image does not belong to this product.'], 403);
            }

            $this->productService->deleteSingleImage($product, $image);

            return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Failed to delete product image via AJAX.', [
                'exception' => $e,
                'admin_id' => Auth::guard('admin')->id(),
                'product_id' => $product->id,
                'image_id' => $image->id,
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        try {
            if ($image->product_id !== $product->id) {
                return response()->json(['success' => false, 'message' => 'Image does not belong to this product.'], 403);
            }

            $this->productService->setPrimaryImage($product, $image->id);

            return response()->json(['success' => true, 'message' => 'Primary image updated.']);
        } catch (\Exception $e) {
            Log::error('Failed to set primary product image via AJAX.', [
                'exception' => $e,
                'admin_id' => Auth::guard('admin')->id(),
                'product_id' => $product->id,
                'image_id' => $image->id,
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function reorderImages(Request $request, Product $product)
    {
        $request->validate([
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'exists:product_images,id'],
        ]);

        try {
            $this->productService->reorderImages($product, $request->input('ordered_ids'));

            return response()->json(['success' => true, 'message' => 'Image order updated.']);
        } catch (\Exception $e) {
            Log::error('Failed to reorder product images via AJAX.', [
                'exception' => $e,
                'admin_id' => Auth::guard('admin')->id(),
                'product_id' => $product->id,
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────
    // TAG AUTOCOMPLETE (AJAX) — unchanged
    // ─────────────────────────────────────────────

    public function searchTags(Request $request)
    {
        $search = $request->input('q', '');

        $tags = Tag::where('name', 'like', "%{$search}%")->limit(10)->pluck('name');

        return response()->json($tags);
    }

    // ─────────────────────────────────────────────
    // PRODUCT SEARCH (Select2 AJAX - Related Products)
    // ─────────────────────────────────────────────

    public function search(Request $request)
    {
        $search = $request->input('q', '');
        $exclude = $request->input('exclude');

        $products = Product::active()
            ->select('id', 'name', 'min_price')
            ->with(['defaultVariant:id,product_id,sku'])
            ->where('name', 'like', "%{$search}%")
            ->when($exclude, fn ($query) => $query->where('id', '!=', $exclude))
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    // ─────────────────────────────────────────────
    // VARIANT OPTIONS AUTOCOMPLETE (AJAX — used by the matrix builder)
    // ─────────────────────────────────────────────

    public function searchOptions(Request $request)
    {
        $search = $request->input('q', '');

        $options = ProductOption::with('values')
            ->where('name', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json($options);
    }

    // ─────────────────────────────────────────────
    // STOCK QUICK UPDATE (AJAX) — now targets a specific VARIANT
    // Route param changed: /products/{product}/variants/{variant}/stock
    // ─────────────────────────────────────────────

    public function updateStock(Request $request, Product $product, ProductVariant $variant)
    {
        $request->validate([
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ]);

        if ($variant->product_id !== $product->id) {
            return response()->json(['success' => false, 'message' => 'Variant does not belong to this product.'], 403);
        }

        try {
            $updated = $this->productService->updateVariantStock($variant, $request->input('stock_quantity'));

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully.',
                'stock_status' => $updated->is_out_of_stock ? 'Out of Stock' : ($updated->is_low_stock ? 'Low Stock' : 'In Stock'),
                'is_low_stock' => $updated->is_low_stock,
                'is_out_of_stock' => $updated->is_out_of_stock,
                'product_total_stock' => $product->fresh()->total_stock,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update variant stock via AJAX.', [
                'exception' => $e,
                'admin_id' => Auth::guard('admin')->id(),
                'product_id' => $product->id,
                'variant_id' => $variant->id,
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
