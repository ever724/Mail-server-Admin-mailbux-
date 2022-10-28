<nav class="nav nav-pills nav-fill">
    <a class="col-6 nav-link bg-secondary text-white active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">{{ __('messages.account') }}</a>
    <a class="col-6 nav-link bg-secondary text-white" id="permissions-tab" data-toggle="tab" href="#permissions" role="tab" aria-controls="permissions" aria-selected="false">{{ __('messages.permissions') }}</a>
</nav>

<div class="tab-content mt-4" id="myTabContent">
    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
        <div class="form-group">
            <label>{{ __('messages.profile_image') }}</label><br>
            <input id="avatar" name="avatar" class="d-none" type="file" onchange="changePreview(this);">
            <label for="avatar">
                <div class="media align-items-center">
                    <div class="mr-3">
                        <div class="avatar avatar-xl">
                            <img id="file-prev" src="{{ $member->avatar }}" class="avatar-img rounded">
                        </div>
                    </div>
                    <div class="media-body">
                        <a class="btn btn-sm btn-light choose-button">{{ __('messages.choose_photo') }}</a>
                    </div>
                </div>
            </label> 
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group required">
                    <label for="first_name">{{ __('messages.first_name') }}</label>
                    <input name="first_name" type="text" class="form-control" placeholder="{{ __('messages.first_name') }}" value="{{ $member->first_name }}" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group required">
                    <label for="last_name">{{ __('messages.last_name') }}</label>
                    <input name="last_name" type="text" class="form-control" placeholder="{{ __('messages.last_name') }}" value="{{ $member->last_name }}" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group required">
                    <label for="email">{{ __('messages.email') }}</label>
                    <input name="email" type="email" class="form-control" placeholder="{{ __('messages.email') }}" value="{{ $member->email }}" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="phone">{{ __('messages.phone') }}</label>
                    <input name="phone" type="text" class="form-control" placeholder="{{ __('messages.phone') }}" value="{{ $member->phone }}">
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col">
                <div class="form-group {{ $member->id == null ? 'required' : '' }}">
                    <label for="password">{{ __('messages.password') }}</label>
                    <input name="password" type="password" class="form-control" placeholder="{{ __('messages.password') }}">
                </div>
            </div>
            <div class="col">
                <div class="form-group {{ $member->id == null ? 'required' : '' }}">
                    <label for="password_confirmation">{{ __('messages.confirm_password') }}</label>
                    <input name="password_confirmation" type="password" class="form-control" placeholder="{{ __('messages.confirm_password') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
        <div class="row">
            <div class="col-3 mb-4">
                <h6>{{ __('messages.company_preferences') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view preferences]" name="permissions[view preferences]" type="checkbox" {{ $member->hasPermission('view preferences') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view preferences]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update preferences]" name="permissions[update preferences]" type="checkbox" {{ $member->hasPermission('update preferences') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update preferences]">{{ __('messages.update') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.company_settings') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view company settings]" name="permissions[view company settings]" type="checkbox" {{ $member->hasPermission('view company settings') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view company settings]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update company settings]" name="permissions[update company settings]" type="checkbox" {{ $member->hasPermission('update company settings') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update company settings]">{{ __('messages.update') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.credit_notes') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view credit notes]" name="permissions[view credit notes]" type="checkbox" {{ $member->hasPermission('view credit notes') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view credit notes]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create credit note]" name="permissions[create credit note]" type="checkbox" {{ $member->hasPermission('create credit note') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create credit note]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update credit note]" name="permissions[update credit note]" type="checkbox" {{ $member->hasPermission('update credit note') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update credit note]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete credit note]" name="permissions[delete credit note]" type="checkbox" {{ $member->hasPermission('delete credit note') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete credit note]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.customers') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view customers]" name="permissions[view customers]" type="checkbox" {{ $member->hasPermission('view customers') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view customers]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create customer]" name="permissions[create customer]" type="checkbox" {{ $member->hasPermission('create customer') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create customer]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update customer]" name="permissions[update customer]" type="checkbox" {{ $member->hasPermission('update customer') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update customer]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete customer]" name="permissions[delete customer]" type="checkbox" {{ $member->hasPermission('delete customer') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete customer]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.custom_fields') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view custom fields]" name="permissions[view custom fields]" type="checkbox" {{ $member->hasPermission('view custom fields') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view custom fields]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create custom field]" name="permissions[create custom field]" type="checkbox" {{ $member->hasPermission('create custom field') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create custom field]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update custom field]" name="permissions[update custom field]" type="checkbox" {{ $member->hasPermission('update custom field') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update custom field]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete custom field]" name="permissions[delete custom field]" type="checkbox" {{ $member->hasPermission('delete custom field') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete custom field]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.estimate_settings') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view estimate settings]" name="permissions[view estimate settings]" type="checkbox" {{ $member->hasPermission('view estimate settings') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view estimate settings]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update estimate settings]" name="permissions[update estimate settings]" type="checkbox" {{ $member->hasPermission('update estimate settings') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update estimate settings]">{{ __('messages.update') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.estimates') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view estimates]" name="permissions[view estimates]" type="checkbox" {{ $member->hasPermission('view estimates') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view estimates]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create estimate]" name="permissions[create estimate]" type="checkbox" {{ $member->hasPermission('create estimate') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create estimate]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update estimate]" name="permissions[update estimate]" type="checkbox" {{ $member->hasPermission('update estimate') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update estimate]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete estimate]" name="permissions[delete estimate]" type="checkbox" {{ $member->hasPermission('delete estimate') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete estimate]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.expense_categories') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view expense categories]" name="permissions[view expense categories]" type="checkbox" {{ $member->hasPermission('view expense categories') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view expense categories]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create expense category]" name="permissions[create expense category]" type="checkbox" {{ $member->hasPermission('create expense category') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create expense category]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update expense category]" name="permissions[update expense category]" type="checkbox" {{ $member->hasPermission('update expense category') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update expense category]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete expense category]" name="permissions[delete expense category]" type="checkbox" {{ $member->hasPermission('delete expense category') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete expense category]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.invoice_settings') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view invoice settings]" name="permissions[view invoice settings]" type="checkbox" {{ $member->hasPermission('view invoice settings') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view invoice settings]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update invoice settings]" name="permissions[update invoice settings]" type="checkbox" {{ $member->hasPermission('update invoice settings') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update invoice settings]">{{ __('messages.update') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.invoices') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view invoices]" name="permissions[view invoices]" type="checkbox" {{ $member->hasPermission('view invoices') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view invoices]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create invoice]" name="permissions[create invoice]" type="checkbox" {{ $member->hasPermission('create invoice') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create invoice]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update invoice]" name="permissions[update invoice]" type="checkbox" {{ $member->hasPermission('update invoice') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update invoice]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete invoice]" name="permissions[delete invoice]" type="checkbox" {{ $member->hasPermission('delete invoice') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete invoice]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.online_payment_gateways') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view online payment gateways]" name="permissions[view online payment gateways]" type="checkbox" {{ $member->hasPermission('view online payment gateways') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view online payment gateways]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update online payment gateway]" name="permissions[update online payment gateway]" type="checkbox" {{ $member->hasPermission('update online payment gateway') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update online payment gateway]">{{ __('messages.update') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.payments') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view payments]" name="permissions[view payments]" type="checkbox" {{ $member->hasPermission('view payments') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view payments]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create payment]" name="permissions[create payment]" type="checkbox" {{ $member->hasPermission('create payment') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create payment]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update payment]" name="permissions[update payment]" type="checkbox" {{ $member->hasPermission('update payment') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update payment]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete payment]" name="permissions[delete payment]" type="checkbox" {{ $member->hasPermission('delete payment') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete payment]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.payment_settings') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view payment settings]" name="permissions[view payment settings]" type="checkbox" {{ $member->hasPermission('view payment settings') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view payment settings]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update payment settings]" name="permissions[update payment settings]" type="checkbox" {{ $member->hasPermission('update payment settings') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update payment settings]">{{ __('messages.update') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.payment_types') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view payment types]" name="permissions[view payment types]" type="checkbox" {{ $member->hasPermission('view payment types') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view payment types]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create payment type]" name="permissions[create payment type]" type="checkbox" {{ $member->hasPermission('create payment type') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create payment type]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update payment type]" name="permissions[update payment type]" type="checkbox" {{ $member->hasPermission('update payment type') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update payment type]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete payment type]" name="permissions[delete payment type]" type="checkbox" {{ $member->hasPermission('delete payment type') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete payment type]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.products') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view products]" name="permissions[view products]" type="checkbox" {{ $member->hasPermission('view products') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view products]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create product]" name="permissions[create product]" type="checkbox" {{ $member->hasPermission('create product') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create product]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update product]" name="permissions[update product]" type="checkbox" {{ $member->hasPermission('update product') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update product]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete product]" name="permissions[delete product]" type="checkbox" {{ $member->hasPermission('delete product') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete product]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.product_settings') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view product settings]" name="permissions[view product settings]" type="checkbox" {{ $member->hasPermission('view product settings') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view product settings]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update product settings]" name="permissions[update product settings]" type="checkbox" {{ $member->hasPermission('update product settings') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update product settings]">{{ __('messages.update') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.product_units') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view product units]" name="permissions[view product units]" type="checkbox" {{ $member->hasPermission('view product units') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view product units]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create product unit]" name="permissions[create product unit]" type="checkbox" {{ $member->hasPermission('create product unit') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create product unit]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update product unit]" name="permissions[update product unit]" type="checkbox" {{ $member->hasPermission('update product unit') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update product unit]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete product unit]" name="permissions[delete product unit]" type="checkbox" {{ $member->hasPermission('delete product unit') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete product unit]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.reports') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view customer sales report]" name="permissions[view customer sales report]" type="checkbox" {{ $member->hasPermission('view customer sales report') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view customer sales report]">{{ __('messages.customer_sales') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view expenses report]" name="permissions[view expenses report]" type="checkbox" {{ $member->hasPermission('view expenses report') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view expenses report]">{{ __('messages.expenses') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view product sales report]" name="permissions[view product sales report]" type="checkbox" {{ $member->hasPermission('view product sales report') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view product sales report]">{{ __('messages.product_sales') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view profit loss report]" name="permissions[view profit loss report]" type="checkbox" {{ $member->hasPermission('view profit loss report') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view profit loss report]">{{ __('messages.profit_loss') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view vendors report]" name="permissions[view vendors report]" type="checkbox" {{ $member->hasPermission('view vendors report') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view vendors report]">{{ __('messages.vendors') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.membership') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view membership]" name="permissions[view membership]" type="checkbox" {{ $member->hasPermission('view membership') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view membership]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update membership]" name="permissions[update membership]" type="checkbox" {{ $member->hasPermission('update membership') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update membership]">{{ __('messages.update') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.tax_types') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view tax types]" name="permissions[view tax types]" type="checkbox" {{ $member->hasPermission('view tax types') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view tax types]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create tax type]" name="permissions[create tax type]" type="checkbox" {{ $member->hasPermission('create tax type') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create tax type]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update tax type]" name="permissions[update tax type]" type="checkbox" {{ $member->hasPermission('update tax type') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update tax type]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete tax type]" name="permissions[delete tax type]" type="checkbox" {{ $member->hasPermission('delete tax type') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete tax type]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.team_members') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view team members]" name="permissions[view team members]" type="checkbox" {{ $member->hasPermission('view team members') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view team members]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create team member]" name="permissions[create team member]" type="checkbox" {{ $member->hasPermission('create team member') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create team member]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update team member]" name="permissions[update team member]" type="checkbox" {{ $member->hasPermission('update team member') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update team member]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete team member]" name="permissions[delete team member]" type="checkbox" {{ $member->hasPermission('delete team member') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete team member]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>

            <div class="col-3 mb-4">
                <h6>{{ __('messages.vendors') }}</h6>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[view vendors]" name="permissions[view vendors]" type="checkbox" {{ $member->hasPermission('view vendors') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[view vendors]">{{ __('messages.view') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[create vendor]" name="permissions[create vendor]" type="checkbox" {{ $member->hasPermission('create vendor') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[create vendor]">{{ __('messages.create') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[update vendor]" name="permissions[update vendor]" type="checkbox" {{ $member->hasPermission('update vendor') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[update vendor]">{{ __('messages.update') }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="permissions[delete vendor]" name="permissions[delete vendor]" type="checkbox" {{ $member->hasPermission('delete vendor') ? 'checked=""' : '' }}>
                        <label class="custom-control-label" for="permissions[delete vendor]">{{ __('messages.delete') }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>