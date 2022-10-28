<form action="" method="GET">
    <div class="card card-form d-flex flex-column flex-sm-row">
        <div class="card-form__body card-body-form-group flex">
            <div class="row">
                <div class="col-sm-auto">
                    <div class="form-group">
                        <label for="include_premium_domain">{{ __('messages.include_premium_domain') }}</label>
                        <div class="custom-control custom-checkbox mt-sm-2">
                            <input id="include_premium_domain" name="include_premium_domain"
                                   type="checkbox"
                                   {{ Request::has('include_premium_domain') ? 'checked=""' : '' }} value="true"
                                   class="custom-control-input">
                            <label class="custom-control-label"
                                   for="include_premium_domain">{{ __('messages.yes') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-auto">
                    <div class="form-group">
                        <label>{{ __('messages.client.exists_on_mail_server') }}</label>
                        <div class="select-group">
                            <select id="exists_on_mail_server" name="exists_on_mail_server"
                                   class="form-control">
                                <option value="">{{__('messages.please_select')}}</option>
                                <option {{request()->input('exists_on_mail_server') == "1" ? "selected" : '' }} value="1">{{__('messages.yes')}}</option>
                                <option {{request()->input('exists_on_mail_server') == "0" ? "selected" : '' }} value="0">{{__('messages.no')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <a href="{{ route('super_admin.clients') }}">{{ __('messages.clear_filters') }}</a>
                </div>
            </div>
        </div>
        <button type="submit"
                class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0">
            <i class="material-icons text-primary icon-20pt">refresh</i>
            {{ __('messages.filter') }}
        </button>
    </div>
</form>