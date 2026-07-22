<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Studio;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function stats()
    {
        $today = Carbon::today()->toDateString();

        $totalBookingToday = Booking::where('booking_date', $today)->count();

        $dailyRevenue = Booking::where('booking_date', $today)
            ->whereIn('status', ['Validated', 'Completed', 'CONFIRMED'])
            ->sum('total_price');

        if ($dailyRevenue >= 1000000) {
            $dailyRevenueFormatted = "Rp " . number_format($dailyRevenue / 1000000, 1) . "M";
        } else {
            $dailyRevenueFormatted = "Rp " . number_format($dailyRevenue, 0, ',', '.');
        }

        $activeStudiosCount = Studio::where('status', 'Available')->count();
        $totalStudiosCount = Studio::count();
        $activeStudios = $activeStudiosCount . "/" . $totalStudiosCount;

        $pendingValidation = Booking::whereIn('status', ['Pending', 'PENDING'])->count();

        $recentTransactionsData = Booking::with(['user', 'studio'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => 'TRX-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT),
                    'client' => $booking->user ? $booking->user->name : 'Unknown',
                    'studio' => $booking->studio ? $booking->studio->name : 'Unknown',
                    'status' => $booking->status,
                    'amount' => 'Rp ' . number_format($booking->total_price, 0, ',', '.')
                ];
            });

        $studioStats = Booking::with('studio')
            ->select('studio_id', \DB::raw('count(*) as total'))
            ->groupBy('studio_id')
            ->get();
        
        $totalAllBookings = $studioStats->sum('total');
        
        $studioDistribution = $studioStats->map(function ($stat) use ($totalAllBookings) {
            return [
                'name' => $stat->studio ? $stat->studio->name : 'Unknown',
                'pct' => $totalAllBookings > 0 ? round(($stat->total / $totalAllBookings) * 100) : 0
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'totalBookingToday' => $totalBookingToday,
                'dailyRevenue' => $dailyRevenueFormatted,
                'activeStudios' => $activeStudios,
                'pendingValidation' => $pendingValidation,
                'recentTransactions' => $recentTransactionsData,
                'studioDistribution' => $studioDistribution
            ]
        ]);
    }
}
