<?php

namespace App\Http\Resources\User;

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
            'emetteur' => new UserResource(
                $this->resource->fromUser
            ),
            'recepteur' => new UserResource(
                $this->resource->toUser
            )
        ];;
    }
}
