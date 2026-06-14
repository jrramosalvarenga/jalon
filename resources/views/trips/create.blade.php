<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Publicar ruta') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 sm:p-6">
                <form method="POST" action="{{ route('trips.store') }}" class="space-y-6">
                    @csrf
                    @include('trips.partials.form', ['trip' => null])

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Publicar') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
