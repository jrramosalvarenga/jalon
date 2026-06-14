<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Amigos de Facebook en Jalon') }}
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
                @if ($friends->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Ninguno de tus amigos de Facebook usa Jalon todavía.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($friends as $friend)
                            <div class="border border-gray-100 bg-gray-50 rounded-2xl p-4 flex items-center justify-between">
                                <div class="text-sm text-gray-900">
                                    <div class="font-semibold">{{ $friend->name }}</div>
                                    <div class="text-gray-500">{{ $friend->email }}</div>
                                </div>
                                <form method="POST" action="{{ route('contacts.request-user', $friend) }}">
                                    @csrf
                                    <x-primary-button>{{ __('Enviar solicitud') }}</x-primary-button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <a href="{{ route('contacts.index') }}" class="text-sm font-semibold text-black hover:text-gray-600">
                &larr; {{ __('Volver a contactos') }}
            </a>

        </div>
    </div>
</x-app-layout>
