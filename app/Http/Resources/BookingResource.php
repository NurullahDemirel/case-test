<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'enter_date' => $this->enter_date->format('Y-m-d H:i:s'),
            'exit_date' => $this->exit_date?->format('Y-m-d H:i:s'),
            'room' => new RoomResource($this->room),
        ];

        if ($this->apply_discount_percentage > 0) {
            $data['apply_discount_percentage'] = '-%' . $this->apply_discount_percentage;
        }
        return $data;
    }
}
