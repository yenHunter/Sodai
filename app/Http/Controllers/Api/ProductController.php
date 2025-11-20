<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductService;
use App\Jobs\ProcessProductImport;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $repository,
        protected ProductService $service
    ) {}

    // GET /api/v1/products?q=search_term
    public function index(Request $request)
    {
        $products = $this->repository->search($request->get('q', ''), 20);
        return response()->json($products);
    }

    // POST /api/v1/products (Vendor Only)
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

        $validated['vendor_id'] = $request->user()->id; // Attach current user

        $product = $this->service->createProductWithVariants($validated);

        return response()->json($product->load('variants'), 201);
    }

    // POST /api/v1/products/import (Vendor Only)
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        // Simple CSV Parsing
        $file = $request->file('file');
        $data = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($data); // Remove header row if exists, assume row 1 is header: name,sku,price,quantity
        
        // Map header to keys
        $csvData = [];
        foreach ($data as $row) {
            if(count($header) == count($row)) {
                $csvData[] = array_combine($header, $row);
            }
        }

        // Dispatch Job
        ProcessProductImport::dispatch($request->user()->id, $csvData);

        return response()->json(['message' => 'Import started successfully.'], 202);
    }
}
