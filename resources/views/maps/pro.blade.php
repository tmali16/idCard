@extends("layouts.app")
@section('content')
<pro-map-component :auth="{{auth()->user()}}" :permissions="{{auth()->user()->getPermissions()->pluck('slug')}}"></pro-map-component>
@endsection
