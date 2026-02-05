<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
        ]);
    }
}
