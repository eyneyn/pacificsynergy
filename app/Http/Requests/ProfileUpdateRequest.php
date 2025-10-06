<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow the request
    }

    public function rules(): array
    {
        return [
            'first_name'   => ['required', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'email'        => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id),
            ],
            // ✅ If phone is required, change to 'required' instead of 'nullable'
            'phone_number' => ['required', 'regex:/^09\d{9}$/'],
            'photo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.regex'    => 'The phone number must start with 09 and contain exactly 11 digits.',
            'photo.image'           => 'The uploaded file must be an image.',
            'photo.mimes'           => 'The photo must be a file of type: jpg, jpeg, png.',
            'photo.max'             => 'The photo must not be larger than 2MB.',
        ];
    }
}
