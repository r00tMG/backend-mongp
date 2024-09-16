<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->resource->id,
            'address' => $this->resource->address,
            'hobbies' => $this->resource->hobbies,
            'job' => $this->resource->job,
            'skill' => $this->resource->skill,
            'user' => new UserResource(
                $this->resource->user
            )
        ];
    }
}
