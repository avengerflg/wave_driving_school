<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // You'll need to install barryvdh/laravel-dompdf

class InvoiceController extends Controller
{
    /**
     * Display user's invoices
     */
    public function index()
    {
        $invoices = Invoice::where('user_id', Auth::id())
            ->with(['items.service', 'items.booking'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show specific invoice
     */
    public function show(Invoice $invoice)
    {
        // Check if user owns this invoice
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $invoice->load(['items.service', 'items.booking', 'user', 'payments']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Download invoice as PDF
     */
    public function download(Invoice $invoice)
    {
        // Check if user owns this invoice
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $invoice->load(['items.service', 'items.booking', 'user']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
