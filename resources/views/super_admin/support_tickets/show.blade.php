@extends('layouts.app', ['page' => 'super_admin.support_tickets'])

@section('title', __('messages.support_tickets') . " | ". $support_ticket->subject)

@section('page_header')
    <script src="{{asset('assets/js/tinymce/tinymce.min.js')}}"></script>
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item"><a
                                href="{{route('super_admin.support_tickets')}}">{{ __('messages.support_tickets') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Ticket #{{$support_ticket->id}}</li>
                </ol>
            </nav>
            <h3 class="text-grey">Ticket #{{$support_ticket->id}} - {{ $support_ticket->subject }}</h3>
        </div>
    </div>
@endsection

@section('content')
    <div class="container bootstrap snippets bootdeys">
        <div class="timeline-centered timeline-sm">
            @foreach($support_ticket->messages as $message)
                <article class="timeline-entry {{$message->sender ? '' : 'left-aligned'}}">
                    <div class="timeline-entry-inner">
                        <time datetime="{{$message->created_at->format('Y-m-d H:i:s')}}" class="timeline-time">
                            <span>{{$message->created_at->format('H:i')}}</span><span>{{$message->created_at->diffForHumans()}}</span>
                        </time>
                        <div class="timeline-icon {{$message->sender ? 'bg-yellow' : 'bg-violet'}}"><i
                                    class="fa {{$message->sender ? 'fa-paper-plane' : 'fa-envelope-open'}} "></i>
                        </div>
                        <div class="timeline-label"><h4 class="timeline-title">{{$message->sender_name}}</h4>

                            <p>{!! $message->body !!}</p>
                            @foreach($message->attachments as $attachment)
                                <div>
                                    <a href="{{route('super_admin.support_tickets.download-attachment', [$attachment])}}">
                                        <i class="fa fa-paperclip"></i> {{$attachment->file_name}}
                                    </a>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </article>
            @endforeach
            @if($support_ticket->closed_at !== null)
                <article class="timeline-entry">
                    <div class="timeline-entry-inner">
                        <time datetime="{{$support_ticket->closed_at->format('Y-m-d H:i:s')}}" class="timeline-time">
                            <span>{{$support_ticket->closed_at->format('H:i')}}</span><span>{{$support_ticket->closed_at->diffForHumans()}}</span>
                        </time>
                        <div class="timeline-icon bg-green"><i class="fa fa-check"></i></div>
                        <div class="timeline-label">
                            <h4 class="timeline-title">{{__('messages.ticket_closed')}}</h4>
                        </div>
                    </div>
                </article>
            @endif
            @if($support_ticket->closed_at == null)
                <article class="timeline-entry">
                    <div class="timeline-entry-inner">
                        <div class="timeline-icon"><i class="fa fa-pen"></i></div>
                        <div class="timeline-label"><h4 class="timeline-title">{{__('messages.reply')}}</h4>
                            <form id="submitReply">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="form-group">
                                    <div class="input-group">
                                            <textarea name="body"
                                                      class="form-control"
                                                      id="replyBody"
                                                      rows="3">{{old('body')}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <a id="addFile" class="btn btn-md btn-outline-secondary"><i
                                                class="fas fa-plus"></i> {{__('messages.add_file')}}</a>
                                </div>
                                <button type="submit" id="submitButton" class="btn btn-primary">{{__('messages.submit')}}</button>
                            </form>
                        </div>
                    </div>
                </article>
                <article class="timeline-entry left-aligned">
                    <div class="timeline-entry-inner" id="closeTicketFormWrapper">
                        <form id="closeTicketForm" method="POST"
                              action="{{route('super_admin.support_tickets.close', $support_ticket)}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <a id="closeTicketButton" data-ticket-id="{{$support_ticket->id}}">
                                <div class="timeline-icon bg-green">
                                    <i class="fa fa-check"></i>
                                </div>
                            </a>
                        </form>
                    </div>
                </article>
            @endif
        </div>
    </div>
@endsection
@section('custom_css')
    @include('super_admin.support_tickets._show_custom_css')
@endsection
@section('custom_js')
    @include('super_admin.support_tickets._show_custom_js')
@endsection