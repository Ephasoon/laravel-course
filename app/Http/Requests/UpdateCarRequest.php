<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->route('car')?->user_id === $this->user()?->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'maker_id' => ['required', 'integer', 'exists:makers,id'],
            'model_id' => [
                'required',
                'integer',
                'exists:models,id',
                Rule::exists('models', 'id')->where('maker_id', $this->input('maker_id')),
            ],
            'year' => ['required', 'integer', 'between:1950,'.(now()->year + 1)],
            'car_type_id' => ['required', 'integer', 'exists:car_types,id'],
            'fuel_type_id' => ['required', 'integer', 'exists:fuel_types,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'price' => ['required', 'integer', 'min:1'],
            'mileage' => ['required', 'integer', 'min:0'],
            'vin' => ['required', 'string', 'max:17', Rule::unique('cars', 'vin')->ignore($this->route('car'))],
            'phone' => ['required', 'string', 'regex:/^(\+2519\d{8}|09\d{8})$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'published' => ['boolean'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer'],
            'abs' => ['boolean'],
            'air_conditioning' => ['boolean'],
            'power_windows' => ['boolean'],
            'power_door_locks' => ['boolean'],
            'cruise_control' => ['boolean'],
            'bluetooth_connectivity' => ['boolean'],
            'remote_start' => ['boolean'],
            'gps_navigation' => ['boolean'],
            'heated_seats' => ['boolean'],
            'climate_control' => ['boolean'],
            'rear_parking_sensors' => ['boolean'],
            'leather_seats' => ['boolean'],
        ];
    }
}
