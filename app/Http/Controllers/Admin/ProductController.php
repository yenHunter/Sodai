<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
            ->select('id', 'name', 'sku', 'thumbnail', 'short_description',
                     'category_id', 'brand_id', 'price', 'stock_quantity',
                     'is_active', 'is_featured', 'average_rating', 'review_count', 'total_sales');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

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
        dd($products, $categories);

        // return view('admin.ecommerce.product.index', compact('products', 'categories'));
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────

    public function show(Product $product)
    {
        $product = $this->productService->getProductForDetails($product);

        return view('admin.ecommerce.product.details', compact('product'));
    }

    // ─────────────────────────────────────────────
    // CREATE
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
            Log::error('Admin failed to create product.', [
                'exception' => $e,
                'admin_id'  => Auth::guard('admin')->id(),
            ]);

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
            Log::error('Admin failed to update product.', [
                'exception'  => $e,
                'admin_id'   => Auth::guard('admin')->id(),
                'product_id' => $product->id,
            ]);

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
            Log::error('Admin failed to delete product.', [
                'exception'  => $e,
                'admin_id'   => Auth::guard('admin')->id(),
                'product_id' => $product->id,
            ]);

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

                Log::warning('Bulk delete: product skipped due to error.', [
                    'exception'  => $e,
                    'admin_id'   => Auth::guard('admin')->id(),
                    'product_id' => $product->id,
                    'name'       => $product->name,
                ]);
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
            Log::error('Admin failed to toggle product status.', [
                'exception'  => $e,
                'admin_id'   => Auth::guard('admin')->id(),
                'product_id' => $product->id,
            ]);

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
            Log::error('Admin failed to toggle product featured status.', [
                'exception'  => $e,
                'admin_id'   => Auth::guard('admin')->id(),
                'product_id' => $product->id,
            ]);

            return redirect()
                ->route('admin.ecommerce.product.index')
                ->with('error', 'Failed to update featured status.');
        }
    }

    // ─────────────────────────────────────────────
    // IMAGE MANAGEMENT (AJAX endpoints)
    // ─────────────────────────────────────────────

    public function deleteImage(Product $product, ProductImage $image)
    {
        try {
            if ($image->product_id !== $product->id) {
                Log::warning('Attempted to delete image not belonging to product.', [
                    'admin_id'   => Auth::guard('admin')->id(),
                    'product_id' => $product->id,
                    'image_id'   => $image->id,
                ]);

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
            Log::error('Failed to delete product image via AJAX.', [
                'exception'  => $e,
                'admin_id'   => Auth::guard('admin')->id(),
                'product_id' => $product->id,
                'image_id'   => $image->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        try {
            if ($image->product_id !== $product->id) {
                Log::warning('Attempted to set primary image not belonging to product.', [
                    'admin_id'   => Auth::guard('admin')->id(),
                    'product_id' => $product->id,
                    'image_id'   => $image->id,
                ]);

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
            Log::error('Failed to set primary product image via AJAX.', [
                'exception'  => $e,
                'admin_id'   => Auth::guard('admin')->id(),
                'product_id' => $product->id,
                'image_id'   => $image->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

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
            Log::error('Failed to reorder product images via AJAX.', [
                'exception'  => $e,
                'admin_id'   => Auth::guard('admin')->id(),
                'product_id' => $product->id,
            ]);

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
    // PRODUCT SEARCH (Select2 AJAX - Related Products)
    // ─────────────────────────────────────────────

    public function search(Request $request)
    {
        $search  = $request->input('q', '');
        $exclude = $request->input('exclude');

        $products = Product::active()
            ->select('id', 'name', 'sku')
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->when($exclude, function($query) use ($exclude) {
                $query->where('id', '!=', $exclude);
            })
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    // ─────────────────────────────────────────────
    // STOCK QUICK UPDATE (AJAX)
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
                'success'         => true,
                'message'         => 'Stock updated successfully.',
                'stock_status'    => $updated->stock_status,
                'is_low_stock'    => $updated->is_low_stock,
                'is_out_of_stock' => $updated->is_out_of_stock,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update product stock via AJAX.', [
                'exception'  => $e,
                'admin_id'   => Auth::guard('admin')->id(),
                'product_id' => $product->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}