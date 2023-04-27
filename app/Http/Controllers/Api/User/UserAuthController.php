<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class UserAuthController extends Controller
{
    use ApiTrait;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('login');
    }

    public function profilePage()
    {
        try {
            return  $this->apiSuccessResponse(['user' => auth()->user()]);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'repeat_password' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                return $this->returnWithMessage($validator->errors()->toArray());
            }

            $user = User::create($request->only('name', 'email', 'password'));

            $token = $user->createToken('myApp')->plainTextToken;

            return $this->apiSuccessResponse(
                [
                    'user' => $user,
                    'token' => $token
                ],
                Response::HTTP_CREATED,
            );
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function login(Request $request)
    {
        try {

            $info = $request->only('email', 'password');

            $validator = Validator::make($info, [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:10'
            ]);

            if ($validator->fails()) {
                return $this->returnWithMessage($validator->errors()->toArray());
            }

            if (Auth::attempt($info)) {
                $user = Auth::user();
                $token =  $user->createToken('myApp')->plainTextToken;

                return $this->apiSuccessResponse(['user' => $user, 'token' => $token], Response::HTTP_OK);
            } else {
                return response()->json([
                    'error' => 1,
                    'message' => 'Email or password is not true !'
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'password' => [Rule::when($this->has('password'), 'required|min:5')],
            'repeat_password' => [Rule::when($this->has('password'), 'required|same:password')]
        ]);

        if ($validator->fails()) {
            return $this->returnWithMessage($validator->errors()->toArray());
        }

        $hasPasspword =  $request->has('password');

        try {
            if ($hasPasspword) {
                auth()->user()->update([
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                    'password' => Hash::make($request->get('password')),
                ]);
            } else {
                auth()->user()->update($request->validated());
            }

            $updatedUser = User::find(auth()->id());

            return $this->apiSuccessResponse(
                ['user' => $updatedUser],
                Response::HTTP_OK,
                $hasPasspword  ? 'Your Password was updated successfully' : null
            );
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find(auth()->id());

            if (!$user) {
                return $this->returnWithMessage(['User not found', Response::HTTP_NOT_FOUND]);
            }
            $user->delete();

            return response()->json([
                'error' => 0,
                'message' => 'Your subscription was canceled'
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {

            return $this->exceptionResponse($exception);
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'error' => 0,
            'message' => 'You were logged out successfully'
        ], Response::HTTP_OK);
    }
}
