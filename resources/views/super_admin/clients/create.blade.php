@extends('layouts.app', ['page' => 'super_admin.clients'])

@section('title', __('messages.clients'))

@section('page_header')
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#"><i class="material-icons icon-20pt">home</i></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{route('super_admin.clients')}}"> {{ __('messages.clients') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.new_account') }}</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{route('super_admin.clients.store')}}" method="POST">
        @csrf
        <div class="card card-form">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{__('messages.client.name')}}</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text"
                                   name="name" value="{{old('name')}}" id="name">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username" class="form-label">{{__('messages.client.email')}}</label><br>
                            <div class="input-group">
                                <input class="form-control @error('username') is-invalid @enderror" type="text" name="username"
                                       value="{{old('username')}}" id="username">
                                <div class="input-group-append">
                                    <div class="input-group-text text-white bg-secondary">{{ '@'. $domain }}</div>
                                </div>
                                <input type="hidden" name="domain" value="{{$domain}}">
                            </div>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">{{__('messages.password')}}</label>
                            <input class="form-control @error('password') is-invalid @enderror" type="password"
                                   name="password" id="password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_repeat">{{__('messages.retype_your_password')}}</label>
                            <input class="form-control @error('password_repeat') is-invalid @enderror" type="password"
                                   name="password_repeat" id="password_repeat">
                            @error('password_repeat')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="organization">{{__('messages.client.organization')}}</label>
                            <input class="form-control @error('organization') is-invalid @enderror" type="text"
                                   value="{{old('organization')}}"
                                   name="organization" id="organization">
                            @error('organization')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="recovery_email">{{__('messages.client.recovery_email')}}</label>
                            <input class="form-control @error('recovery_email') is-invalid @enderror" type="text"
                                   value="{{old('recovery_email')}}" name="recovery_email" id="recovery_email">
                            @error('recovery_email')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="api_access">{{__('messages.client.api_access')}}</label>
                            <select class="form-control @error('api_access') is-invalid @enderror" name="api_access"
                                    id="api_access">
                                <option value="1">{{__('messages.yes')}}</option>
                                <option value="0" {{old('api_access') == "0" ? 'selected': ''}}>{{__('messages.no')}}</option>
                            </select>
                            @error('api_access')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="enabled">{{__('messages.client.enabled')}}</label>
                            <select class="form-control @error('enabled') is-invalid @enderror" name="enabled"
                                    id="enabled">
                                <option value="1">{{__('messages.yes')}}</option>
                                <option value="0" {{old('enabled') == "0" ? 'selected': ''}}>{{__('messages.no')}}</option>
                            </select>
                            @error('enabled')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-success disabled">
                            {{__('messages.save')}}
                        </button>
                        <a href="{{route('super_admin.clients')}}" class="btn btn-secondary">
                            {{__('messages.cancel')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
