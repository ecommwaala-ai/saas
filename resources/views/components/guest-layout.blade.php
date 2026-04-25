@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' - '.config('app.name') : config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <main class="flex min-h-screen items-center justify-center px-4 py-10">
            <div class="w-full max-w-md">
                <div class="mb-8 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-600 text-xl font-bold text-white shadow-sm">S</div>
                    <h1 class="mt-5 text-2xl font-bold text-gray-900">{{ config('app.name') }}</h1>
                    <p class="mt-2 text-sm text-gray-500">Sign in to continue to your workspace.</p>
                </div>

                <div class="card">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </body>
</html>
