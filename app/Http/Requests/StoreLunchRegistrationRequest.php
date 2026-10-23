<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLunchRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:80']];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Verrätst du uns noch deinen Namen?',
            'name.string' => 'Bitte gib einen gültigen Namen ein.',
            'name.max' => 'Dein Name darf höchstens 80 Zeichen lang sein.',
        ];
    }
}
