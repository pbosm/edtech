<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
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
        $isCreate = $this->isMethod('post');

        return [
            'name'           => array_merge($isCreate ? ['required'] : ['sometimes','required'], ['string','max:255']),
            'description'    => $isCreate ? ['nullable','string','max:65535'] : ['sometimes','nullable','string','max:65535'],
            'duration_hours' => array_merge($isCreate ? ['required'] : ['sometimes','required'], ['integer','min:1']
            ),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'O campo :attribute é obrigatório.',
            'name.string'    => 'O campo :attribute deve ser um texto.',
            'name.max'       => 'O campo :attribute não pode ter mais que :max caracteres.',

            'description.required' => 'A :attribute é obrigatória.',
            'description.string'   => 'A :attribute deve ser um texto.',
            'description.max'      => 'A :attribute não pode ter mais que :max caracteres.',

            'duration_hours.required' => 'O campo :attribute é obrigatório.',
            'duration_hours.integer'  => 'O campo :attribute deve ser um número inteiro.',
            'duration_hours.min'      => 'O campo :attribute deve ser pelo menos :min.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'           => 'nome',
            'description'    => 'descrição',
            'duration_hours' => 'duração (horas)',
        ];
    }
}
