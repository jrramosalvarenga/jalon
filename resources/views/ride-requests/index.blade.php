<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Solicitudes de jalón') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-2xl p-4">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Recibidas') }}</h3>

                @if ($received->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('No has recibido solicitudes.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($received as $rideRequest)
                            <div class="border border-gray-100 bg-gray-50 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
                                <div class="text-sm text-gray-900">
                                    <a href="{{ route('trips.show', $rideRequest->trip) }}" class="font-semibold hover:text-gray-600">
                                        {{ $rideRequest->trip->origin }} &rarr; {{ $rideRequest->trip->destination }}
                                    </a>
                                    <div class="text-gray-500">
                                        {{ $rideRequest->requester->name }} &middot; {{ $rideRequest->seats_requested }} {{ __('cupo(s)') }}
                                        &middot; <span class="capitalize">{{ $rideRequest->status }}</span>
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

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Enviadas') }}</h3>

                @if ($sent->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('No has enviado solicitudes.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($sent as $rideRequest)
                            <div class="border border-gray-100 bg-gray-50 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
                                <div class="text-sm text-gray-900">
                                    <a href="{{ route('trips.show', $rideRequest->trip) }}" class="font-semibold hover:text-gray-600">
                                        {{ $rideRequest->trip->origin }} &rarr; {{ $rideRequest->trip->destination }}
                                    </a>
                                    <div class="text-gray-500">
                                        {{ __('Conduce') }} {{ $rideRequest->trip->driver->name }} &middot; {{ $rideRequest->seats_requested }} {{ __('cupo(s)') }}
                                        &middot; <span class="capitalize">{{ $rideRequest->status }}</span>
                                    </div>
                                </div>
                                @if (in_array($rideRequest->status, ['pending', 'accepted']))
                                    <form method="POST" action="{{ route('ride-requests.update', $rideRequest) }}" onsubmit="return confirm('{{ __('¿Cancelar tu solicitud?') }}')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="cancel">
                                        <x-danger-button>{{ __('Cancelar') }}</x-danger-button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
