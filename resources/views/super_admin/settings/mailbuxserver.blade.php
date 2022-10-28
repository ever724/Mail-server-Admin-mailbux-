@extends('layouts.app', ['page' => 'super_admin.settings.mailbuxserver'])

@section('title', __('messages.mailbuxserver_settings'))

@section('page_header')
<div class="page__heading d-flex align-items-center">
    <div class="flex">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                <li class="breadcrumb-item active" aria-current="page"><a>{{ __('messages.mailbuxserver_settings') }}</a></li>
            </ol>
        </nav>
        <h1 class="m-0 h3">{{ __('messages.mailbuxserver_settings') }}</h1>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('super_admin.settings.mailbuxserver.update') }}" method="POST" enctype="multipart/form-data">
    @include('layouts._form_errors')
    @csrf

    @include('super_admin.settings._mailbuxserver_form')
</form>
@endsection