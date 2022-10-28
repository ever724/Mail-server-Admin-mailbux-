@extends('layouts.app', ['page' => 'super_admin.dashboard'])

@section('title', __('messages.dashboard'))
    
@section('page_header')
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.dashboard') }}</li>
                </ol>
            </nav>
            <h1 class="m-0">{{ __('messages.dashboard') }}</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="row card-group-row">
        <div class="col-lg-4 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body card-body-x-lg flex-row align-items-center">
                <div class="flex">
                    <div class="card-header__title text-muted mb-2">{{ __('messages.users') }}</div>
                    <div class="text-amount">{{ isset($users) ? $users : '0'}}</div>
                </div>
                <div><i class="material-icons icon-muted icon-40pt ml-3">perm_identity</i></div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body card-body-x-lg flex-row align-items-center">
                <div class="flex">
                    <div class="card-header__title text-muted mb-2">{{ __('messages.active_subscriptions') }}</div>
                    <div class="text-amount">{{ isset($active_subscriptions) ? $active_subscriptions : '0'}}</div>
                </div>
                <div><i class="material-icons icon-muted icon-40pt ml-3">assessment</i></div>
            </div>
        </div>
        
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex align-items-center">
            <h3 class="card-header__title mb-0 fs-1-3rem">{{ __('messages.earnings_this_year') }}</h3>
        </div>
        <div class="card-body">
            <div class="chart">
                <canvas id="earningsChart" class="chart-canvas chartjs-render-monitor" width="1998" height="600"></canvas>
            </div>
        </div>
    </div>

    
@endsection