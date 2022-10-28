@extends('layouts.customer_portal', ['page' => 'auth'])

@section('title', __('messages.login'))

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body p-5">
                    <h4 class="text-center">{{ __('messages.login_to_your_account') }}</h4>
                    <p class="text-center mb-4">{{ $currentCustomer->company->name }}</p>

                    <form id="auth-form" action="{{ route('customer_portal.login.submit', ['customer' => $currentCustomer->uid]) }}" method="POST" novalidate>
                        @csrf
                        @honeypot
                        
                        @if($errors->has('recaptcha_token'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{$errors->first('recaptcha_token')}}</strong>
                            </span>
                        @endif
                    
                        <div class="form-group">
                            <label class="text-label" for="email">{{ __('messages.email') }}:</label>
                            <div class="input-group input-group-merge">
                                <input id="email" name="email" type="email"
                                    class="form-control form-control-prepended @error('email') is-invalid @enderror"
                                    placeholder="user@example.com" value="{{ old('email') }}" autocomplete="email"
                                    autofocus required>
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="far fa-envelope"></span>
                                    </div>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                    
                        </div>

                        <div class="form-group">
                            <label class="text-label" for="password">{{ __('messages.password') }}:</label>
                            <div class="input-group input-group-merge">
                                <input id="password" name="password" type="password"
                                    class="form-control form-control-prepended @error('password') is-invalid @enderror"
                                    placeholder="{{ __('messages.enter_your_password') }}" required autocomplete="current-password">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="fa fa-key"></span>
                                    </div>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    
                        <div class="form-group">
                            <button class="btn btn-block btn-primary" type="submit">{{ __('messages.login') }}</button>
                        </div>
                    
                        <div class="form-group text-center">
                            <a href="{{ route('customer_portal.forgot_password', ['customer' => $currentCustomer->uid]) }}">{{ __('messages.dont_know_your_password') }}</a> <br>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection