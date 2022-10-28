<div class="card card-form">
    <div class="row no-gutters">
        <div class="col-lg-4 card-body">
            <p><strong class="headings-color">{{ __('messages.plan_information') }}</strong></p>
        </div>
        <div class="col-lg-8 card-form__body card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required">
                        <label for="paddle_monthly_id">Paddle Plan (Monthly)</label>
                        <select name="paddle_monthly_id" class="form-control" required>
                            <option value="0">Please Select Paddle Plan</option>
                            @foreach($monthlyPaddlePlans as $paddlePlan)
                                <option value="{{$paddlePlan['id']}}"
                                        {{ $plan->paddle_monthly_id !== 0 ? $plan->paddle_monthly_id === $paddlePlan['id'] ? 'selected=""' : '' : ''}}
                                >
                                    {{$paddlePlan['id']}} - {{$paddlePlan['name']}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required">
                        <label for="paddle_annual_id">Paddle Plan (Annual)</label>
                        <select name="paddle_annual_id" class="form-control" required>
                            <option value="0">Please Select Paddle Plan</option>
                            @foreach($annualPaddlePlans as $paddlePlan)
                                <option value="{{$paddlePlan['id']}}"
                                        {{ $plan->paddle_annual_id !== 0 ? $plan->paddle_annual_id === $paddlePlan['id'] ? 'selected=""' : '' : ''}}
                                >
                                    {{$paddlePlan['id']}} - {{$paddlePlan['name']}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group required">
                        <label for="name">{{ __('messages.name') }}</label>
                        <input name="name" type="text" class="form-control" placeholder="{{ __('messages.name') }}"
                               value="{{ $plan->name ?? old('name') }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="description">{{ __('messages.description') }}</label>
                        <textarea class="form-control" rows="10" name="description" id="description"
                                  placeholder="{{ __('messages.description') }}">{{ old('description', $plan->description) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group required">
                        <label for="monthly_price">{{ __('messages.monthly_price') }}</label>
                        <input name="monthly_price" type="text" class="form-control price_input"
                               placeholder="{{ __('messages.monthly_price') }}" autocomplete="off"
                               value="{{ $plan->monthly_price ?? old('monthly_price') ?? 0 }}" required>
                        <small class="form-text text-muted">{{ __('messages.plan_price_helper') }}</small>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="monthly_sales_price">{{ __('messages.monthly_sales_price') }}</label>
                        <input name="monthly_sales_price" type="text" class="form-control price_input"
                               placeholder="{{ __('messages.monthly_sales_price') }}" autocomplete="off"
                               value="{{ $plan->monthly_sales_price ?? old('monthly_sales_price') ?? 0 }}">
                        <small class="form-text text-muted">&nbsp;</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group required">
                        <label for="annual_price">{{ __('messages.annual_price') }}</label>
                        <input name="annual_price" type="text" class="form-control price_input"
                               placeholder="{{ __('messages.annual_price') }}" autocomplete="off"
                               value="{{ $plan->annual_price ?? old('annual_price') ?? 0 }}" required>
                        <small class="form-text text-muted">{{ __('messages.plan_price_helper') }}</small>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="annual_sales_price">{{ __('messages.annual_sales_price') }}</label>
                        <input name="annual_sales_price" type="text" class="form-control price_input"
                               placeholder="{{ __('messages.annual_sales_price') }}" autocomplete="off"
                               value="{{ $plan->annual_sales_price ?? old('annual_sales_price') ?? 0 }}">
                        <small class="form-text text-muted">&nbsp;</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group required">
                        <label for="trial_period">{{ __('messages.trial_period') }}</label>
                        <input name="trial_period" type="number" class="form-control"
                               placeholder="{{ __('messages.trial_period') }}" value="{{ $plan->trial_period ?? old('trial_period') }}"
                               required>
                        <small class="form-text text-muted">{{ __('messages.plan_trial_period_helper') }}</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group required">
                        <label for="order">{{ __('messages.order') }}</label>
                        <input name="order" type="number" class="form-control" placeholder="{{ __('messages.order') }}"
                               value="{{ $plan->order ?? old('order') ?? \App\Models\Plan::query()->count() + 1 }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-form">
    <div class="row no-gutters">
        <div class="col-lg-4 card-body">
            <p><strong class="headings-color">{{ __('messages.mailbux_settings') }}</strong></p>
        </div>
        <div class="col-lg-8 card-form__body card-body">
            <h4 class="headings-color">{{__('messages.quotas')}}</h4>

            <div class="row mt-5">
                <div class="col-md-6 col-sm-12 form-group required">
                    <label for="storagequota_total">{{__('messages.storagequota_total')}}</label>
                    <input type="text" name="mailbux_settings[storagequota_total]" id="storagequota_total" class="form-control"
                           value="{{$plan->mailbux_settings['storagequota_total'] ?? old('mailbux_settings.storagequota_total')}}"
                    >
                </div>
                <div class="col-md-6 col-sm-12 form-group required">
                    <label for="quota_domains">{{__('messages.quota_domains')}}</label>
                    <input type="text" name="mailbux_settings[quota_domains]" id="quota_domains" class="form-control"
                           value="{{$plan->mailbux_settings['quota_domains'] ?? old('mailbux_settings.quota_domains')}}"
                    >
                </div>
                <div class="col-md-6 col-sm-12 form-group required">
                    <label for="quota_mailboxes">{{__('messages.quota_mailboxes')}}</label>
                    <input type="text" name="mailbux_settings[quota_mailboxes]" id="quota_mailboxes" class="form-control"
                           value="{{$plan->mailbux_settings['quota_mailboxes'] ?? old('mailbux_settings.quota_mailboxes')}}"
                    >
                </div>
                <div class="col-md-6 col-sm-12 form-group required">
                    <label for="quota_aliases">{{__('messages.quota_aliases')}}</label>
                    <input type="text" name="mailbux_settings[quota_aliases]" id="quota_aliases" class="form-control"
                           value="{{$plan->mailbux_settings['quota_aliases'] ?? old('mailbux_settings.quota_aliases')}}"
                    >
                </div>
                <div class="col-md-6 col-sm-12 form-group required">
                    <label for="quota_domainaliases">{{__('messages.quota_domainaliases')}}</label>
                    <input type="text" name="mailbux_settings[quota_domainaliases]" id="quota_domainaliases" class="form-control"
                           value="{{$plan->mailbux_settings['quota_domainaliases'] ?? old('mailbux_settings.quota_domainaliases')}}">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-form">
    <div class="row no-gutters">
        <div class="col-lg-4 card-body">
            <p><strong class="headings-color">{{ __('messages.plan_feature_information') }}</strong></p>
        </div>
        <div class="col-lg-8 card-form__body card-body">
            @php
                $features = $features ?? old('features', $plan->features);
            @endphp
            @if(empty($features) && count($plan->features) === 0)
                <div class="row">
                    <div class="col">
                        <div class="form-group required">
                            <label for="">Feature Label</label>
                            <input name="features[0][label]" type="text" class="form-control"
                                   placeholder="Feature Label" value="" required>
                            <small class="form-text text-muted"></small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group required">
                            <label for="">Feature Value</label>
                            <input name="features[0][value]" type="text" class="form-control"
                                   placeholder="Feature Value" value="" required>
                            <small class="form-text text-muted">{{ __('messages.plan_feature_unlimited_helper') }}</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group required">
                            <label for="">Is Displayed</label>
                            <select name="features[0][is_displayed]" type="text"
                                    class="form-control">
                                <option value="1">{{__('messages.yes')}}</option>
                                <option value="0">{{__('messages.no')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-group">
                            <label for=""> Delete </label>
                            <button type="button" disabled class="btn btn-danger f-remove"><i
                                        class="fa fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            @endif
            <div id="feature-data">
                <div id="feature-list">
                    <input type="hidden"
                           value="{{ old('total_feature', $plan->features->count() > 0 ? $plan->features->count() - 1 : 0) }}"
                           name="total_feature" id="total_feature">
                    @if($features)
                        @foreach($features as $key => $content)
                            <div class="row dynamic__feature">
                                <input type="hidden" name="features[{{$loop->index}}][id]"
                                       value="{{$content['id'] ?? ''}}">
                                <div class="col">
                                    <div class="form-group required">
                                        @if($loop->first)
                                            <label for="">Feature Label</label>
                                        @endif
                                        <input class="form-control mb-1" name="features[{{$loop->index}}][label]"
                                               type="text" placeholder="Feature Label" value="{{$content['label']}}"/>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group required">
                                        @if($loop->first)
                                            <label for="">Feature Value</label>
                                        @endif
                                        <input class="form-control" rows="10" name="features[{{$loop->index}}][value]"
                                               placeholder="Feature Value" value="{{$content['value']}}"></input>
                                        <small class="form-text text-muted">Set -1 to make this feature unlimited.
                                        </small>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group required">
                                        @if($loop->first)
                                            <label for="">Is Displayed</label>
                                        @endif
                                        <select name="features[{{$loop->index}}][is_displayed]" type="text"
                                                class="form-control">
                                            <option value="1" {{$content['is_displayed'] ? 'selected' : ''}}>{{__('messages.yes')}}</option>
                                            <option value="0" {{$content['is_displayed'] ? '' : 'selected'}}>{{__('messages.no')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group required">
                                        @if($loop->first)
                                            <label for="">{{__('order')}}</label>
                                        @endif
                                            <input class="form-control" min="1" type="number" name="features[{{$loop->index}}][order]" value="{{$content['order'] ?? ($loop->index + 1)}}">
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        @if($loop->first)
                                            <label for=""> Delete </label>
                                            <button type="button" disabled class="btn btn-danger remove"><i
                                                        class="fa fa-trash"></i></button>
                                        @else
                                            <button type="button" class="btn btn-danger f-remove"><i
                                                        class="fa fa-trash"></i></button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-1">
                    <div class="form-group">
                        <button id="feature-add" type="button" class="add btn btn-secondary"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group text-center mt-5">
                <button class="btn btn-primary save_form_button">{{ __('messages.save_plan') }}</button>
            </div>
        </div>
    </div>
</div>