<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        // Get all role names for the user and make them into an array
        $roles = $this->roles->pluck('name');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // Include other user attributes as needed
            'roles' => $roles, // Return role names directly
        ];
    }
}
