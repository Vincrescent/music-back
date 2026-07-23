<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Booking::with(['user', 'studio'])->orderBy('booking_date', 'desc');

        if ($user && !in_array(strtolower($user->role ?? ''), ['admin', 'kasir', 'pemilik', 'teknisi'])) {
            $query->where('user_id', $user->id);
        }

        $bookings = $query->get();
        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_price' => 'required|numeric',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'customer_email' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'Pending';

        $booking = Booking::create($validated);

        $phone = $booking->customer_phone ?: ($request->user()->phone ?? '081520330787');
        \App\Services\WhatsAppService::sendAutomatedReceipt($phone, $booking);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully & Automated WA Notification Sent!',
            'data' => $booking
        ], 201);
    }

    public function validatePayment(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'Validated';
        $booking->save();

        $phone = $booking->customer_phone ?: ($booking->user->phone ?? '081520330787');
        \App\Services\WhatsAppService::sendAutomatedReceipt($phone, $booking);

        return response()->json([
            'success' => true,
            'message' => 'Booking validated successfully & Automated WA Receipt Sent!',
            'data' => $booking
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Validated,Completed,Cancelled'
        ]);

        $booking = Booking::findOrFail($id);
        $booking->status = $validated['status'];
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully',
            'data' => $booking
        ]);
    }
}
