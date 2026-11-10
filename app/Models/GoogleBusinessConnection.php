<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleBusinessConnection extends Model
{
    protected $fillable = [
        'refresh_token',
        'location_name',
        'location_title',
    ];

    protected function casts(): array
    {
        return [
            'refresh_token' => 'encrypted',
        ];
    }
}
