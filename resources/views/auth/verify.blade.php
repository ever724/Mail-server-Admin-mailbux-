@extends('layouts.auth')

@section('title', __('messages.verify_your_email'))

@section('content')
<h1 class="text-center h6 mb-4">{{ __('messages.verify_your_email') }}</h1>

<div class="text-center">
    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            {{ __('messages.verify_email_fresh') }}
        </div>
    @endif

    {{ __('messages.resend_invitaion_description') }}
    <br>
    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('Click here') }}</button>
    </form>
</div>
@endsection
