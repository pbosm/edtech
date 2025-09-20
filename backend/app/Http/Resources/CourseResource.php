<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\BrDates;
use App\Http\Resources\Concerns\CompletableResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    use CompletableResource, BrDates;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request): array
    {
        $data = [
            'id'   => $this->id,
            'name' => $this->name,
            'description'    => $this->description,
            'duration_hours' => $this->duration_hours,
        ];

        if ($this->complete) {
            $data += [
                'students_count' => $this->whenCounted('students'),
                'students'       => StudentResource::collection($this->whenLoaded('students')),
                'created_at'     => $this->brDate($this->created_at),
                'updated_at'     => $this->brDate($this->updated_at),
            ];
        }

        return $data;
    }
}
