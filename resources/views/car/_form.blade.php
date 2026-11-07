@php
    $isEdit = isset($car);
    $features = $isEdit ? $car->features : null;
    $lastYear = now()->year + 1;
@endphp

        <form
          action="{{ $isEdit ? route('car.update', $car) : route('car.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="card add-new-car-form"
        >
          @csrf
          @if($isEdit)
            @method('PUT')
          @endif
          <div class="form-content">
            <div class="form-details">
              <div class="row">
                <div class="col">
                  <div class="form-group @error('maker_id') has-error @enderror">
                    <label>Maker</label>
                    <select id="makerSelect" name="maker_id">
                      <option value="">Maker</option>
                      @foreach($makers as $maker)
                        <option value="{{ $maker->id }}" @selected((string) old('maker_id', $car->maker_id ?? '') === (string) $maker->id)>
                          {{ $maker->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('maker_id')
                      <p class="error-message">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col">
                  <div class="form-group @error('model_id') has-error @enderror">
                    <label>Model</label>
                    <select id="modelSelect" name="model_id">
                      <option value="">Model</option>
                      @foreach($models as $model)
                        <option
                          value="{{ $model->id }}"
                          data-parent="{{ $model->maker_id }}"
                          @selected((string) old('model_id', $car->model_id ?? '') === (string) $model->id)
                        >
                          {{ $model->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('model_id')
                      <p class="error-message">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col">
                  <div class="form-group @error('year') has-error @enderror">
                    <label>Year</label>
                    <select name="year">
                      <option value="">Year</option>
                      @for($year = $lastYear; $year >= 1950; $year--)
                        <option value="{{ $year }}" @selected((string) old('year', $car->year ?? '') === (string) $year)>{{ $year }}</option>
                      @endfor
                    </select>
                    @error('year')
                      <p class="error-message">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
              </div>
              <div class="form-group @error('car_type_id') has-error @enderror">
                <label>Car Type</label>
                <select name="car_type_id">
                  <option value="">Car Type</option>
                  @foreach($carTypes as $carType)
                    <option value="{{ $carType->id }}" @selected((string) old('car_type_id', $car->car_type_id ?? '') === (string) $carType->id)>
                      {{ $carType->name }}
                    </option>
                  @endforeach
                </select>
                @error('car_type_id')
                  <p class="error-message">{{ $message }}</p>
                @enderror
              </div>
              <div class="row">
                <div class="col">
                  <div class="form-group @error('price') has-error @enderror">
                    <label>Price</label>
                    <input type="number" name="price" placeholder="Price" value="{{ old('price', $car->price ?? '') }}" />
                    @error('price')
                      <p class="error-message">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col">
                  <div class="form-group @error('vin') has-error @enderror">
                    <label>Vin Code</label>
                    <input name="vin" placeholder="Vin Code" value="{{ old('vin', $car->vin ?? '') }}" />
                    @error('vin')
                      <p class="error-message">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col">
                  <div class="form-group @error('mileage') has-error @enderror">
                    <label>Mileage (ml)</label>
                    <input type="number" name="mileage" placeholder="Mileage" value="{{ old('mileage', $car->mileage ?? '') }}" />
                    @error('mileage')
                      <p class="error-message">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
              </div>
              <div class="form-group @error('fuel_type_id') has-error @enderror">
                <label>Fuel Type</label>
                <select name="fuel_type_id">
                  <option value="">Fuel Type</option>
                  @foreach($fuelTypes as $fuelType)
                    <option value="{{ $fuelType->id }}" @selected((string) old('fuel_type_id', $car->fuel_type_id ?? '') === (string) $fuelType->id)>
                      {{ $fuelType->name }}
                    </option>
                  @endforeach
                </select>
                @error('fuel_type_id')
                  <p class="error-message">{{ $message }}</p>
                @enderror
              </div>
              <div class="row">
                <div class="col">
                  <div class="form-group @error('state_id') has-error @enderror">
                    <label>State/Region</label>
                    <select id="stateSelect" name="state_id">
                      <option value="">State/Region</option>
                      @foreach($states as $state)
                        <option value="{{ $state->id }}" @selected((string) old('state_id', $car->city?->state_id ?? '') === (string) $state->id)>
                          {{ $state->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('state_id')
                      <p class="error-message">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col">
                  <div class="form-group @error('city_id') has-error @enderror">
                    <label>City</label>
                    <select id="citySelect" name="city_id">
                      <option value="">City</option>
                      @foreach($cities as $city)
                        <option
                          value="{{ $city->id }}"
                          data-parent="{{ $city->state_id }}"
                          @selected((string) old('city_id', $car->city_id ?? '') === (string) $city->id)
                        >
                          {{ $city->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('city_id')
                      <p class="error-message">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="form-group @error('address') has-error @enderror">
                    <label>Address</label>
                    <input name="address" placeholder="Address" value="{{ old('address', $car->address ?? '') }}" />
                    @error('address')
                      <p class="error-message">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col">
                  <div class="form-group @error('phone') has-error @enderror">
                    <label>Phone</label>
                    <input name="phone" placeholder="Phone" value="{{ old('phone', $car->phone ?? '') }}" />
                    @error('phone')
                      <p class="error-message">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col">
                    <label class="checkbox">
                      <input
                        type="checkbox"
                        name="air_conditioning"
                        value="1"
                        @checked(old('air_conditioning', $features?->air_conditioning ?? false))
                      />
                      Air Conditioning
                    </label>
                    @error('air_conditioning')
                      <span class="text-error">{{ $message }}</span>
                    @enderror

                    <label class="checkbox">
                      <input type="checkbox" name="power_windows" value="1" @checked(old('power_windows', $features?->power_windows ?? false)) />
                      Power Windows
                    </label>
                    @error('power_windows')
                      <span class="text-error">{{ $message }}</span>
                    @enderror

                    <label class="checkbox">
                      <input
                        type="checkbox"
                        name="power_door_locks"
                        value="1"
                        @checked(old('power_door_locks', $features?->power_door_locks ?? false))
                      />
                      Power Door Locks
                    </label>
                    @error('power_door_locks')
                      <span class="text-error">{{ $message }}</span>
                    @enderror

                    <label class="checkbox">
                      <input type="checkbox" name="abs" value="1" @checked(old('abs', $features?->abs ?? false)) />
                      ABS
                    </label>
                    @error('abs')
                      <span class="text-error">{{ $message }}</span>
                    @enderror

                    <label class="checkbox">
                      <input type="checkbox" name="cruise_control" value="1" @checked(old('cruise_control', $features?->cruise_control ?? false)) />
                      Cruise Control
                    </label>
                    @error('cruise_control')
                      <span class="text-error">{{ $message }}</span>
                    @enderror

                    <label class="checkbox">
                      <input
                        type="checkbox"
                        name="bluetooth_connectivity"
                        value="1"
                        @checked(old('bluetooth_connectivity', $features?->bluetooth_connectivity ?? false))
                      />
                      Bluetooth Connectivity
                    </label>
                    @error('bluetooth_connectivity')
                      <span class="text-error">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="col">
                    <label class="checkbox">
                      <input type="checkbox" name="remote_start" value="1" @checked(old('remote_start', $features?->remote_start ?? false)) />
                      Remote Start
                    </label>
                    @error('remote_start')
                      <span class="text-error">{{ $message }}</span>
                    @enderror

                    <label class="checkbox">
                      <input type="checkbox" name="gps_navigation" value="1" @checked(old('gps_navigation', $features?->gps_navigation ?? false)) />
                      GPS Navigation System
                    </label>
                    @error('gps_navigation')
                      <span class="text-error">{{ $message }}</span>
                    @enderror

                    <label class="checkbox">
                      <input type="checkbox" name="heated_seats" value="1" @checked(old('heated_seats', $features?->heated_seats ?? false)) />
                      Heated Seats
                    </label>
                    @error('heated_seats')
                      <span class="text-error">{{ $message }}</span>
                    @enderror

                    <label class="checkbox">
                      <input type="checkbox" name="climate_control" value="1" @checked(old('climate_control', $features?->climate_control ?? false)) />
                      Climate Control
                    </label>
                    @error('climate_control')
                      <span class="text-error">{{ $message }}</span>
                    @enderror

                    <label class="checkbox">
                      <input
                        type="checkbox"
                        name="rear_parking_sensors"
                        value="1"
                        @checked(old('rear_parking_sensors', $features?->rear_parking_sensors ?? false))
                      />
                      Rear Parking Sensors
                    </label>
                    @error('rear_parking_sensors')
                      <span class="text-error">{{ $message }}</span>
                    @enderror

                    <label class="checkbox">
                      <input type="checkbox" name="leather_seats" value="1" @checked(old('leather_seats', $features?->leather_seats ?? false)) />
                      Leather Seats
                    </label>
                    @error('leather_seats')
                      <span class="text-error">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
              <div class="form-group @error('description') has-error @enderror">
                <label>Detailed Description</label>
                <textarea rows="10" name="description">{{ old('description', $car->description ?? '') }}</textarea>
                @error('description')
                  <p class="error-message">{{ $message }}</p>
                @enderror
              </div>
              <div class="form-group @error('published') has-error @enderror">
                <label class="checkbox">
                  <input
                    type="checkbox"
                    name="published"
                    value="1"
                    @checked(old('published', $isEdit ? (bool) $car->published_at : false))
                  />
                  Published
                </label>
                @error('published')
                  <p class="error-message">{{ $message }}</p>
                @enderror
              </div>
            </div>
            <div class="form-images">
              <div class="form-image-upload">
                <div class="upload-placeholder">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    style="width: 48px"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                    />
                  </svg>
                </div>
                <input id="carFormImageUpload" type="file" name="images[]" multiple accept="image/*" />
              </div>
              <div id="imagePreviews" class="car-form-images"></div>
              @error('images')
                <span class="text-error">{{ $message }}</span>
              @enderror
              @error('images.*')
                <span class="text-error">{{ $message }}</span>
              @enderror
            </div>
          </div>
          <div class="p-medium" style="width: 100%">
            <div class="flex justify-end gap-1">
              <button type="button" class="btn btn-default">Reset</button>
              <button class="btn btn-primary">Submit</button>
            </div>
          </div>
        </form>
