<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\JobListing;
use App\Models\User;
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
        $bookings = $this->bookingService->getEmployerBookings($request->user());

        return $this->success(BookingResource::collection($bookings), 'Bookings retrieved.');
    }

    public function store(CreateBookingRequest $request): JsonResponse
    {
        $job    = JobListing::findOrFail($request->job_listing_id);
        $worker = User::findOrFail($request->worker_id);

        $booking = $this->bookingService->createBooking($job, $worker, $request->user());

        return $this->created(
            new BookingResource($booking->load('jobListing', 'worker', 'employer')),
            'Booking created successfully.'
        );
    }

    public function show(Booking $booking): JsonResponse
    {
        return $this->success(
            new BookingResource($booking->load('jobListing', 'worker', 'employer')),
            'Booking retrieved.'
        );
    }

    public function confirm(Booking $booking): JsonResponse
    {
        $confirmed = $this->bookingService->confirmBooking($booking);

        return $this->success(new BookingResource($confirmed), 'Booking confirmed.');
    }

    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $cancelled = $this->bookingService->cancelBooking($booking, $request->reason);

        return $this->success(new BookingResource($cancelled), 'Booking cancelled.');
    }
}
