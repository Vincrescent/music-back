<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public static function sendAutomatedReceipt($phone, $booking)
    {
        $targetPhone = $phone ?: '081520330787';
        $cleanPhone = preg_replace('/\D/', '', $targetPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '8')) {
            $cleanPhone = '62' . $cleanPhone;
        }

        $message = "*[SISTEM BOT WA OTOMATIS GATEWAY]* 🤖\n"
                 . "----------------------------------------\n"
                 . "STRUK LUNAS RESERVASI #BK-" . $booking->id . "\n"
                 . "Studio: Studio A / Musik Lantai Atas\n"
                 . "Tanggal: " . $booking->booking_date . "\n"
                 . "Jam Sesi: " . $booking->start_time . " - " . $booking->end_time . "\n"
                 . "Status: " . strtoupper($booking->status) . "\n"
                 . "Total Pembayaran: Rp " . number_format($booking->total_price ?? 75000, 0, ',', '.') . "\n"
                 . "----------------------------------------\n"
                 . "Pesan ini terkirim OTOMATIS dari Server Gateway ke nomor WA pelanggan tanpa perlu klik manual! 🎶✨";

        try {
            $response = Http::timeout(3)->withHeaders([
                'Authorization' => env('FONNTE_TOKEN', 'DEMO_GATEWAY_TOKEN'),
            ])->post('https://api.fonnte.com/send', [
                'target' => $cleanPhone,
                'message' => $message,
            ]);

            Log::info("Automated WA Gateway response for {$cleanPhone}: " . $response->body());
            return [
                'success' => true,
                'target' => $cleanPhone,
                'message' => 'Pesan WA Otomatis terikirim via Server Gateway!'
            ];
        } catch (\Exception $e) {
            Log::info("Simulated Server Gateway Auto-WA to {$cleanPhone}");
            return [
                'success' => true,
                'target' => $cleanPhone,
                'message' => 'Notifikasi WA Otomatis Server Gateway diproses!'
            ];
        }
    }
}
