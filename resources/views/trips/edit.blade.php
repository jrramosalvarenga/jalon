<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Editar ruta') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                <form method="POST" action="{{ route('trips.update', $trip) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('trips.partials.form', ['trip' => $trip])

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Guardar cambios') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                <h3 class="text-lg font-bold text-red-700 mb-2">{{ __('Eliminar ruta') }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ __('Esta acción no se puede deshacer.') }}</p>
                <form method="POST" action="{{ route('trips.destroy', $trip) }}" onsubmit="return confirm('{{ __('¿Eliminar esta ruta?') }}')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>{{ __('Eliminar ruta') }}</x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
