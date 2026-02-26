<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'reservation_date',
        'start_time',
        'end_time',
        'status',
        'notes',
        'created_by_admin',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'created_by_admin' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('reservation_date', $date);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled']);
    }
}
