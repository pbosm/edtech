<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollmentStoreRequest extends FormRequest
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
            'student_id'          => ['required', 'integer', 'exists:students,id'],
            'course_id'           => ['required', 'integer', 'exists:courses,id'],
            'enrollment_date'     => ['required', 'date'],
            'progress_percentage' => ['nullable', 'integer', 'between:0,100'],
            'completion_date'     => ['nullable', 'date', 'after_or_equal:enrollment_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required'          => 'O campo :attribute é obrigatório.',
            'student_id.integer'           => 'O campo :attribute deve ser um número inteiro.',
            'student_id.exists'            => 'O :attribute informado não foi encontrado.',

            'course_id.required'           => 'O campo :attribute é obrigatório.',
            'course_id.integer'            => 'O campo :attribute deve ser um número inteiro.',
            'course_id.exists'             => 'O :attribute informado não foi encontrado.',

            'enrollment_date.required'     => 'A :attribute é obrigatória.',
            'enrollment_date.date'         => 'A :attribute deve ser uma data válida.',

            'progress_percentage.integer'  => 'O campo :attribute deve ser um número inteiro.',
            'progress_percentage.between'  => 'O campo :attribute deve estar entre :min e :max.',

            'completion_date.date'         => 'A :attribute deve ser uma data válida.',
            'completion_date.after_or_equal' => 'A :attribute deve ser posterior ou igual à data de matrícula.',
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id'          => 'aluno',
            'course_id'           => 'curso',
            'enrollment_date'     => 'data de matrícula',
            'progress_percentage' => 'progresso (%)',
            'completion_date'     => 'data de conclusão',
        ];
    }
}
