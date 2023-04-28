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


    public function scopeAvailableBetween($query, $startdate, $enddate = null)
    {
        return $query->whereDoesntHave('bookings', function ($q) use ($startdate, $enddate) {
            $q->where(function ($query) use ($startdate, $enddate) {
                $query->where('enter_date', '>=', $startdate)
                    ->where('enter_date', '<', $enddate)
                    ->orWhere('exit_date', '>', $startdate)
                    ->where('exit_date', '<=', $enddate)
                    ->orWhere(function ($query) use ($startdate, $enddate) {
                        $query->where('enter_date', '<=', $startdate)
                            ->where('exit_date', '>=', $enddate);
                    });
            });
        });
    }
}
