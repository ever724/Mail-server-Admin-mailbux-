@extends('layouts.app', ['page' => 'super_admin.support_tickets'])

@section('title', __('messages.support_tickets'))

@section('page_header')
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.support_tickets') }}</li>
                </ol>
            </nav>
            <h1 class="m-0">{{ __('messages.support_tickets') }}</h1>
        </div>
    </div>
@endsection

@section('content')
    @include('super_admin.support_tickets._filters')

    <div class="card">
        @include('super_admin.support_tickets._table')
    </div>
@endsection
@section('custom_css')
    @include('super_admin.support_tickets._table_custom_css_js')
@endsection
@section('custom_js')
    <script>
        $('tr.unread').click(function(){
            let id = $(this).data('id');
            if(typeof id === 'number'){
                window.location.href = "{{route('super_admin.support_tickets.show', '%ticketId%')}}".replace('%ticketId%', id);
            }
        })
    </script>
@endsection