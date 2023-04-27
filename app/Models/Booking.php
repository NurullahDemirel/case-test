<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['room'];

    protected $casts = [
        'enter_date' => 'datetime',
        'exit_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function room()
    {
        return $this->belongsTo(EscapeRoom::class, 'room_id');
    }
}
