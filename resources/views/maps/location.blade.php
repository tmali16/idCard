@extends("layouts.app")
@section('content')
    @if(auth()->check() && auth()->user()?->username == 'admin')
    <location-component></location-component>
    @endif
@endsection
