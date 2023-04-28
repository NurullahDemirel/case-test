<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\EscapeRoom;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    use ApiTrait;

    //user's future reservations
    public function index()
    {
        try {
            return $this->apiSuccessResponse([
                'bookings' => auth()->user()->bookings
            ]);
        } catch (Exception $exception) {
            $this->exceptionResponse($exception);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'room_id' => 'required|exists:escape_rooms,id',
                'enter_date' => ['required', 'date', 'before_or_equal:exit_date', 'date_format:Y-m-d H:i:s'],
                'exit_date' => ['required', 'date', 'after_or_equal:enter_date', 'date_format:Y-m-d H:i:s']
            ]);

            if ($validator->fails()) {
                return $this->returnWithMessage($validator->errors()->toArray());
            }
            $enter_date = $request->enter_date;
            $exit_date = $request->exit_date;
            $room_id = $request->room_id;

            $rooms = $available_rooms = EscapeRoom::where('id', $room_id)->availableBetween($enter_date, $exit_date)->get();

            if (!$rooms->count()) {
                return $this->returnWithMessage([
                    'This room not available for this dates'
                ]);
            }

            $booking = auth()->user()->bookings()->create($request->all());

            return $this->apiSuccessResponse([
                'booking' => $booking,
                'message' => 'Your booking was created for between' . $request->enter_date . '-' . $request->exit_date
            ]);
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $booking = auth()->user()->bookings()->where('id', $id,)->first();

            if (!$booking) {
                return $this->returnWithMessage([
                    'Booking not found'
                ]);
            }

            return $this->apiSuccessResponse([
                'booking' => $booking
            ]);
        } catch (Exception $exception) {
            $this->exceptionResponse($exception);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'room_id' => 'required|exists:escape_rooms,id',
                'enter_date' => ['required', 'date', 'before_or_equal:exit_date', 'date_format:Y-m-d H:i:s'],
                'exit_date' => ['required', 'date', 'after_or_equal:enter_date', 'date_format:Y-m-d H:i:s']
            ]);

            if ($validator->fails()) {
                return $this->returnWithMessage($validator->errors()->toArray());
            }
            $booking = auth()->user()->bookings()->where('id', $id,)->first();


            if (!$booking) {
                return $this->returnWithMessage([
                    'This booking does not belong to you'
                ]);
            }

            $enter_date = $request->enter_date;
            $exit_date = $request->exit_date;
            $room_id = $request->room_id;
            //if request same not befro info
            if (!(Carbon::parse($enter_date) == $booking->enter_date) && !(Carbon::parse($exit_date) == $booking->exit_date) && !($booking->room_id == $room_id)) {
                $rooms = $available_rooms = EscapeRoom::where('id', $room_id)->availableBetween($enter_date, $exit_date)->get();

                if (!$rooms->count()) {
                    return $this->returnWithMessage([
                        'This room not available for this dates'
                    ]);
                }

                $booking->update([$request->all()]);

                $updatedBooking = Booking::find($id);

                return $this->apiSuccessResponse([
                    'booking' => $updatedBooking,
                    'message' => 'Booking was updated.'
                ]);
            }
            return $this->apiSuccessResponse([
                'booking' => $booking,
                'message' => 'Booking was updated.'
            ]);
        } catch (Exception $exception) {
            $this->exceptionResponse($exception);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {


        try {
            $booking = auth()->user()->bookings()->where('id', $id,)->first();

            if (!$booking) {
                return $this->returnWithMessage([
                    'Booking not found'
                ]);
            }
            $booking->delete();

            return $this->apiSuccessResponse([
                'Bookings was deleted'
            ]);
        } catch (Exception $exception) {
            $this->exceptionResponse($exception);
        }
    }
}
