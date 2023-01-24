@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <form action="/user/store">
                    @include('form.userForm')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
