<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Studio;
use App\Models\Equipment;
use App\Models\Booking;
use App\Models\Review;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $kasir = User::create([
            'name' => 'Vincent Kasir',
            'username' => 'vincentkasir',
            'password' => Hash::make('bulan123'),
            'role' => 'kasir',
            'phone' => '081234567890'
        ]);

        $admin = User::create([
            'name' => 'Vincent Admin',
            'username' => 'vincentadmin',
            'password' => Hash::make('bulan123'),
            'role' => 'admin',
            'phone' => '081234567891'
        ]);

        $teknisi = User::create([
            'name' => 'Vincent Teknisi',
            'username' => 'vincentteknisi',
            'password' => Hash::make('bulan123'),
            'role' => 'teknisi',
            'phone' => '081234567892'
        ]);

        $pemilik = User::create([
            'name' => 'Vincent Pemilik',
            'username' => 'vincentpemilik',
            'password' => Hash::make('bulan123'),
            'role' => 'pemilik',
            'phone' => '081234567893'
        ]);

        $customer = User::create([
            'name' => 'Vincent',
            'username' => 'vincent',
            'password' => Hash::make('bulan123'),
            'role' => 'user',
            'phone' => '081234567894'
        ]);

        // 2. Studios
        $studioA = Studio::create(['name' => 'Studio A', 'type' => 'Premium', 'price_per_hour' => 150000, 'status' => 'Available', 'description' => 'Studio rekaman standar industri dengan SSL Origin 32.']);
        $studioB = Studio::create(['name' => 'Studio B', 'type' => 'Standard', 'price_per_hour' => 85000, 'status' => 'Available', 'description' => 'Ideal untuk latihan band dan rekaman demo.']);
        $studioC = Studio::create(['name' => 'Studio C', 'type' => 'Standard', 'price_per_hour' => 85000, 'status' => 'Maintenance', 'description' => 'Studio C sedang perbaikan akustik.']);
        
        // 3. Equipment
        Equipment::create(['studio_id' => $studioA->id, 'name' => 'SSL Origin 32 Console', 'status' => 'Good', 'notes' => 'Kalibrasi terakhir 12 Okt']);
        Equipment::create(['studio_id' => $studioA->id, 'name' => 'Neumann U87', 'status' => 'Good']);
        Equipment::create(['studio_id' => $studioB->id, 'name' => 'Fender Twin Reverb Amp', 'status' => 'Service', 'notes' => 'Tabung berisik, dijadwalkan ganti minggu ini']);
        Equipment::create(['studio_id' => $studioB->id, 'name' => 'Yamaha Stage Custom Drum Kit', 'status' => 'Good']);

        // 4. Bookings
        Booking::create([
            'user_id' => $customer->id,
            'studio_id' => $studioA->id,
            'booking_date' => Carbon::now()->toDateString(),
            'start_time' => '14:00:00',
            'end_time' => '17:00:00',
            'status' => 'Pending',
            'total_price' => 450000
        ]);

        Booking::create([
            'user_id' => $customer->id,
            'studio_id' => $studioB->id,
            'booking_date' => Carbon::now()->toDateString(),
            'start_time' => '19:00:00',
            'end_time' => '21:00:00',
            'status' => 'Validated',
            'total_price' => 170000
        ]);
    }
}
