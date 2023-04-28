<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class NewBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'room_id' => 'required|exists:escape_rooms,id',
            'enter_date' => ['required', 'date', 'before:exit_date', 'date_format:Y-m-d H:i:s'],
            'user_count' => ['required', 'numeric'],
        ];

        if (request()->has('exit_date')) {
            $rules['exit_date'] = ['required', 'date', 'after:enter_date', 'date_format:Y-m-d H:i:s'];
        }
        return $rules;
    }
}
