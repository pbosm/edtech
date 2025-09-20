<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'progress_percentage' => ['required','integer','between:0,100'],
            'completion_date'     => ['nullable','date'],
        ];
    }

    public function messages(): array
    {
        return [
            'progress_percentage.required' => 'O campo :attribute é obrigatório.',
            'progress_percentage.integer'  => 'O campo :attribute deve ser um número inteiro.',
            'progress_percentage.between'  => 'O campo :attribute deve estar entre :min e :max.',

            'completion_date.date'         => 'A :attribute deve ser uma data válida.',
        ];
    }

    public function attributes(): array
    {
        return [
            'progress_percentage' => 'progresso (%)',
            'completion_date'     => 'data de conclusão',
        ];
    }
}
