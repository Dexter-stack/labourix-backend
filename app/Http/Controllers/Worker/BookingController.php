<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use ApiResponse;

    public function __construct(private BookingService $bookingService) {}

    public function index(Request $request): JsonResponse
    {
        $bookings = $this->bookingService->getWorkerBookings($request->user());

        return $this->success(BookingResource::collection($bookings), 'Bookings retrieved.');
    }

    public function show(Booking $booking): JsonResponse
    {
        return $this->success(
            new BookingResource($booking->load('jobListing', 'employer')),
            'Booking retrieved.'
        );
    }

    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $cancelled = $this->bookingService->cancelBooking($booking, $request->reason);

        return $this->success(new BookingResource($cancelled), 'Booking cancelled.');
    }
}
