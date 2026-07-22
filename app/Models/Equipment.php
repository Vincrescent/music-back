<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'studio_id',
        'name',
        'status',
        'notes',
    ];

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
}
