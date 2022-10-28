@extends('layouts.app', ['page' => 'super_admin.users'])

@section('title', __('messages.edit_user'))
    
@section('page_header')
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('super_admin.users') }}">{{ __('messages.users') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.edit_user') }}</li>
                </ol>
            </nav>
            <h1 class="m-0 h3">{{ __('messages.edit_user') }}</h1>
        </div>
        <a href="{{ route('super_admin.users.delete', $user->id) }}" class="btn btn-danger ml-3 delete-confirm">
            <i class="material-icons">delete</i> 
            {{ __('messages.delete_user') }}
        </a>
    </div>
@endsection
 
@section('content') 
    <form action="{{ route('super_admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @include('layouts._form_errors')
        @csrf
        
        @include('super_admin.users._form')
    </form>
@endsection