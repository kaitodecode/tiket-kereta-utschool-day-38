<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Payment;
use App\Models\Schedule;
use App\Services\XenditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;

/**
 * @OA\Schema(
 *     schema="Booking",
 *     required={"id", "user_id", "schedule_id", "total_price", "status"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="schedule_id", type="integer", example=1),
 *     @OA\Property(property="total_price", type="number", format="float", example=100000),
 *     @OA\Property(property="status", type="string", enum={"pending", "paid", "cancelled"}, example="pending"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class BookingController extends Controller
{

    private $xenditClient;
    public function __construct()
    {
        $xenditKey = config('services.xendit.secret_key');
        if (empty($xenditKey)) {
            Log::error('Xendit secret key is not configured');
            throw new \Exception('Payment gateway configuration error');
        }
        Configuration::setXenditKey($xenditKey);
        $this->xenditClient = new InvoiceApi();
    }

    /**
     * @OA\Get(
     *     path="/api/bookings",
     *     summary="Get all bookings",
     *     tags={"Bookings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bookings retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bookings retrieved"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Booking")),
     *                 @OA\Property(property="first_page_url", type="string"),
     *                 @OA\Property(property="from", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="last_page_url", type="string"),
     *                 @OA\Property(property="links", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="next_page_url", type="string"),
     *                 @OA\Property(property="path", type="string"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="prev_page_url", type="string"),
     *                 @OA\Property(property="to", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index()
    {
        $perPage = request()->get('per_page', 10);
        $bookings = Booking::with(['schedule', 'passengers', 'payment', 'schedule.train', 'schedule.route.origin', 'schedule.route.destination'])->paginate($perPage);

        return $this->json($bookings, "Bookings retrieved", 200);
    }

    /**
     * @OA\Get(
     *     path="/api/bookings/history",
     *     summary="Get user booking history",
     *     tags={"Bookings"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User booking history retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bookings retrieved"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     allOf={
     *                         @OA\Schema(ref="#/components/schemas/Booking")
     *                     }
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function history()
    {
        $user = auth()->user();
        if (!$user) {
            return $this->json(null, "Unauthorized", 401);
        }

        $bookings = Booking::with(['schedule', 'passengers', 'payment', 'schedule.train', 'schedule.route.origin', 'schedule.route.destination'])
            ->where('user_id', $user->id)
            ->get();

        return $this->json($bookings, "Bookings retrieved", 200);
    }

    /**
     * @OA\Post(
     *     path="/api/bookings",
     *     summary="Create a new booking",
     *     tags={"Bookings"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"schedule_id", "passengers"},
     *             @OA\Property(property="schedule_id", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *             @OA\Property(
     *                 property="passengers",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="id_number", type="string", example="1234567890"),
     *                     @OA\Property(property="status", type="string", enum={"adult", "child"}, example="adult")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Booking created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Booking created"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/Booking")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Train is full or invalid data"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create booking"
     *     )
     * )
     */
public function store(StoreBookingRequest $request)
{
    try {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $schedule = Schedule::lockForUpdate()->find($data['schedule_id']);

            if (!$schedule) {
                return $this->json(null, "Schedule not found", 404);
            }

            $totalPrice = 0;
            $seatNow = $schedule->seat_available;
            $adultPassenger = 0;

            if ($seatNow <= 0 || $seatNow < count($data['passengers'])) {
                return $this->json(null, "Train is full", 400);
            }


            $booking = Booking::create([
                'user_id' => auth()->user()->id,
                'schedule_id' => $data['schedule_id'],
                'total_price' => 0,
                'status' => 'pending',
            ]);

            $bookingPassengers = [];

            foreach ($data['passengers'] as $passenger) {
                if (empty($passenger['name']) || empty($passenger['id_number']) || empty($passenger['status'])) {
                    return $this->json(null, "Invalid passenger data", 400);
                }

                $bookingPassengers[] = [
                    'name' => $passenger['name'],
                    'id_number' => $passenger['id_number'],
                    'seat_number' => $seatNow--,
                    'status' => $passenger['status'],
                ];

                if ($passenger['status'] == 'adult') {
                    $adultPassenger++;
                    $totalPrice += $schedule->price;
                }
            }

            if (XenditService::isHasUnpaidOrders($booking->user_id)) {
                return $this->json(null, 'You have an unpaid order. Please complete it first.', 400);
            }

            $invoiceRequest = new CreateInvoiceRequest([
                'external_id' => (string)$booking->id,
                'amount' => (float)$totalPrice,
                'description' => "Credit Order #" . $booking->id,
                'customer' => [
                    'given_names' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
                'currency' => 'IDR',
                'invoice_duration' => 3600,
                'success_redirect_url' => config('app.url') . '/payment/success/' . $booking->id,
                'failure_redirect_url' => config('app.url') . '/payment/failure/' . $booking->id,
                
            ]);

            try {
                $invoice = $this->xenditClient->createInvoice($invoiceRequest);
            } catch (\Exception $xenditException) {
                Log::error('Xendit API Error: ' . $xenditException->getMessage());
                return $this->json(null, 'Payment gateway error. Please try again later.', 500);
            }

            try {
                $payment = Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => (float)$totalPrice,
                    'status' => 'pending',
                    'payment_id' => $invoice->getId(),
                    'payment_url' => $invoice->getInvoiceUrl(),
                    'payment_type' => 'xendit',
                ]);
            } catch (\Exception $e) {
                Log::error('Payment Creation Error: ' . $e->getMessage());
                return $this->json($e->getMessage(), 'Error creating payment record', 500);
            }

            $booking->total_price = $totalPrice;
            $booking->save();

            $booking->passengers()->createMany($bookingPassengers);

            $schedule->seat_available -= $adultPassenger;
            $schedule->save();

            return $this->json([
                'message' => 'Booking created',
                'data' => $booking->load('passengers'),
                'payment' => $payment,
            ], 201);
        });
    } catch (\Exception $e) {
        Log::error('Booking Creation Error: ' . $e->getMessage());
        return $this->json(null, "Failed to create booking: " . $e->getMessage(), 500);
    }
}

