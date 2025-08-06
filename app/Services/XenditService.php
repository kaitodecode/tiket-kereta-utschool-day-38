<?php
namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class XenditService
{
    public static function isHasUnpaidOrders($userId = null)
    {
        $unpaidOrders = Booking::when($userId, function ($q) use ($userId) {
            return $q->where('user_id', $userId);
        })
            ->where('status', 'pending')
            ->whereHas('payment', function ($q) {
                $q->where('status', 'pending');
            })
            ->get();
        return $unpaidOrders->count() > 0;
    }

    public static function getInvoice($invoiceId)
    {
        Configuration::setXenditKey(config('services.xendit.secret_key'));
        $xenditInvoiceApi = new InvoiceApi();
        return $xenditInvoiceApi->getInvoiceById($invoiceId);
    }

    public static function createInvoice($booking)
    {
        Configuration::setXenditKey(config('services.xendit.secret_key'));
        $xenditInvoiceApi = new InvoiceApi();

        $createInvoiceRequest = new CreateInvoiceRequest([
            'external_id' => 'booking-' . $booking->id,
            'amount' => $booking->total_price,
            'description' => 'Payment for booking #' . $booking->id,
            'invoice_duration' => 86400, // 24 hours
            'customer' => [
                'given_names' => $booking->user->name ?? 'Customer',
                'email' => $booking->user->email ?? 'customer@example.com',
            ],
            'success_redirect_url' => config('app.url') . '/payment/success',
            'failure_redirect_url' => config('app.url') . '/payment/failed',
        ]);

        $invoice = $xenditInvoiceApi->createInvoice($createInvoiceRequest);

        // Create payment record
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'status' => 'pending',
            'payment_id' => $invoice['id'],
            'payment_url' => $invoice['invoice_url'],
            'payment_type' => 'xendit_invoice',
        ]);

        return [
            'invoice' => $invoice,
            'payment' => $payment,
        ];
    }

    public static function updatePaymentStatus($paymentId, $status)
    {
        $payment = Payment::where('payment_id', $paymentId)->first();
        
        if ($payment) {
            DB::transaction(function () use ($payment, $status) {
                $payment->update(['status' => $status]);
                
                // Update booking status based on payment status
                if ($status === 'paid') {
                    $payment->booking->update(['status' => 'paid']);
                } elseif ($status === 'expired' || $status === 'failed') {
                    $payment->booking->update(['status' => 'canceled']);
                }
            });
        }
        
        return $payment;
    }

    public static function updateExpiredInvoice($bookingId, $paymentId)
    {
        try {
            $invoice = self::getInvoice($paymentId);
            
            if ($invoice['status'] === 'EXPIRED') {
                $booking = Booking::with('payment')->findOrFail($bookingId);

                DB::transaction(function () use ($booking, $invoice) {
                    // Update booking status
                    $booking->update([
                        'status' => 'canceled',
                        'updated_at' => Carbon::parse($invoice['expiry_date']),
                    ]);

                    // Update payment status
                    if ($booking->payment) {
                        $booking->payment->update([
                            'status' => 'expired',
                            'updated_at' => Carbon::parse($invoice['expiry_date']),
                        ]);
                    }
                });
                
                return true; // Invoice was expired and updated
            }
            
            return false; // Invoice is not expired
        } catch (\Exception $e) {
            Log::error('Error updating expired invoice for booking #' . $bookingId . ': ' . $e->getMessage());
            throw $e;
        }
    }

    public static function checkUnpaidOrders()
    {
        $unpaidOrders = Booking::where('status', 'pending')
            ->with('payment')
            ->whereHas('payment', function ($q) {
                $q->where('status', 'pending');
            })
            ->get();

        $processedCount = 0;
        $expiredCount = 0;
        $errorCount = 0;

        Log::info('Found ' . $unpaidOrders->count() . ' unpaid orders to check');

        foreach ($unpaidOrders as $booking) {
            try {
                if ($booking->payment && $booking->payment->payment_id) {
                    $wasExpired = self::updateExpiredInvoice($booking->id, $booking->payment->payment_id);
                    if ($wasExpired) {
                        $expiredCount++;
                        Log::info('Booking #' . $booking->id . ' marked as expired');
                    }
                    $processedCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Error processing booking #' . $booking->id . ': ' . $e->getMessage());
            }
        }

        Log::info("Processed: {$processedCount}, Expired: {$expiredCount}, Errors: {$errorCount}");
        
        return [
            'total' => $unpaidOrders->count(),
            'processed' => $processedCount,
            'expired' => $expiredCount,
            'errors' => $errorCount,
        ];
    }
}