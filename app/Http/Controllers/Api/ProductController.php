<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Jobs\ProcessProductImport;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $repository,
        protected ProductService $service
    ) {}

    public function index(Request $request)
    {
        $products = $this->repository->search($request->get('q', ''), 20);
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'required|string|unique:product_variants,sku',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock_quantity' => 'required|integer|min:0',
        ]);

        $validated['vendor_id'] = $request->user()->id;
        $product = $this->service->createProductWithVariants($validated);
        return response()->json($product->load('variants'), 201);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $data = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($data); 
        
        $csvData = [];
        foreach ($data as $row) {
            if(count($header) == count($row)) {
                $csvData[] = array_combine($header, $row);
            }
        }
        ProcessProductImport::dispatch($request->user()->id, $csvData);
        return response()->json(['message' => 'Import started successfully.'], 202);
    }
}
