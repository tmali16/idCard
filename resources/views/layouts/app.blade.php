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
        <div class="min-h-screen bg-blue-400">
{{--            @include('layouts.navigation')--}}
            <header class="bg-white shadow-sm">
                <div class="container mx-auto px-2 sm:px-6 lg:px-8">
                    <ul class="flex flex-row mt-0 mr-6 w-full gap-x-8 text-sm font-medium">
                        <li class="">
                            <a class="text-gray-900 dark:text-white hover:underline {{request()->routeIs('index') ? 'border-b-2 border-red-400' : ''}}" aria-current="page" href="{{route('index')}}">
                                Главная
                            </a>
                        </li>
                        @permission('request.terminal')
                        <li class=" {{request()->routeIs('terminal') ? 'border-b-2 border-red-400' : ''}}">
                            <a class="text-gray-900 dark:text-white hover:underline" href="{{route('terminal')}}">Контроль</a>
                        </li>
                        @endpermission
                        <li class="">
                        <a class="hover:bg-gray-200 block px-4 text-red-500 text-sm text-gray-700" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Выход') }}
                            <form action="{{route('logout')}}" style="display: none;" id="logout-form" method="post">@csrf</form>
                        </a>
                        </li>
                    </ul>
                </div>
            </header>
            <!-- Page Content -->
            <main class="h-screen" id="app">
                @yield("content")
            </main>
        </div>
    </body>
</html>
