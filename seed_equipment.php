<?php

use App\Models\Equipment;
use App\Models\MaintenanceTicket;
use Carbon\Carbon;

$equipments = [
    ['name' => 'Yamaha HS8 Studio Monitor', 'category' => 'Alat Studio', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Neumann U87 Ai Microphone', 'category' => 'Alat Studio', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Universal Audio Apollo Twin X', 'category' => 'Alat Studio', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Shure SM7B Vocal Microphone', 'category' => 'Alat Studio', 'studio_id' => 2, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Focusrite Scarlett 18i20', 'category' => 'Alat Studio', 'studio_id' => 2, 'status' => 'Good', 'notes' => ''],
    ['name' => 'AKG C414 XLII', 'category' => 'Alat Studio', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Sennheiser HD 650 Headphones', 'category' => 'Alat Studio', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Beyerdynamic DT 770 PRO', 'category' => 'Alat Studio', 'studio_id' => 2, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Korg Kronos 88-Key Synthesizer', 'category' => 'Alat Studio', 'studio_id' => 2, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Roland RD-2000 Stage Piano', 'category' => 'Alat Studio', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Fender Stratocaster American Professional II', 'category' => 'Alat Musik', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Gibson Les Paul Standard', 'category' => 'Alat Musik', 'studio_id' => 2, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Marshall JCM800 Guitar Amp', 'category' => 'Alat Musik', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Ampeg SVT-CL Bass Amp', 'category' => 'Alat Musik', 'studio_id' => 2, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Fender Precision Bass', 'category' => 'Alat Musik', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'DW Collector\'s Series Drum Kit', 'category' => 'Alat Musik', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Pearl Masters Maple Complete', 'category' => 'Alat Musik', 'studio_id' => 2, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Zildjian K Custom Cymbal Set', 'category' => 'Alat Musik', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Sabian HHX Evolution Cymbal Set', 'category' => 'Alat Musik', 'studio_id' => 2, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Kemper Profiler PowerRack', 'category' => 'Alat Musik', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Novation Summit Synthesizer', 'category' => 'Alat Musik', 'studio_id' => 2, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Moog Subsequent 37', 'category' => 'Alat Musik', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Elektron Octatrack MKII', 'category' => 'Alat Studio', 'studio_id' => 2, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Solid State Logic SSL2+', 'category' => 'Alat Studio', 'studio_id' => 1, 'status' => 'Good', 'notes' => ''],
    ['name' => 'Mellotron M4000D Mini', 'category' => 'Alat Musik', 'studio_id' => 2, 'status' => 'Good', 'notes' => '']
];

foreach ($equipments as $eq) {
    Equipment::firstOrCreate(['name' => $eq['name']], $eq);
}

// Generate scenario for maintenance tickets (every day for the past 7 days, and some for today)
$allEq = Equipment::all();
$issues = [
    'Kabel terputus', 'Suara pecah / distorsi', 'Tombol tidak berfungsi', 'Power mati total',
    'Noise grounding tinggi', 'Fader seret', 'Tabung amp berisik', 'Konektor longgar'
];

$count = 0;
foreach($allEq as $eq) {
    if(rand(1, 10) <= 3 && $eq->status == 'Good') { // 30% chance to be broken
        $status = rand(1, 10) <= 5 ? 'Service' : 'Broken';
        $eq->update(['status' => $status]);
        
        $date = Carbon::now()->subDays(rand(0, 5));
        
        MaintenanceTicket::create([
            'equipment_id' => $eq->id,
            'technician_id' => null,
            'issue_description' => $issues[array_rand($issues)],
            'status' => 'Pending',
            'scheduled_date' => null,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
        $count++;
    }
}
echo "Seeded " . count($equipments) . " equipments and created $count broken scenarios.\n";
