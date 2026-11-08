<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarFeaturesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'abs' => $this->abs,
            'air_conditioning' => $this->air_conditioning,
            'power_windows' => $this->power_windows,
            'power_door_locks' => $this->power_door_locks,
            'cruise_control' => $this->cruise_control,
            'bluetooth_connectivity' => $this->bluetooth_connectivity,
            'remote_start' => $this->remote_start,
            'gps_navigation' => $this->gps_navigation,
            'heated_seats' => $this->heated_seats,
            'climate_control' => $this->climate_control,
            'rear_parking_sensors' => $this->rear_parking_sensors,
            'leather_seats' => $this->leather_seats,
        ];
    }
}
