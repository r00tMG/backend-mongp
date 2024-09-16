<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandeResource extends JsonResource
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
            'annonce' => new AnnonceResource(
                $this->resource->annonce
            ),
            'client' => new UserResource(
                $this->resource->user
            ),
            'status' => $this->resource->statut,
            'kilos_demandes' => $this->resource->kilos_demandes,
            'prix_de_la_demande' => $this->resource->prix_de_la_demande
        ];
    }
}
