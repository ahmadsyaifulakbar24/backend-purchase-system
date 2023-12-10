<?php

namespace App\Http\Resources\ActivityLog;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::find($this->causer_id);
        return [
            'id' => $this->id,
            'log_name' => $this->log_name,
            'description' => $this->description,
            'properties' => $this->properties,
            'user' => ($user) ? $user->name : null,
            'ip' => $this->ip,
            'browser' => $this->browser,
            'os' => $this->os,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
