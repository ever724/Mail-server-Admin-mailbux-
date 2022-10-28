<form action="" method="GET">
    <div class="card card-form d-flex flex-column flex-sm-row">
        <div class="card-form__body card-body-form-group flex">
            <div class="row">
                <div class="col-sm-auto">
                    <div class="form-group">
                        <label for="filter[sender_name]">{{ __('messages.sender_name') }}</label>
                        <input name="filter[sender_name]" type="text" class="form-control" value="{{ Request::input('filter.sender_name') ?? '' }}" placeholder="{{ __('messages.search') }}">
                    </div>
                </div>
                <div class="col-sm-auto">
                    <div class="form-group">
                        <label for="filter[with_closed]">{{ __('messages.with_closed') }}</label>
                        <div class="custom-control custom-checkbox mt-sm-2">
                            <input id="filter[with_closed]" name="filter[with_closed]" type="checkbox" {{ Request::has('filter.with_closed') ? 'checked=""' : '' }} value="true" class="custom-control-input" >
                            <label class="custom-control-label" for="filter[with_closed]">{{ __('messages.yes') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-auto">
                    <div class="form-group">
                        <label for="filter[unread_only]">{{ __('messages.unread_only') }}</label>
                        <div class="custom-control custom-checkbox mt-sm-2">
                            <input id="filter[unread_only]" name="filter[unread_only]" type="checkbox" {{ Request::has('filter.unread_only') ? 'checked=""' : '' }} value="true" class="custom-control-input" >
                            <label class="custom-control-label" for="filter[unread_only]">{{ __('messages.yes') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <a href="{{ route('super_admin.support_tickets') }}">{{ __('messages.clear_filters') }}</a>
                </div>
            </div>
        </div>
        <button type="submit" class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0">
            <i class="material-icons text-primary icon-20pt">refresh</i>
            {{ __('messages.filter') }}
        </button>
    </div>
</form>