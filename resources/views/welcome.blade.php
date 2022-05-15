@extends("layouts.app")
@section('content')
    <div class="h-full w-full flex flex-column items-center justify-center">
        <div class="w-64 bg-white ">
            <div class="px-6 h-20 text-center items-center flex justify-center mb-2">
                <v-icon size="lg">mdi-map-marker-radius </v-icon>
            </div>
            <a href="/map" class="bg-green-500 shadow-lg px-6 py-1 text-center text-white">КАРТА</a>
        </div>
    </div>
@endsection
