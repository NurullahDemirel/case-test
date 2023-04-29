<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\NewBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\EscapeRoom;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    use ApiTrait;

    //user's future reservations
    public function index()
    {
        try {
            return $this->apiSuccessResponse('', ['bookings' => auth()->user()->bookings]);
        } catch (Exception $exception) {
            $this->exceptionResponse($exception);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewBookingRequest $request)
    {
        try {
            $room_id = $request->room_id;

            $userCount = $request->user_count ?? 1;

            $room  = EscapeRoom::where('id', $room_id)->first();

            if ($userCount > $room->capacity) {
                return $this->returnWithError('This room is full');
            }

            if (!is_null(auth()->user()->birthday)) {
                $isBirthday = $this->isTodayBirthDay(auth()->user()->birthday);
            }

            $paidAmount = isset($isBirthday) && $isBirthday ? (($room->price) - ($room->price * 0.1)) : $room->price;

            $booking = DB::transaction(function () use ($request, $paidAmount, $room) {

                $booking = auth()->user()->bookings()->create(array_merge(
                    $request->all(),
                    ['paid_amount' => $paidAmount, 'user_count' => $request->user_count]
                ));

                $room->update(['capacity' => ($room->capacity - $request->user_count)]);
                return $booking;
            });

            $message = 'Your booking was created for between' . $request->enter_date . '-' . $request->exit_date;
            return $this->apiSuccessResponse($message, ['booking' => new BookingResource($booking)]);
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
                return $this->returnWithError('Booking not found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->apiSuccessResponse('', ['booking' => new BookingResource($booking)]);
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
                return $this->returnWithError('Bad request', $validator->errors()->toArray());
            }
            $booking = auth()->user()->bookings()->where('id', $id,)->first();


            if (!$booking) {
                return $this->returnWithError('This booking does not belong to you');
            }

            $enter_date = $request->enter_date;
            $exit_date = $request->exit_date;
            $room_id = $request->room_id;
            //if request same not befro info
            if (
                (Carbon::parse($enter_date) == $booking->enter_date)
                && (Carbon::parse($exit_date)->format('Y-m-d H:i:s') == $booking->exit_date->format('Y-m-d H:i:s'))
                && ($booking->room_id == $room_id)
            ) {
                return $this->apiSuccessResponse('Booking was updated.', ['booking' => new BookingResource($booking)]);
            } else {
                dd('sasa');
                //if customer want to cahnge room
                if ($room_id != $booking->room_id) {
                } else {
                    dd('sasa');
                    $rooms = $available_rooms = EscapeRoom::where('id', $room_id)->availableBetween($enter_date, $exit_date)->get();

                    if (!$rooms->count()) {
                        return $this->returnWithError('This room not available for this dates');
                    }

                    $booking->update([$request->all()]);

                    $updatedBooking = Booking::find($id);

                    return $this->apiSuccessResponse('Booking was updated.', ['booking' => $updatedBooking]);
                }
            }
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
                return $this->returnWithError('Booking not found');
            }
            $userCount = $booking->user_count;

            $booking->room->update(['capacity' => $booking->room->capacity + $userCount]);

            $booking->delete();

            return $this->apiSuccessResponse('Bookings was deleted');
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }
}
