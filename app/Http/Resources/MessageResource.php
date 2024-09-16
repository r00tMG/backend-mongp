<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'contenu' => $this->resource->contenu,
            'date_envoi' => $this->resource->date_envoi,
            'emetteur_id' => new UserResource($this->resource->emetteur_id),
            'recepteur_id' => new UserResource($this->resource->recepteur_id),
        ];
    }
}
