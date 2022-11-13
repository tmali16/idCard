@extends('layouts.guest')

@section('content')
<form method="POST" action="{{ route('login') }}" class=" w-full h-full flex justify-center items-center">
    @csrf
    <div class="w-full lg:w-1/3 px-2 mb-6 lg:mb-0">
        @error('username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <div class="bg-white shadow ">
            <div class="border-b border-grey-lighter p-4">
                <h3 class="text-grey-darkest">
                    Добро пожаловать
                </h3>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <label for="password" class="uppercase m-0 text-sm tracking-wide mb-0 font-semibold text-grey-darker ">
                        Uname
                    </label>
                    <input type="text" autocomplete="off" required id="username" value="{{ old('password') }}" name="username" class="no-appearance bg-grey-lighter w-full leading-normal py-1 px-3  border-b border-grey-light focus:outline-none">
                </div>
                <div class="mb-4">
                    <label for="password" class="uppercase m-0 text-sm tracking-wide mb-0 font-semibold text-grey-darker ">
                        Password
                    </label>
                    <input type="password" required id="password" value="{{ old('password') }}" name="password" class="no-appearance bg-grey-lighter w-full leading-normal py-1 px-3  border-b border-grey-light focus:outline-none">
                </div>
            </div>
            <div class="w-full justify-end items-center flex p-4">
                <button type="submit" class=" text-sm bg-blue-500 text-white text-lg  py-1 px-4">
                    Access
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
