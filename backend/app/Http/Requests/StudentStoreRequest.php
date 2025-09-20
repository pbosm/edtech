<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CpfBr;

class StudentStoreRequest extends FormRequest
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
            'name'  => ['bail','required','string','max:255'],
            'email' => ['bail','required','email','max:255','unique:students,email'],
            'cpf'   => ['bail','required','string','size:14', new CpfBr, 'unique:students,cpf'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'O campo :attribute é obrigatório.',
            'name.string'    => 'O campo :attribute deve ser um texto.',
            'name.max'       => 'O campo :attribute não pode ter mais que :max caracteres.',

            'email.required' => 'O campo :attribute é obrigatório.',
            'email.email'    => 'Informe um :attribute válido.',
            'email.max'      => 'O campo :attribute não pode ter mais que :max caracteres.',
            'email.unique'   => 'Já existe um aluno com este :attribute.',

            'cpf.required'   => 'O campo :attribute é obrigatório.',
            'cpf.string'     => 'O campo :attribute deve ser um texto.',
            'cpf.size'       => 'O campo :attribute deve estar no formato XXX.XXX.XXX-XX (14 caracteres).',
            'cpf.unique'     => 'Já existe um aluno com este :attribute.',
            'cpf.cpf_br'     => 'O :attribute informado é inválido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'  => 'nome',
            'email' => 'e-mail',
            'cpf'   => 'CPF',
        ];
    }
}
