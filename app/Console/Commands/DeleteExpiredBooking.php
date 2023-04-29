<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteExpiredBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookings = Booking::with('room')->whereDate('exit_date', '<', now())->get();

        foreach ($bookings as $booking) {
            DB::transaction(function () use ($booking) {
                $booking->room->update([
                    'capacity' => $booking->room->capacity + $booking->user_count
                ]);
                $booking->delete();
            });

            //send email to the customer for info
        }
    }
}
