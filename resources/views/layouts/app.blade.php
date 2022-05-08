<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-blue-400 overflow-hidden">
{{--            @include('layouts.navigation')--}}
{{--            <header class="bg-white shadow-sm">--}}
{{--                <div class="container mx-auto pt-2 px-4 sm:px-6 lg:px-8">--}}
{{--                    <ul class="nav">--}}
{{--                        <li class="nav-item ">--}}
{{--                            <a class="nav-link  {{request()->routeIs('index') ? 'border-b-2 border-red-400' : ''}}" aria-current="page" href="{{route('index')}}">--}}
{{--                                Главная--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item {{request()->routeIs('control') ? 'border-b-2 border-red-400' : ''}}">--}}
{{--                            <a class="nav-link" href="{{route('control')}}">Контроль</a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item {{request()->routeIs('guard') ? 'border-b-2 border-red-400' : ''}}">--}}
{{--                            <a class="nav-link" href="#">--}}
{{--                                Сторож--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </header>--}}
            <!-- Page Content -->
            <main class="h-100" id="app">
                @yield("content")
            </main>
        </div>
    </body>
</html>
