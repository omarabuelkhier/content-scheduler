<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'content'        => $this->content,
            'image_url'      => $this->image_url,
            'scheduled_time' => $this->scheduled_time,
            'status'         => $this->status,
            'user_id'        => $this->user_id,
            'platforms'      => $this->platforms->map(function ($platform) {
                return [
                    'id'   => $platform->id,
                    'name' => $platform->name,
                    'type' => $platform->type,
                ];
            }),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