public function success(Booking $booking)
{
    $booking = Booking::find($booking->id);

    if (!$booking) {
        return $this->json([
            'message' => 'Booking not found',
        ], 404);
    }

    $booking->status = 'paid';
    $booking->save();

    return view('payments.success', compact('booking'));
}

public function failure(Booking $booking)
{
    $booking = Booking::find($booking->id);

    if (!$booking) {
        return $this->json([
            'message' => 'Booking not found',
        ], 404);
    }


    $booking->status = 'failed';
    $booking->save();

    return view('payments.failed', compact('booking'));
}

    /**
     * @OA\Get(
     *     path="/api/bookings/{id}",
     *     summary="Get a specific booking",
     *     tags={"Bookings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Booking ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Booking retrieved"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/Booking")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Booking not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show(Booking $booking)
    {
        $booking = Booking::with('passengers')->find($booking->id);

        if (!$booking) {
            return $this->json([
                'message' => 'Booking not found',
            ], 404);
        }

        if ($booking->user_id != auth()->user()->id) {
            return $this->json([
                'message' => 'Booking not found',
            ], 404);
        }

        return $this->json([
            'message' => 'Booking retrieved',
            'data' => $booking,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/bookings/{id}",
     *     summary="Update a booking",
     *     tags={"Bookings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Booking ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"pending", "paid", "cancelled"}, example="paid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Booking updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 allOf={
     *                     @OA\Schema(ref="#/components/schemas/Booking")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized access"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Booking not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot update paid booking"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update booking"
     *     )
     * )
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        try {
            return DB::transaction(function () use ($request, $booking) {
                $data = $request->validated();
                $booking = Booking::lockForUpdate()->find($booking->id);

                if (!$booking) {
                    return $this->json([
                        'message' => 'Booking not found',
                    ], 404);
                }

                if ($booking->user_id !== auth()->user()->id) {
                    return $this->json([
                        'message' => 'Unauthorized access',
                    ], 403);
                }

                if ($booking->status === 'paid') {
                    return $this->json([
                        'message' => 'Cannot update paid booking',
                    ], 422);
                }

                $booking->fill($data);
                $booking->save();

                return $this->json([
                    'message' => 'Booking updated successfully',
                    'data' => $booking->fresh(),
                ]);
            });
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Failed to update booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/bookings/{id}",
     *     summary="Delete a booking",
     *     tags={"Bookings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Booking ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Booking deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized access"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Booking not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot delete paid booking"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete booking"
     *     )
     * )
     */
    public function destroy(Booking $booking)
    {
        try {
            return DB::transaction(function () use ($booking) {
                $booking = Booking::lockForUpdate()->find($booking->id);

                if (!$booking) {
                    return $this->json([
                        'message' => 'Booking not found',
                    ], 404);
                }

                if ($booking->user_id !== auth()->user()->id) {
                    return $this->json([
                        'message' => 'Unauthorized access',
                    ], 403);
                }

                if ($booking->status === 'paid') {
                    return $this->json([
                        'message' => 'Cannot delete paid booking',
                    ], 422);
                }

                $booking->delete();

                return $this->json([
                    'message' => 'Booking deleted successfully',
                ]);
            });
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Failed to delete booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
