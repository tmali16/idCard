@extends('layouts.guest')

@section('content')
<form method="POST" action="{{ route('login') }}" class="mt-10">
    @csrf
    <div class="w-full lg:w-1/3 px-2 mb-6 lg:mb-0">
        @error('username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-grey-lighter p-4">
                <h3 class="text-grey-darkest">
                    Access to
                </h3>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <label for="email" class="uppercase text-sm tracking-wide mb-0 font-semibold text-grey-darker px-2">
                        Uname
                    </label>
                    <input type="text" required id="email" value="{{ old('username') }}" name="username" class="no-appearance bg-grey-lighter w-full leading-normal py-1 px-3 rounded border-b border-grey-light mt-2 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label for="password" class="uppercase text-sm tracking-wide mb-0 font-semibold text-grey-darker px-2">
                        Password
                    </label>
                    <input type="password" required id="password" value="{{ old('password') }}" name="password" class="no-appearance bg-grey-lighter w-full leading-normal py-1 px-3 rounded border-b border-grey-light mt-2 focus:outline-none">
                </div>
            </div>
            <button type="submit" class="uppercase font-bold w-full bg-gray-400 text-lg text-blue-lightest rounded-b-lg p-3">
                Access
            </button>
        </div>
    </div>
</form>
{{--    <div class="card">--}}
{{--        <div class="">{{ __('Login') }}</div>--}}
{{--        <div class="">--}}

{{--                @csrf--}}
{{--                <div class="">--}}
{{--                    <label for="username" class="col-md-4 col-form-label text-md-end">{{ __('u') }}</label>--}}
{{--                    <div class="flex">--}}
{{--                        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="" required autocomplete="username" autofocus>--}}
{{--                        @error('username')--}}
{{--                            <span class="invalid-feedback" role="alert">--}}
{{--                                <strong>{{ $message }}</strong>--}}
{{--                            </span>--}}
{{--                        @enderror--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class=" mb-3">--}}
{{--                    <label for="password" class="">{{ __('P') }}</label>--}}
{{--                    <div class="col-md-6">--}}
{{--                        <input id="password" type="password" class=" @error('password') bg-red-500 @enderror" name="password" required autocomplete="current-password">--}}
{{--                        @error('password')--}}
{{--                            <span class="invalid-feedback" role="alert">--}}
{{--                                <strong>{{ $message }}</strong>--}}
{{--                            </span>--}}
{{--                        @enderror--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class=" mb-0">--}}
{{--                    <div class="col-md-8 offset-md-4">--}}
{{--                        <button type="submit" class="btn btn-primary">--}}
{{--                            {{ __('Login') }}--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--        </div>--}}
{{--    </div>--}}
@endsection
