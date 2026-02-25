@extends('layouts.customers')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            {{-- Global Error Alert (Optional but good for Authentication errors) --}}
            @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
            <div class="alert alert-danger shadow-sm border-0 small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card shadow border-0 overflow-hidden">
                <div class="card-body p-4">
                    <h4 class="text-center mb-4">🗝️ Login</h4>

                    <form action="{{ url('/login') }}" method="POST">
                        @csrf

                        {{-- Email Input using your form-input component --}}
                        <x-form-input
                            name="email"
                            label="Email Address"
                            type="email"
                            placeholder="example@mail.com"
                            :required="true"
                            autofocus />

                        {{-- Password Input using your form-input component --}}
                        <x-form-input
                            name="password"
                            label="Password"
                            type="password"
                            placeholder="*****.."
                            :required="true" />
                        {{-- Submit Button --}}
                        <x-app-button type="submit" color="primary" class="w-100 py-2 shadow-sm">
                            Login
                        </x-app-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection