<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ApiTrait
{
    public function exceptionResponse(\Exception $exception)
    {
        $response = [
            'error' => 1,
            'message' => $exception->getMessage(),
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
            'code' => $exception->getCode()
        ];
        return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function apiSuccessResponse($message, array $additionalData = [], $statuCode = Response::HTTP_OK)
    {

        $data = !empty($additionalData) ?  array_merge(['error' => 0, ' message' => $message], $additionalData)
            : ['error' => 0, 'message' => $message];

        return response()->json($data, $statuCode);
    }

    public function returnWithError($message, array $messages = [], $statu = Response::HTTP_UNPROCESSABLE_ENTITY, $error = 1)
    {
        $data = count($messages) ? ['message' => $message, 'errors' => $messages, 'error' => $error] : ['message' => $message, 'error' => $error];

        return response()->json($data, $statu);
    }

    public function checkRequestKey($key, Request $request)
    {
        if (!$request->has($key)) {
            return $this->returnWithError("${key} is required field for this request");
        }
        return true;
    }

    public function apiRequestError(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error' => true,
            'errors' => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    public function isTodayBirthDay(Carbon $birthday)
    {
        $now = now();
        return ($now->format('d') == $birthday->format('d')) && ($now->format('m') == $birthday->format('m'));
    }
}
