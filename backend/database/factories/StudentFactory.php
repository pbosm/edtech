<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'  => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'cpf'   => $this->validCpf(),
        ];
    }

    private function validCpf(): string
    {
        $n = [];
        for ($i=0; $i<9; $i++) $n[] = random_int(0,9);

        $s = 0;
        for ($i=0,$w=10; $i<9; $i++,$w--) $s += $n[$i]*$w;
        $d1 = ($s % 11); $d1 = $d1 < 2 ? 0 : 11 - $d1;

        $s = 0;
        for ($i=0,$w=11; $i<9; $i++,$w--) $s += $n[$i]*$w;
        $s += $d1*2;
        $d2 = ($s % 11); $d2 = $d2 < 2 ? 0 : 11 - $d2;

        $cpf = sprintf('%d%d%d.%d%d%d.%d%d%d-%d%d',
            $n[0],$n[1],$n[2],$n[3],$n[4],$n[5],$n[6],$n[7],$n[8],$d1,$d2
        );

        return $cpf;
    }
}
