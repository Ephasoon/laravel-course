<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'price' => $this->price,
            'mileage' => $this->mileage,
            'vin' => $this->vin,
            'phone' => $this->phone,
            'address' => $this->address,
            'description' => $this->description,
            'published_at' => $this->published_at,
            'maker' => $this->whenLoaded('maker', fn () => $this->maker->name),
            'model' => $this->whenLoaded('model', fn () => $this->model->name),
            'car_type' => $this->whenLoaded('carType', fn () => $this->carType->name),
            'fuel_type' => $this->whenLoaded('fuelType', fn () => $this->fuelType->name),
            'city' => $this->whenLoaded('city', fn () => $this->city->name),
            'state' => $this->whenLoaded('city', fn () => $this->city->state->name),
            'primary_image' => $this->whenLoaded('primaryImage', fn () => $this->primaryImage?->image_path),
            'images' => $this->whenLoaded('images', fn () => CarImageResource::collection($this->images)),
            'features' => $this->whenLoaded('features', fn () => $this->features ? new CarFeaturesResource($this->features) : null),
            'owner' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
        ];
    }
}
