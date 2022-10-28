@extends('layouts.app', ['page' => 'settings'])

@section('title', __('messages.api_credentials'))

@section('content')
    <div class="page__heading">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">business</i></a></li>
                <li class="breadcrumb-item">{{ __('messages.settings') }}</li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.api_credentials') }}</li>
            </ol>
        </nav>
        <h1 class="m-0">{{ __('messages.api_credentials') }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-3">
            @include('application.settings._aside', ['tab' => 'api'])
        </div>
        <div class="col-lg-9">
            <div class="card card-form">
                <div class="row no-gutters">
                    <div class="col card-form__body card-body bg-white">
                        <h3>{{ __('messages.api_token') }}</h3>
                        <div class="row mt-4">
                            <div class="col">
                                <input class="form-control" type="text" value="{{ $authUser->api_token }}" readonly>
                            </div>
                            <div class="col col-auto">
                                <a class="btn btn-danger delete-confirm" href="{{ route('settings.api.revoke', ['company_uid' => $currentCompany->uid]) }}">{{ __('Revoke token') }}</a>
                            </div>
                        </div>
                        <p class="mt-4">{{ __('messages.api_documentation_link_description') }}: <a href="{{ url('/docs/api/v1/index.html') }}" target="_blank">{{ url('/docs/api/v1/index.html') }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

