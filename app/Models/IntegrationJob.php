<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'payload',
        'status',
        'last_error',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
