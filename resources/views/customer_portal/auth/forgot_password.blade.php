@extends('layouts.customer_portal', ['page' => 'auth'])

@section('title', __('messages.reset_password'))

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body p-5">
                    <h4 class="text-center">{{ __('messages.reset_password') }}</h4>
                    <p class="text-center mb-4">{{ $currentCustomer->company->name }}</p>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                
                    <form action="{{ route('customer_portal.forgot_password.submit', ['customer' => $currentCustomer->uid]) }}" method="POST" novalidate>
                        @csrf
                        @honeypot
                        <div class="form-group">
                            <label class="text-label" for="email">{{ __('messages.email') }}:</label>
                            <div class="input-group input-group-merge">
                                <input id="email" name="email" type="email" class="form-control form-control-prepended @error('email') is-invalid @enderror" placeholder="user@example.com" value="{{ old('email') }}" autocomplete="email" autofocus required>
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
                            <button class="btn btn-block btn-primary" type="submit">{{ __('messages.send_reset_link') }}</button>
                        </div>
                
                        <div class="form-group">
                            <a href="{{ route('customer_portal.login', ['customer' => $currentCustomer->uid]) }}" class="btn btn-block btn-light">{{ __('messages.return_to_login') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection