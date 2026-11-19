<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ProductCsvService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductCsvController extends Controller
{
    public function __construct(private ProductCsvService $csvService) {}

    public function index()
    {
        return view('admin.ecommerce.product.import-export', [
            'columns' => ProductCsvService::COLUMNS,
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'status']);
        $isFiltered = collect($filters)->contains(fn ($value) => $value !== null && $value !== '');

        return $this->csvDownload(
            ($isFiltered ? 'products-filtered_' : 'products_').now()->format('Y-m-d_His').'.csv',
            fn ($handle) => $this->csvService->writeExport($handle, $filters)
        );
    }

    public function template()
    {
        return $this->csvDownload(
            'products-import-template.csv',
            fn ($handle) => $this->csvService->writeTemplate($handle)
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        try {
            $result = $this->csvService->import($request->file('file'));
        } catch (\Throwable $e) {
            Log::error('Product CSV import failed.', [
                'exception' => $e,
                'admin_id' => Auth::guard('admin')->id(),
            ]);

            return back()->with('error', 'Import failed: '.$e->getMessage());
        }

        $stats = $result['stats'];
        $errors = $result['errors'];

        $message = vsprintf(
            '%d product%s created, %d updated; %d variant%s added, %d updated.',
            [
                $stats['products_created'],
                $stats['products_created'] === 1 ? '' : 's',
                $stats['products_updated'],
                $stats['variants_created'],
                $stats['variants_created'] === 1 ? '' : 's',
                $stats['variants_updated'],
            ]
        );

        if (! empty($errors)) {
            $message .= ' '.count($errors).' row'.(count($errors) === 1 ? '' : 's').' skipped — see the details below.';
        }

        $imported = $stats['products_created'] + $stats['products_updated'];

        return back()
            ->with('import_result', [
                'stats' => $stats,
                'errors' => array_slice($errors, 0, 100),
                'error_count' => count($errors),
            ])
            ->with($imported === 0 && ! empty($errors) ? 'error' : 'success', $message);
    }

    private function csvDownload(string $filename, callable $writer)
    {
        return response()->streamDownload(function () use ($writer) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens the file with the correct encoding.
            fwrite($handle, "\xEF\xBB\xBF");

            $writer($handle);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
