<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EscapeRoom extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'room_id')->whereDate('enter_date', '>=', now());
    }


    public function scopeAvailableBetween(Builder $query, $enter_date, $exit_date)
    {
        $enter_date = Carbon::createFromFormat('Y-m-d H:i:s', $enter_date);
        $exit_date = Carbon::createFromFormat('Y-m-d H:i:s', $exit_date);


        return $query->whereDoesntHave('bookings', function ($query) use ($enter_date, $exit_date) {
            $query->whereBetween('enter_date', [$enter_date, $exit_date])
                ->orWhereBetween('exit_date', [$enter_date, $exit_date])
                ->orWhere(function ($query) use ($enter_date, $exit_date) {
                    $query->where('enter_date', '<', $enter_date)
                        ->where('exit_date', '>', $exit_date);
                });
        });
    }
}
