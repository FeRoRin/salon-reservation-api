<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'open_time',
        'close_time',
        'working_days',
    ];

    protected $casts = [
        'working_days' => 'array',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'open_time' => '09:00:00',
            'close_time' => '18:00:00',
            'working_days' => [1, 2, 3, 4, 5], // Mon-Fri
        ]);
    }
}
