<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Contactos') }}
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
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Agregar contacto') }}</h3>
                <form method="POST" action="{{ route('contacts.store') }}" class="flex items-start gap-3">
                    @csrf
                    <div class="flex-1">
                        <x-text-input name="email" type="email" class="block w-full" placeholder="correo@ejemplo.com" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <x-primary-button>{{ __('Enviar solicitud') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Importar contactos') }}</h3>

                <div class="space-y-3">
                    <div class="flex items-center justify-between border border-gray-100 bg-gray-50 rounded-2xl p-4">
                        <div>
                            <div class="font-semibold text-gray-900">{{ __('Contactos del teléfono') }}</div>
                            <div class="text-sm text-gray-500">{{ __('Busca a tus contactos del teléfono que ya usan Jalon.') }}</div>
                        </div>
                        <button type="button" id="import-phone-contacts" class="hidden inline-flex items-center justify-center px-5 py-2.5 bg-black border border-transparent rounded-full font-semibold text-sm text-white hover:bg-gray-800 whitespace-nowrap">
                            {{ __('Importar') }}
                        </button>
                    </div>

                    <div id="phone-contacts-results" class="space-y-3"></div>

                    <div class="flex items-center justify-between border border-gray-100 bg-gray-50 rounded-2xl p-4">
                        <div>
                            <div class="font-semibold text-gray-900">{{ __('Amigos de Facebook') }}</div>
                            <div class="text-sm text-gray-500">{{ __('Encuentra a tus amigos de Facebook que usan Jalon.') }}</div>
                        </div>
                        @if (auth()->user()->facebook_token)
                            <a href="{{ route('contacts.facebook-friends') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-black border border-transparent rounded-full font-semibold text-sm text-white hover:bg-gray-800 whitespace-nowrap">
                                {{ __('Ver amigos') }}
                            </a>
                        @else
                            <a href="{{ route('social.redirect', 'facebook') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-300 rounded-full font-semibold text-sm text-gray-900 hover:bg-gray-50 whitespace-nowrap">
                                {{ __('Vincular Facebook') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Solicitudes recibidas') }}</h3>

                @if ($received->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('No tienes solicitudes pendientes.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($received as $contact)
                            <div class="border border-gray-100 bg-gray-50 rounded-2xl p-4 flex items-center justify-between">
                                <div class="text-sm text-gray-900">{{ $contact->user->name }} ({{ $contact->user->email }})</div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('contacts.accept', $contact) }}">
                                        @csrf
                                        @method('PATCH')
                                        <x-primary-button>{{ __('Aceptar') }}</x-primary-button>
                                    </form>
                                    <form method="POST" action="{{ route('contacts.reject', $contact) }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-secondary-button>{{ __('Rechazar') }}</x-secondary-button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Solicitudes enviadas') }}</h3>

                @if ($sent->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('No has enviado solicitudes pendientes.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($sent as $contact)
                            <div class="border border-gray-100 bg-gray-50 rounded-2xl p-4 flex items-center justify-between">
                                <div class="text-sm text-gray-900">{{ $contact->contact->name }} ({{ $contact->contact->email }})</div>
                                <form method="POST" action="{{ route('contacts.destroy', $contact) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-secondary-button>{{ __('Cancelar') }}</x-secondary-button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Mis contactos') }}</h3>

                @if ($contacts->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Todavía no tienes contactos.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($contacts as $entry)
                            <div class="border border-gray-100 bg-gray-50 rounded-2xl p-4 flex items-center justify-between">
                                <div class="text-sm text-gray-900">{{ $entry['person']->name }} ({{ $entry['person']->email }})</div>
                                <form method="POST" action="{{ route('contacts.destroy', $entry['record']) }}" onsubmit="return confirm('{{ __('¿Eliminar este contacto?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button>{{ __('Eliminar') }}</x-danger-button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
