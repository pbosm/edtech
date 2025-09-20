<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\BrDates;
use App\Http\Resources\Concerns\CompletableResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    use CompletableResource, BrDates;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'cpf'           => $this->cpf,
            'courses_count' => $this->whenCounted('courses'),
        ];
    }
}
