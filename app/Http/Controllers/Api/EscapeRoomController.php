<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoomControllerRequest;
use App\Http\Resources\RoomResource;
use App\Models\EscapeRoom;
use App\Traits\ApiTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class EscapeRoomController extends Controller
{

    use ApiTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return $this->apiSuccessResponse('', ['rooms' => RoomResource::collection(EscapeRoom::all())]);
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            //any one can this if has neccessary role
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
            //code...
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            ////any one can this if has neccessary role
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            ////any one can this if has neccessary role
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function checkRoomsBySlot($id, $timeSlot)
    {
        try {
            $dates = explode('between', $timeSlot);
            $startDate = Carbon::parse($dates[0]);
            $finishDate = Carbon::parse($dates[1]);

            if (!($finishDate->greaterThan($startDate))) {
                return $this->apiSuccessResponse('Finish date must be grather than start date');
            }

            $finishDate = $finishDate->format('Y-m-d H:i:s');
            $startDate = $startDate->format('Y-m-d H:i:s');

            return $this->apiSuccessResponse('', ['rooms' => RoomResource::collection(EscapeRoom::where('id', $id)->availableBetween($startDate, $finishDate)->get())]);
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }
}
