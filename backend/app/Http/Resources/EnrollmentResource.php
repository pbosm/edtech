<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\CompletableResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Concerns\BrDates;

class EnrollmentResource extends JsonResource
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
            'id' => $this->id,
            'student' => [
                'id'    => $this->student->id,
                'name'  => $this->student->name,
                'email' => $this->student->email,
            ],
            'course' => [
                'id'   => $this->course->id,
                'name' => $this->course->name,
            ],
            'progress_percentage' => $this->progress_percentage,
            'enrollment_date'     => $this->enrollment_date,
            'completion_date'     => $this->completion_date,
            'created_at'          => $this->brDate($this->created_at),
            'updated_at'          => $this->brDate($this->updated_at),
        ];
    }
}
