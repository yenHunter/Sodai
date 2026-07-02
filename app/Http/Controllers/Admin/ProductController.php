<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index()
    {
        $products = $this->productService->getProductsForIndex();
        $categories = $this->productService->getCategoriesForForm();

        return view('admin.ecommerce.product.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = $this->productService->getCategoriesForForm();

        return view('admin.ecommerce.product.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $this->productService->store($request->validated());

            return redirect()->route('admin.ecommerce.product.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ecommerce.product.index')->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'tags', 'images']);

        return view('admin.ecommerce.product.details', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'tags']);
        $categories = $this->productService->getCategoriesForForm();

        return view('admin.ecommerce.product.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $this->productService->update($product, $request->validated());

            return redirect()->route('admin.ecommerce.product.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ecommerce.product.index')->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->productService->delete($product);

            return redirect()->route('admin.ecommerce.product.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ecommerce.product.index')->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(Product $product)
    {
        try {
            $updated = $this->productService->toggleStatus($product);
            $status = $updated->is_active ? 'activated' : 'deactivated';

            return redirect()->route('admin.ecommerce.product.index')->with('success', "Product {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()->route('admin.ecommerce.product.index')->with('error', 'Failed to update product status.');
        }
    }

    public function toggleFeatured(Product $product)
    {
        try {
            $updated = $this->productService->toggleFeatured($product);
            $status = $updated->is_featured ? 'featured' : 'unfeatured';

            return redirect()->route('admin.ecommerce.product.index')->with('success', "Product {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()->route('admin.ecommerce.product.index')->with('error', 'Failed to update featured status.');
        }
    }
}
