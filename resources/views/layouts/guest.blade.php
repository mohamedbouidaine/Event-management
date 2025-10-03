<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900">

        <!-- Header / Navbar -->
        <nav class="w-full bg-white dark:bg-gray-800 shadow p-4">
            <div class="max-w-5xl mx-auto flex justify-between items-center">
                <a href="/" class="text-xl font-bold text-gray-800 dark:text-gray-200">MyApp</a>
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-200 hover:underline">Login</a>
                    <a href="{{ route('register') }}" class="text-gray-700 dark:text-gray-200 hover:underline">Register</a>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="flex justify-center items-center flex-1 pt-6 sm:pt-0">
            <div class="w-full sm:max-w-md px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

    </div>
</body>
</html>
