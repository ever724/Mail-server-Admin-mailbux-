@extends('layouts.customer_portal', ['page' => 'auth'])

@section('title', __('messages.reset_your_password'))

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body p-5">
                    <h4 class="text-center">{{ __('messages.reset_your_password') }}</h4>
                    <p class="text-center mb-4">{{ $currentCustomer->company->name }}</p>

                    <form action="{{ route('customer_portal.reset_password.submit', ['customer' => $currentCustomer->uid, 'token' => request('token', '')]) }}" method="POST" novalidate>
                        @csrf
                        @honeypot
                    
                        <div class="form-group">
                            <label class="text-label" for="password">{{ __('messages.new_password') }}:</label>
                            <div class="input-group input-group-merge">
                                <input id="password" name="password" type="password"
                                    class="form-control form-control-prepended @error('password') is-invalid @enderror"
                                    placeholder="{{ __('messages.enter_your_password') }}"
                                    value="" autocomplete="password" required>
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="far fa-key"></span>
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
                            <label class="text-label" for="password_confirmation">{{ __('messages.retype_your_password') }}:</label>
                            <div class="input-group input-group-merge">
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                    class="form-control form-control-prepended @error('password_confirmation') is-invalid @enderror"
                                    placeholder="{{ __('messages.retype_your_password') }}" value=""
                                    autocomplete="password_confirmation" required>
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="far fa-key"></span>
                                    </div>
                                </div>
                                @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    
                        <div class="form-group">
                            <button class="btn btn-block btn-primary" type="submit">{{ __('messages.reset_password') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection