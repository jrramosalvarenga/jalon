<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#000000">

        <title>{{ config('app.name', 'Jalon') }}</title>

        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="icon" href="{{ asset('icons/icon-192.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-gray-900">
        <div class="min-h-screen flex flex-col items-center justify-center px-6 text-center">
            <h1 class="text-6xl sm:text-7xl font-extrabold tracking-tight text-black mb-4">Jalon</h1>
            <p class="max-w-md text-lg text-gray-600 mb-10">
                Comparte tus rutas de viaje con tus contactos y ofrece o solicita un jalón.
                Tú decides cuántos cupos llevas y si el viaje tiene costo.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 w-full max-w-xs sm:max-w-none sm:w-auto">
                @auth
                    <a href="{{ route('trips.index') }}" class="px-8 py-3.5 bg-black text-white rounded-full font-semibold hover:bg-gray-800">
                        Ir a mis rutas
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-3.5 bg-black text-white rounded-full font-semibold hover:bg-gray-800">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}" class="px-8 py-3.5 bg-white border border-gray-300 rounded-full font-semibold hover:bg-gray-50">
                        Crear cuenta
                    </a>
                @endauth
            </div>
        </div>
    </body>
</html>
