<?php

namespace App\Jobs;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateInvoicePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        // Load relationships to prevent N+1 queries in view
        $this->order->load('items.variant.product', 'user');

        // Generate PDF
        $pdf = Pdf::loadView('invoices.template', ['order' => $this->order]);
        
        // Save to Storage
        $fileName = 'invoices/' . $this->order->invoice_number . '.pdf';
        Storage::put('public/' . $fileName, $pdf->output());

        // In a real app, you would trigger an email here with the attachment
        // Mail::to($this->order->user)->send(new OrderInvoiceMail($fileName));
    }
}
