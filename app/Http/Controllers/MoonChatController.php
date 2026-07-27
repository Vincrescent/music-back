<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoonChatController extends Controller
{
    /**
     * MOON AI Chat — proxies user messages to OpenRouter API
     * with Studio Musik Lantai Atas context.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array',
        ]);

        $apiKey = config('services.openrouter.api_key');

        if (!$apiKey) {
            return response()->json([
                'reply' => 'Maaf, MOON sedang tidak tersedia saat ini. Silakan hubungi kami langsung di studio. 🌙',
                'error' => true,
            ], 200);
        }

        // Studio context for MOON's personality
        $systemPrompt = <<<EOT
Kamu adalah MOON 🌙, asisten AI virtual milik Studio Musik Lantai Atas. Kamu ramah, helpful, dan punya kepribadian yang hangat dengan sentuhan humor.

TENTANG STUDIO MUSIK LANTAI ATAS:
- Studio musik profesional yang berlokasi di Jakarta
- Buka setiap hari dari jam 09:00 sampai 01:00 dini hari
- Memiliki 4 studio dengan berbagai kapasitas dan harga

DAFTAR STUDIO:
1. Studio 1 — Kapasitas 10 orang, Full Head Cabinet, Backline Lengkap + AC + Air Minum. Harga: Rp 75.000 - 100.000/jam
2. Studio 2 — Kapasitas 6 orang, Half Backline, Setengah Backline + AC + Air Minum. Harga: Rp 50.000 - 75.000/jam
3. Studio 3 — Kapasitas 15 orang, Full Head Cabinet + Recording, Backline Lengkap + Rekaman + AC + Air Minum. Harga: Rp 100.000 - 150.000/jam
4. Studio 4 — Kapasitas 4 orang, Acoustic, Setup Akustik + AC + Air Minum. Harga: Rp 40.000 - 60.000/jam

FASILITAS:
- Bilik Rekaman Kedap Suara (noise floor -20dB, kaca akustik tiga lapis)
- Ruang Kontrol Mixing (Monitor Genelec 8351B, Universal Audio Apollo Interface)
- Wi-Fi Kecepatan Tinggi (gigabit)
- Minuman & Makanan Premium (kopi artisanal, camilan sehat)
- Area Lounge dengan kursi ergonomis
- Kontrol Suhu HVAC senyap
- Akses smart-key 24/7 dengan CCTV
- Daya listrik bersih (sirkuit ground terisolasi)
- Loker penyimpanan alat ber-AC

METODE PEMBAYARAN:
- Transfer Bank (BCA / Mandiri / BNI)
- E-Wallet
- QRIS

JAM OPERASIONAL SLOT:
- Pagi: 09:00 - 11:00
- Siang: 11:00 - 17:00 (slot 2 jam)
- Sore & Malam: 17:00 - 01:00 (slot 2 jam)

ATURAN KAMU:
1. Jawab SELALU dalam Bahasa Indonesia kecuali user bertanya dalam bahasa lain
2. Gunakan emoji sesekali untuk kesan ramah 🎵🎸🎤
3. Jika ditanya hal di luar topik studio musik, tetap jawab dengan sopan tapi arahkan kembali ke layanan studio
4. Sarankan user untuk menekan tombol "Pesan Sekarang" di website untuk booking
5. Jangan pernah memberikan informasi yang tidak akurat tentang studio
6. Jika tidak tahu jawabannya, akui dan sarankan untuk menghubungi langsung
7. Selalu akhiri dengan sesuatu yang helpful atau ajakan untuk booking
EOT;

        // Build messages array (OpenAI-compatible format)
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Add conversation history if provided
        if ($request->history && is_array($request->history)) {
            foreach ($request->history as $msg) {
                $role = $msg['role'] === 'user' ? 'user' : 'assistant';
                $messages[] = [
                    'role' => $role,
                    'content' => $msg['text'],
                ];
            }
        }

        // Add the current user message
        $messages[] = [
            'role' => 'user',
            'content' => $request->message,
        ];

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => config('app.url', 'http://localhost'),
                    'X-Title' => 'Studio Musik Lantai Atas',
                ])
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => 'google/gemini-2.0-flash-exp:free',
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 1024,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? 'Maaf, MOON tidak bisa memproses permintaan kamu saat ini. 🌙';

                return response()->json([
                    'reply' => $reply,
                    'error' => false,
                ]);
            } else {
                Log::error('OpenRouter API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'reply' => 'Maaf, MOON sedang mengalami gangguan (kode: ' . $response->status() . '). Coba lagi nanti ya! 🌙',
                    'error' => true,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('MoonChat exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'reply' => 'Maaf, terjadi kesalahan koneksi. Coba lagi nanti ya! 🌙',
                'error' => true,
            ]);
        }
    }
}
