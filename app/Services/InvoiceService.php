<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Generate invoice for a booking
     */
    public function generateInvoiceForBooking(Booking $booking): Invoice
    {
        // Load relationships
        $booking->load(['service', 'user', 'suburb']);
        
        // Create invoice
        $invoice = Invoice::create([
            'user_id' => $booking->user_id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'amount' => $booking->service->price,
            'status' => 'pending',
            'due_date' => Carbon::now()->addDays(7),
            'notes' => "Invoice for driving lesson booking on {$booking->date->format('d/m/Y')}",
        ]);

        // Create invoice item
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $booking->service->name . ' - ' . $booking->date->format('d/m/Y') . ' at ' . Carbon::parse($booking->start_time)->format('H:i'),
            'quantity' => 1,
            'unit_price' => $booking->service->price,
            'amount' => $booking->service->price,
            'booking_id' => $booking->id,
            'service_id' => $booking->service_id,
        ]);

        return $invoice;
    }

    /**
     * Mark invoice as paid
     */
    public function markInvoiceAsPaid(Invoice $invoice, Payment $payment): Invoice
    {
        $invoice->update([
            'status' => 'paid',
            'paid_date' => $payment->payment_date ?? now(),
        ]);

        // Update payment with invoice_id
        $payment->update([
            'invoice_id' => $invoice->id,
        ]);

        return $invoice;
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the last invoice number for this month
        $lastInvoice = Invoice::where('invoice_number', 'like', "WDS-{$year}{$month}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract the sequence number and increment
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf("WDS-%s%s-%04d", $year, $month, $newNumber);
    }
}
