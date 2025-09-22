<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfBr implements ValidationRule
{
    /**
     * Valida CPF com dígitos verificadores.
     *
     * Aceita o valor mascarado (XXX.XXX.XXX-XX) ou só dígitos.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $raw = (string) $value;
        // só dígitos
        $cpf = preg_replace('/\D/', '', $raw) ?? '';

        // 11 dígitos
        if (strlen($cpf) !== 11) {
            $fail('O :attribute informado é inválido.');
            return;
        }

        // rejeita sequências repetidas
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            $fail('O :attribute informado é inválido.');
            return;
        }

        // calcula DV
        $dv = function (string $base, int $factor) {
            $sum = 0;
            for ($i = 0; $i < strlen($base); $i++) {
                $sum += intval($base[$i]) * ($factor - $i);
            }
            $rest = $sum % 11;
            return $rest < 2 ? 0 : 11 - $rest;
        };

        $dv1 = $dv(substr($cpf, 0, 9), 10);
        if ($dv1 !== intval($cpf[9])) {
            $fail('O :attribute informado é inválido.');
            return;
        }

        $dv2 = $dv(substr($cpf, 0, 10), 11);
        if ($dv2 !== intval($cpf[10])) {
            $fail('O :attribute informado é inválido.');
            return;
        }
    }
}
