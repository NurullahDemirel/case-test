<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    use ApiTrait;

    public function loginAsAdmin(Request $request)
    {

        try {
            $info = $request->only('email', 'password');

            $validator = Validator::make($info, [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:10'
            ]);

            if ($validator->fails()) {
                return $this->returnWithError('Validation error', $validator->errors()->toArray());
            }

            if (Auth::attempt($info)) {
                if (!auth()->user()->hasRole('Admin')) {
                    return $this->returnWithError('Please try with admin info!', [], Response::HTTP_FORBIDDEN);
                }
                return $this->apiSuccessResponse('Login was success', ['user' => auth()->user(), 'token' => auth()->user()->createToken('myApp')->plainTextToken], Response::HTTP_OK);
            }
            return $this->returnWithError(['Email or password is no true!']);
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }
}
