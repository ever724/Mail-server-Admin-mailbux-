<div class="card card-form">
    <div class="row no-gutters">
        <div class="col-lg-4 card-body">
            <p><strong class="headings-color">{{ __('messages.mailbuxserver_settings') }}</strong></p>
        </div>
        <div class="col-lg-8 card-form__body card-body">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="form-group required">
                        <label for="mailbux_domain_url">{{ __('messages.mailbux_domain_url') }}</label>
                        <input name="mailbux_domain_url" type="text" class="form-control"
                               placeholder="{{ __('messages.mailbux_domain_url') }}"
                               value="{{ get_system_setting('mailbux_domain_url') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group required">
                        <label for="mailbux_username">{{ __('messages.mailbux_username') }}</label>
                        <input name="mailbux_username" type="text" class="form-control"
                               placeholder="{{ __('messages.mailbux_username') }}"
                               value="{{ get_system_setting('mailbux_username') }}">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group required">
                        <label for="mailbux_password">{{ __('messages.mailbux_password') }}</label>
                        <input name="mailbux_password" class="form-control"
                               placeholder="{{ __('messages.mailbux_password') }}"
                               value="{{ get_system_setting('mailbux_password') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="mailbux_active">{{ __('messages.active') }}</label>
                        <select name="mailbux_active" class="form-control">
                            <option value="0" {{ get_system_setting('mailbux_active') == false ? 'selected' : '' }}>{{ __('messages.disabled') }}</option>
                            <option value="1" {{ get_system_setting('mailbux_active') == true  ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="mailbux_auth_token">{{ __('messages.current_token') }}</label>
                        <input name="mailbux_auth_token" type="text"
                               class="form-control disabled" disabled
                               value="{{ get_system_setting('mailbux_auth_token') }}"
                        >
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label for="mailbux_auth_token">{{ __('messages.refresh') }}</label>
                        <a href="{{route('super_admin.settings.mailbuxserver.refresh-token')}}" class="btn btn-md btn-primary"><i class="fa fa-sync"></i></a>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="form-group">
                        <label for="mailbux_auth_token">{{ __('messages.last_update') }}</label>
                        <input name="mailbux_auth_token" type="text"
                               class="form-control disabled" disabled
                               value="{{\Illuminate\Support\Carbon::createFromTimestamp(get_system_setting('mailbux_auth_token_last_update'))->format('Y-m-d H:i:s') }}"
                        >
                    </div>
                </div>
            </div>
            <div class="form-group text-center mt-5">
                <button class="btn btn-primary save_form_button">{{ __('messages.update') }}</button>
            </div>
        </div>
    </div>
</div>