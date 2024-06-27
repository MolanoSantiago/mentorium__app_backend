<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $routeName = $this->route()->getName();

        if ($routeName === 'login') {
            return [
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'password' => 'required|string|min:8|max:255',
            ];
        } elseif ($routeName === 'signup') {
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|unique:users,phone',
                'password' => 'required|string|min:8|max:255|confirmed',
            ];
        }

        return [];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'phone.unique' => 'The phone number has already been taken.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
