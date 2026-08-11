<?php

namespace App\Http\Controllers\Visitor;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Visitor\ProductCatalogService;

class ProductController extends Controller
{
    public function __construct(
        private ProductCatalogService $catalogService
    ) {}

    public function index(Request $request)
    {
        $filters  = $this->extractFilters($request);
        $products = $this->catalogService->getFilteredProducts($filters);

        $activeAttrs = $this->catalogService->getActiveAttributeKeys();

        return view('visitor.pages.products', [
            'products'     => $products,
            'categories'   => $this->catalogService->getFilterableCategories(),
            'colors'       => in_array('color', $activeAttrs) ? $this->catalogService->getAvailableColors() : [],
            'sizes'        => in_array('size', $activeAttrs) ? $this->catalogService->getAvailableSizes() : [],
            'priceBounds'  => $this->catalogService->getPriceBounds(),
            'activeAttrs'  => $activeAttrs,
            'filters'      => $filters,
        ]);
    }

    public function byCategory(Request $request, Category $category)
    {
        abort_unless($category->is_active, 404);

        $filters  = $this->extractFilters($request);
        $products = $this->catalogService->getFilteredProducts($filters, $category);

        $activeAttrs = $this->catalogService->getActiveAttributeKeys();

        return view('visitor.pages.products-by-category', [
            'products'     => $products,
            'category'     => $category,
            'colors'       => in_array('color', $activeAttrs) ? $this->catalogService->getAvailableColors($category) : [],
            'sizes'        => in_array('size', $activeAttrs) ? $this->catalogService->getAvailableSizes($category) : [],
            'priceBounds'  => $this->catalogService->getPriceBounds($category),
            'activeAttrs'  => $activeAttrs,
            'filters'      => $filters,
        ]);
    }

    private function extractFilters(Request $request): array
    {
        return [
            'category'  => array_filter((array) $request->input('category', [])),
            'color'     => array_filter((array) $request->input('color', [])),
            'size'      => array_filter((array) $request->input('size', [])),
            'price_min' => $request->input('price_min'),
            'price_max' => $request->input('price_max'),
            'sort'      => $request->input('sort', 'relevance'),
        ];
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product      = $this->catalogService->getProductForDetail($product);
        $variantMatrix = $this->catalogService->buildVariantMatrix($product);
        $activeAttrs  = $this->catalogService->getActiveAttributeKeys();

        return view('visitor.pages.product-details', [
            'product'       => $product,
            'variantMatrix' => $variantMatrix,
            'activeAttrs'   => $activeAttrs,
        ]);
    }
}
