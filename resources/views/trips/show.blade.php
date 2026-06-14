<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ $trip->origin }} &rarr; {{ $trip->destination }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-2xl p-4">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-2xl p-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6 space-y-2">
                <div class="text-sm text-gray-500">{{ __('Conductor') }}: <span class="text-gray-900 font-medium">{{ $trip->driver->name }}</span></div>
                <div class="text-sm text-gray-500">{{ __('Salida') }}: <span class="text-gray-900 font-medium">{{ $trip->departure_at->format('d/m/Y H:i') }}</span></div>
                <div class="text-sm text-gray-500">{{ __('Cupos') }}: <span class="text-gray-900 font-medium">{{ $trip->seats_available }}/{{ $trip->seats_total }} {{ __('disponibles') }}</span></div>
                <div class="text-sm text-gray-500">{{ __('Costo') }}: <span class="text-gray-900 font-medium">{{ $trip->isFree() ? __('Gratis') : '$'.number_format($trip->cost, 2) }}</span></div>
                <div class="text-sm text-gray-500">{{ __('Estado') }}: <span class="text-gray-900 font-medium capitalize">{{ $trip->status }}</span></div>
                @if ($trip->notes)
                    <div class="text-sm text-gray-500">{{ __('Notas') }}: <span class="text-gray-900 font-medium">{{ $trip->notes }}</span></div>
                @endif
            </div>

            @if ($trip->driver_id === auth()->id())
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Solicitudes de jalón') }}</h3>

                    @if ($rideRequests->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('Nadie ha solicitado jalón todavía.') }}</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($rideRequests as $rideRequest)
                                <div class="border border-gray-100 bg-gray-50 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $rideRequest->requester->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $rideRequest->seats_requested }} {{ __('cupo(s)') }}
                                            &middot;
                                            <span class="capitalize">{{ $rideRequest->status }}</span>
                                            @if ($rideRequest->message)
                                                &middot; "{{ $rideRequest->message }}"
                                            @endif
                                        </div>
                                    </div>
                                    @if ($rideRequest->status === 'pending')
                                        <div class="flex items-center gap-2">
                                            <form method="POST" action="{{ route('ride-requests.update', $rideRequest) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="accept">
                                                <x-primary-button>{{ __('Aceptar') }}</x-primary-button>
                                            </form>
                                            <form method="POST" action="{{ route('ride-requests.update', $rideRequest) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="reject">
                                                <x-secondary-button>{{ __('Rechazar') }}</x-secondary-button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Tu solicitud') }}</h3>

                    @if ($myRequest)
                        <p class="text-sm text-gray-700 mb-4">
                            {{ __('Estado') }}: <span class="capitalize">{{ $myRequest->status }}</span>
                            &middot; {{ $myRequest->seats_requested }} {{ __('cupo(s)') }}
                        </p>

                        @if (in_array($myRequest->status, ['pending', 'accepted']))
                            <form method="POST" action="{{ route('ride-requests.update', $myRequest) }}" onsubmit="return confirm('{{ __('¿Cancelar tu solicitud?') }}')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="cancel">
                                <x-danger-button>{{ __('Cancelar solicitud') }}</x-danger-button>
                            </form>
                        @endif
                    @elseif ($trip->status === 'active' && $trip->seats_available > 0)
                        <form method="POST" action="{{ route('trips.ride-requests.store', $trip) }}" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="seats_requested" :value="__('Cupos que necesitas')" />
                                <x-text-input id="seats_requested" name="seats_requested" type="number" min="1" max="{{ $trip->seats_available }}" class="mt-1 block w-full" value="1" required />
                                <x-input-error :messages="$errors->get('seats_requested')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="message" :value="__('Mensaje (opcional)')" />
                                <x-text-input id="message" name="message" type="text" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('message')" class="mt-2" />
                            </div>
                            <x-primary-button>{{ __('Solicitar jalón') }}</x-primary-button>
                        </form>
                    @else
                        <p class="text-sm text-gray-500">{{ __('Esta ruta ya no tiene cupos disponibles.') }}</p>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
