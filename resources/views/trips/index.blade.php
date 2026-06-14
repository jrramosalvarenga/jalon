<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                {{ __('Rutas') }}
            </h2>
            <a href="{{ route('trips.create') }}" class="inline-flex items-center px-5 py-2.5 bg-black border border-transparent rounded-full font-semibold text-sm text-white hover:bg-gray-800">
                {{ __('Publicar ruta') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-2xl p-4">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Mis rutas') }}</h3>

                @if ($myTrips->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Todavía no has publicado ninguna ruta.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($myTrips as $trip)
                            <div class="border border-gray-100 bg-gray-50 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $trip->origin }} &rarr; {{ $trip->destination }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $trip->departure_at->format('d/m/Y H:i') }}
                                        &middot;
                                        {{ $trip->seats_available }}/{{ $trip->seats_total }} {{ __('cupos') }}
                                        &middot;
                                        {{ $trip->isFree() ? __('Gratis') : '$'.number_format($trip->cost, 2) }}
                                        &middot;
                                        <span class="capitalize">{{ $trip->status }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('trips.show', $trip) }}" class="text-sm font-semibold text-black hover:text-gray-600">{{ __('Ver') }}</a>
                                    <a href="{{ route('trips.edit', $trip) }}" class="text-sm font-semibold text-black hover:text-gray-600">{{ __('Editar') }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Rutas de tus contactos') }}</h3>

                @if ($feed->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Ninguno de tus contactos tiene rutas próximas.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($feed as $trip)
                            <div class="border border-gray-100 bg-gray-50 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $trip->origin }} &rarr; {{ $trip->destination }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ __('Conduce') }} {{ $trip->driver->name }}
                                        &middot;
                                        {{ $trip->departure_at->format('d/m/Y H:i') }}
                                        &middot;
                                        {{ $trip->seats_available }}/{{ $trip->seats_total }} {{ __('cupos') }}
                                        &middot;
                                        {{ $trip->isFree() ? __('Gratis') : '$'.number_format($trip->cost, 2) }}
                                    </div>
                                </div>
                                <a href="{{ route('trips.show', $trip) }}" class="text-sm font-semibold text-black hover:text-gray-600">{{ __('Ver') }}</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
