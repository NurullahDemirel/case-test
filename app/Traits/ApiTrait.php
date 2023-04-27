<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

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

    public function apiSuccessResponse($data = null, $statuCode = Response::HTTP_OK, $message = null)
    {
        $basicData = ['error' => false];

        if ($data) {
            $basicData['data'] = $data;
        }

        if ($message) {
            $basicData['message']  = $message;
        }

        return response()->json($basicData, $statuCode);
    }

    public function returnWithMessage(array $messages, $statu = Response::HTTP_UNPROCESSABLE_ENTITY, $error = 1)
    {
        return response()->json([
            'error' => $error,
            'messages' => $messages,
        ], $statu);
    }

    public function checkRequestKey($key, Request $request)
    {
        if (!$request->has($key)) {
            return $this->returnWithMessage("${key} is required field for this request");
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
}
