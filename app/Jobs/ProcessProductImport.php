<?php

namespace App\Jobs;

use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $vendorId,
        protected array $csvData // Passing array to avoid file permission issues in queue
    ) {}

    public function handle(ProductService $service): void
    {
        foreach ($this->csvData as $row) {
            try {
                // Expecting CSV columns: name, sku, price, quantity
                $payload = [
                    'vendor_id' => $this->vendorId,
                    'name' => $row['name'],
                    'description' => 'Imported Product',
                    'variants' => [
                        [
                            'sku' => $row['sku'],
                            'price' => $row['price'],
                            'stock_quantity' => $row['quantity'],
                        ]
                    ]
                ];

                $service->createProductWithVariants($payload);
            } catch (\Exception $e) {
                Log::error("Failed to import product row: " . json_encode($row) . " Error: " . $e->getMessage());
            }
        }
    }
}
