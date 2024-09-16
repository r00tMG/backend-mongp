<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Admin\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnonceResource extends JsonResource
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
            'kilos_disponibles' => $this->resource->kilos_disponibles,
            'date_depart' => $this->resource->date_depart,
            'date_arrivee' => $this->resource->date_arrivee,
            'description' => $this->resource->description,
            'origin' => $this->resource->origin,
            'destination' => $this->resource->destination,
            'created_at' => $this->resource->created_at,
            'prix_du_kilo' => $this->resource->prix_du_kilo,
            'date_now' => Carbon::now(),
            'user' => new UserResource(
                $this->resource->user
            )
        ];
    }
}
