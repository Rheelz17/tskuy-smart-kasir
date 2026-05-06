<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Tambahin validasi username, wajib diisi dan nggak boleh kembar sama user lain
            'username' => ['required', 'string', 'max:100', \Illuminate\Validation\Rule::unique(User::class)->ignore($this->user()->id)],
            // Tambahin validasi phone, boleh kosong (nullable)
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', \Illuminate\Validation\Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }
}
