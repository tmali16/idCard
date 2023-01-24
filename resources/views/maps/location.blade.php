@extends("layouts.app")
@section('content')
    <location-component :auth="{{auth()->user()}}" :permission="{{auth()->user()->getPermissions()->pluck('slug')}}"></location-component>
@endsection
