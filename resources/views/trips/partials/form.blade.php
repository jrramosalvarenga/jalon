@php
    $old = fn ($field, $default = null) => old($field, $trip?->{$field} ?? $default);
@endphp

<div>
    <x-input-label for="origin" :value="__('Origen')" />
    <x-text-input id="origin" name="origin" type="text" class="mt-1 block w-full" :value="$old('origin')" placeholder="{{ __('Escribe una ciudad o lugar...') }}" autocomplete="off" required autofocus />
    <input type="hidden" id="origin_lat" name="origin_lat" value="{{ $old('origin_lat') }}">
    <input type="hidden" id="origin_lng" name="origin_lng" value="{{ $old('origin_lng') }}">
    <x-input-error :messages="$errors->get('origin')" class="mt-2" />
</div>

<div>
    <x-input-label for="destination" :value="__('Destino')" />
    <x-text-input id="destination" name="destination" type="text" class="mt-1 block w-full" :value="$old('destination')" placeholder="{{ __('Escribe una ciudad o lugar...') }}" autocomplete="off" required />
    <input type="hidden" id="destination_lat" name="destination_lat" value="{{ $old('destination_lat') }}">
    <input type="hidden" id="destination_lng" name="destination_lng" value="{{ $old('destination_lng') }}">
    <x-input-error :messages="$errors->get('destination')" class="mt-2" />
</div>

<div>
    <x-input-label for="departure_at" :value="__('Fecha y hora de salida')" />
    <x-text-input id="departure_at" name="departure_at" type="datetime-local" class="mt-1 block w-full"
        :value="old('departure_at', $trip?->departure_at?->format('Y-m-d\TH:i'))" required />
    <x-input-error :messages="$errors->get('departure_at')" class="mt-2" />
</div>

<div>
    <x-input-label for="seats_total" :value="__('Cupos disponibles')" />
    <x-text-input id="seats_total" name="seats_total" type="number" min="1" max="50" class="mt-1 block w-full" :value="$old('seats_total', 1)" required />
    <x-input-error :messages="$errors->get('seats_total')" class="mt-2" />
</div>

<div>
    <x-input-label for="cost" :value="__('Costo por persona')" />
    <x-text-input id="cost" name="cost" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="$old('cost')" placeholder="0.00" />
    <p class="text-sm text-gray-500 mt-1">{{ __('Déjalo vacío o en 0 si el jalón es gratis.') }}</p>
    <x-input-error :messages="$errors->get('cost')" class="mt-2" />
</div>

<div>
    <x-input-label for="notes" :value="__('Notas (opcional)')" />
    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-black focus:ring-black rounded-xl shadow-sm px-4 py-3">{{ $old('notes') }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>
