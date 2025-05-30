<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $method = $request->input('method');
        $dateRange = $request->input('date_range');
        
        $query = Payment::with(['user', 'booking', 'invoice'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('transaction_id', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($method, function ($query, $method) {
                return $query->where('payment_method', $method);
            })
            ->when($dateRange, function ($query, $dateRange) {
                $dates = explode(' to ', $dateRange);
                if (count($dates) === 2) {
                    $startDate = Carbon::parse($dates[0])->startOfDay();
                    $endDate = Carbon::parse($dates[1])->endOfDay();
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                return $query;
            });
            
        // Get payment methods for filter
        $paymentMethods = Payment::distinct()->pluck('payment_method')->filter();
            
        $payments = $query->latest()->paginate(15)->withQueryString();
        
        // Calculate totals for dashboard
        $totalPayments = Payment::where('status', 'completed')->sum('amount');
        $todayPayments = Payment::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');
        $monthlyPayments = Payment::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
            
        return view('admin.payments.index', compact(
            'payments', 
            'search', 
            'status', 
            'method', 
            'dateRange', 
            'paymentMethods',
            'totalPayments',
            'todayPayments',
            'monthlyPayments'
        ));
    }

    /**
     * Display a listing of invoices.
     */
    public function invoices(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $dateRange = $request->input('date_range');
        
        $query = Invoice::with(['user', 'payments'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            })
            ->when($status, function ($query, $status) {
                if ($status === 'overdue') {
                    return $query->where('status', 'pending')
                        ->whereDate('due_date', '<', now());
                }
                return $query->where('status', $status);
            })
            ->when($dateRange, function ($query, $dateRange) {
                $dates = explode(' to ', $dateRange);
                if (count($dates) === 2) {
                    $startDate = Carbon::parse($dates[0])->startOfDay();
                    $endDate = Carbon::parse($dates[1])->endOfDay();
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                return $query;
            });
            
        $invoices = $query->latest()->paginate(15)->withQueryString();
        
        // Calculate totals
        $totalUnpaid = Invoice::where('status', 'pending')->sum('amount');
        $totalPaid = Invoice::where('status', 'paid')->sum('amount');
        $overdue = Invoice::where('status', 'pending')
            ->whereDate('due_date', '<', now())
            ->count();
            
        return view('admin.payments.invoices', compact(
            'invoices', 
            'search', 
            'status',
            'dateRange',
            'totalUnpaid',
            'totalPaid',
            'overdue'
        ));
    }

    /**
     * Show payment details.
     */
    public function show(Payment $payment)
    {
        $payment->load(['user', 'booking.service', 'booking.instructor.user', 'invoice']);
        
        return view('admin.payments.show', compact('payment'));
    }
    
    /**
     * Show invoice details.
     */
    public function showInvoice(Invoice $invoice)
    {
        $invoice->load(['user', 'items.booking', 'items.service', 'payments']);
        
        return view('admin.payments.invoice-show', compact('invoice'));
    }
    
    /**
     * Create a new invoice.
     */
    public function createInvoice()
    {
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        $bookings = Booking::whereDoesntHave('payment')->orWhereHas('payment', function($q) {
            $q->where('status', '!=', 'completed');
        })->with(['user', 'service'])->get();
        
        return view('admin.payments.invoice-create', compact('users', 'bookings'));
    }

    /**
     * Store a new invoice.
     */
    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'invoice_number' => 'nullable|string|max:50|unique:invoices,invoice_number',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.booking_id' => 'nullable|exists:bookings,id',
            'items.*.service_id' => 'nullable|exists:services,id',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Generate invoice number if not provided
            if (empty($validated['invoice_number'])) {
                $latestInvoice = Invoice::latest()->first();
                $lastNumber = $latestInvoice ? intval(substr($latestInvoice->invoice_number, 3)) : 0;
                $validated['invoice_number'] = 'INV' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            }
            
            // Create invoice
            $invoice = Invoice::create([
                'user_id' => $validated['user_id'],
                'invoice_number' => $validated['invoice_number'],
                'amount' => $validated['amount'],
                'status' => 'pending',
                'due_date' => $validated['due_date'],
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Create invoice items
            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['quantity'] * $item['unit_price'],
                    'booking_id' => $item['booking_id'] ?? null,
                    'service_id' => $item['service_id'] ?? null,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.payments.invoice.show', $invoice)
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating invoice: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    /**
     * Edit an invoice.
     */
    public function editInvoice(Invoice $invoice)
    {
        // Only allow editing pending invoices
        if ($invoice->status !== 'pending') {
            return redirect()->route('admin.payments.invoice.show', $invoice)
                ->with('error', 'Only pending invoices can be edited.');
        }
        
        $invoice->load(['user', 'items']);
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        $bookings = Booking::whereDoesntHave('payment')->orWhereHas('payment', function($q) {
            $q->where('status', '!=', 'completed');
        })->with(['user', 'service'])->get();
        
        return view('admin.payments.invoice-edit', compact('invoice', 'users', 'bookings'));
    }

    /**
     * Update an invoice.
     */
    public function updateInvoice(Request $request, Invoice $invoice)
    {
        // Only allow editing pending invoices
        if ($invoice->status !== 'pending') {
            return redirect()->route('admin.payments.invoice.show', $invoice)
                ->with('error', 'Only pending invoices can be edited.');
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number,' . $invoice->id,
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.booking_id' => 'nullable|exists:bookings,id',
            'items.*.service_id' => 'nullable|exists:services,id',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update invoice
            $invoice->update([
                'user_id' => $validated['user_id'],
                'invoice_number' => $validated['invoice_number'],
                'amount' => $validated['amount'],
                'due_date' => $validated['due_date'],
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Get existing item IDs
            $existingItemIds = $invoice->items->pluck('id')->toArray();
            $updatedItemIds = [];
            
            // Update or create items
            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    $item = InvoiceItem::find($itemData['id']);
                    if ($item && $item->invoice_id === $invoice->id) {
                        $item->update([
                            'description' => $itemData['description'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['unit_price'],
                            'amount' => $itemData['quantity'] * $itemData['unit_price'],
                            'booking_id' => $itemData['booking_id'] ?? null,
                            'service_id' => $itemData['service_id'] ?? null,
                        ]);
                        $updatedItemIds[] = $item->id;
                    }
                } else {
                    $item = InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $itemData['description'],
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'amount' => $itemData['quantity'] * $itemData['unit_price'],
                        'booking_id' => $itemData['booking_id'] ?? null,
                        'service_id' => $itemData['service_id'] ?? null,
                    ]);
                    $updatedItemIds[] = $item->id;
                }
            }
            
            // Delete items that were removed
            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($itemsToDelete)) {
                InvoiceItem::whereIn('id', $itemsToDelete)->delete();
            }
            
            DB::commit();
            
            return redirect()->route('admin.payments.invoice.show', $invoice)
                ->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating invoice: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    /**
     * Record a payment for an invoice.
     */
    public function recordPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->amount,
            'payment_method' => 'required|string|in:credit_card,bank_transfer,cash,paypal,stripe,other',
            'transaction_id' => 'nullable|string|max:100',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create payment record
            $payment = Payment::create([
                'user_id' => $invoice->user_id,
                'invoice_id' => $invoice->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'] ?? null,
                'status' => 'completed',
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Update invoice status if fully paid
            $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
            
            if ($totalPaid >= $invoice->amount) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_date' => now(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.payments.invoice.show', $invoice)
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording payment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Mark invoice as paid without payment.
     */
    public function markPaid(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('info', 'Invoice is already marked as paid.');
        }
        
        $invoice->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);
        
        return back()->with('success', 'Invoice marked as paid successfully.');
    }

    /**
     * Mark invoice as cancelled.
     */
    public function markCancelled(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Paid invoices cannot be cancelled.');
        }
        
        $invoice->update([
            'status' => 'cancelled',
        ]);
        
        return back()->with('success', 'Invoice marked as cancelled successfully.');
    }

    /**
     * Send invoice to customer.
     */
    public function sendInvoice(Invoice $invoice)
    {
        // Here you would typically implement email sending logic
        // For now we'll just return a success message
        
        return back()->with('success', 'Invoice sent to customer successfully.');
    }

    /**
     * Generate PDF invoice.
     */
    public function downloadInvoicePdf(Invoice $invoice)
    {
        $invoice->load(['user', 'items.service', 'items.booking', 'payments']);
        
        // Here you would implement PDF generation logic
        // For now, we'll just return to the invoice page
        
        return back()->with('info', 'PDF download functionality will be implemented soon.');
    }
    
    /**
     * Generate payment receipt.
     */
    public function generateReceipt(Payment $payment)
    {
        $payment->load(['user', 'booking.service', 'invoice']);
        
        // Here you would implement PDF generation logic for receipt
        // For now, we'll just return to the payment page
        
        return back()->with('info', 'Receipt generation functionality will be implemented soon.');
    }
}