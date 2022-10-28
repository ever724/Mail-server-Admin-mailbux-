<form action="" method="GET">
    <div class="card card-form d-flex flex-column flex-sm-row">
        <div class="card-form__body card-body-form-group flex">
            <div class="row">
                <div class="col-sm-auto">
                    <div class="form-group">
                        <label for="filter[client_name]">{{ __('messages.client_name') }}</label>
                        <input name="filter[client_name]" type="text" class="form-control" value="{{ Request::input('filter.client_name') ?? '' }}" placeholder="{{ __('messages.search') }}">
                    </div>
                </div>
                <div class="col-sm-auto">
                    <div class="form-group">
                        <label for="filter[this_month]">{{ __('messages.this_month') }}</label>
                        <div class="custom-control custom-checkbox mt-sm-2">
                            <input id="filter[this_month]" name="filter[this_month]" type="checkbox" {{ Request::has('filter.this_month') ? 'checked=""' : '' }} value="true" class="custom-control-input" >
                            <label class="custom-control-label" for="filter[this_month]">{{ __('messages.yes') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-auto">
                    <div class="form-group">
                        <label for="filter[not_trials]">{{ __('messages.not_trials') }}</label>
                        <div class="custom-control custom-checkbox mt-sm-2">
                            <input id="filter[not_trials]" name="filter[not_trials]" type="checkbox" {{ Request::has('filter.not_trials') ? 'checked=""' : '' }} value="true" class="custom-control-input" >
                            <label class="custom-control-label" for="filter[not_trials]">{{ __('messages.yes') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <a href="{{ route('super_admin.invoices') }}">{{ __('messages.clear_filters') }}</a>
                </div>
            </div>
        </div>
        <button type="submit" class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0">
            <i class="material-icons text-primary icon-20pt">refresh</i>
            {{ __('messages.filter') }}
        </button>
    </div>
</form>