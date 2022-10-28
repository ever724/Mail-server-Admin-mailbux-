@extends('layouts.app', ['page' => 'super_admin.clients'])

@section('title', __('messages.clients'))

@section('page_header')
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.clients') }}</li>
                </ol>
            </nav>
            <h1 class="m-0">{{ __('messages.clients') }}</h1>
        </div>
    </div>
@endsection

@section('content')
    @include('super_admin.clients._filters')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-4 text-left">
                    {{__('messages.client.last_synced_at')}}: {{$lastSyncedAt->diffForHumans()}}
                </div>
                <div class="col-md-4 text-center">

                </div>
                <div class="col-md-4 text-right">
                    <a href="{{route('super_admin.clients.sync')}}" class="btn btn-sm btn-outline-dark"><i
                                class="fa fa-sync"></i>
                        {{__('messages.sync')}}
                    </a>
                    <a href="{{route('super_admin.clients.create')}}" class="btn btn-sm btn-outline-success"><i
                                class="fa fa-plus"></i>
                        {{__('messages.new_account')}}
                    </a>
                </div>
            </div>
        </div>
        @include('super_admin.clients._table')
    </div>
@endsection
