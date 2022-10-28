<div class="mdk-drawer js-mdk-drawer" id="default-drawer" data-align="start" data-position="left">
    <div class="mdk-drawer__scrim"></div>
    <div class="mdk-drawer__content">
        <div class="sidebar sidebar-light sidebar-left simplebar" data-simplebar="init">
            <div class="simplebar-wrapper">
                <div class="simplebar-height-auto-observer-wrapper">
                    <div class="simplebar-height-auto-observer"></div>
                </div>
                <div class="simplebar-mask">
                    <div class="simplebar-offset">
                        <div class="simplebar-content">

                            @if($authUser->hasRole('super_admin'))
                                <div class="sidebar-heading sidebar-m-t">Super Admin Menu</div>
                                <ul class="sidebar-menu">
                                    <li class="sidebar-menu-item {{ $page == 'super_admin.dashboard' ? 'active' : ''}}">
                                        <a class="sidebar-menu-button" href="{{ route('super_admin.dashboard') }}">
                                            <span class="sidebar-menu-text">{{ __('messages.dashboard') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-menu-item {{ $page == 'super_admin.users' ? 'active' : ''}}">
                                        <a class="sidebar-menu-button" href="{{ route('super_admin.users') }}">
                                            <span class="sidebar-menu-text">{{ __('messages.users') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-menu-item {{ $page == 'super_admin.plans' ? 'active' : ''}}">
                                        <a class="sidebar-menu-button" href="{{ route('super_admin.plans') }}">
                                            <span class="sidebar-menu-text">{{ __('messages.plans') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-menu-item {{ $page == 'super_admin.pages' ? 'active' : ''}}">
                                        <a class="sidebar-menu-button" href="{{ route('super_admin.pages') }}">
                                            <span class="sidebar-menu-text">{{ __('messages.pages') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-menu-item {{ $page == 'super_admin.subscriptions' ? 'active' : ''}}">
                                        <a class="sidebar-menu-button" href="{{ route('super_admin.subscriptions') }}">
                                            <span class="sidebar-menu-text">{{ __('messages.subscriptions') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-menu-item {{ $page == 'super_admin.support_tickets' ? 'active' : ''}}">
                                        <a class="sidebar-menu-button" href="{{ route('super_admin.support_tickets') }}">
                                            <span class="sidebar-menu-text">{{ __('messages.support_tickets') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-menu-item {{ $page == 'super_admin.clients' ? 'active' : ''}}">
                                        <a class="sidebar-menu-button" href="{{ route('super_admin.clients') }}">
                                            <span class="sidebar-menu-text">{{ __('messages.clients') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-menu-item {{ $page == 'super_admin.invoices' ? 'active' : ''}}">
                                        <a class="sidebar-menu-button" href="{{ route('super_admin.invoices') }}">
                                            <span class="sidebar-menu-text">{{ __('messages.invoices') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-menu-item {{ $page == 'super_admin.orders' ? 'active' : ''}}">
                                        <a class="sidebar-menu-button" href="{{ route('super_admin.orders') }}">
                                            <span class="sidebar-menu-text">{{ __('messages.orders') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-menu-item {{ $page == 'super_admin.languages' ? 'active' : ''}}">
                                        <a class="sidebar-menu-button" href="{{ route('super_admin.languages') }}">
                                            <span class="sidebar-menu-text">{{ __('messages.languages') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-menu-item {{ str_contains($page, 'super_admin.settings.') ? 'active open' : ''}}">
                                        <a class="sidebar-menu-button {{ str_contains($page, 'super_admin.settings.') ? '' : 'collapsed'}}" data-toggle="collapse" href="#settings_menu" aria-expanded="false">
                                            <span class="sidebar-menu-text">{{ __('messages.settings') }}</span>
                                            <span class="ml-auto sidebar-menu-toggle-icon"></span>
                                        </a>
                                        <ul class="sidebar-submenu {{ str_contains($page, 'super_admin.settings.') ? 'collapse show' : 'collapse'}}" id="settings_menu" style="">
                                            <li class="sidebar-menu-item {{ $page == 'super_admin.settings.application' ? 'active' : ''}}">
                                                <a class="sidebar-menu-button" href="{{ route('super_admin.settings.application') }}">
                                                    <span class="sidebar-menu-text">{{ __('messages.application_settings') }}</span>
                                                </a>
                                            </li>
                                            <li class="sidebar-menu-item {{ $page == 'super_admin.settings.mail' ? 'active' : ''}}">
                                                <a class="sidebar-menu-button" href="{{ route('super_admin.settings.mail') }}">
                                                    <span class="sidebar-menu-text">{{ __('messages.mail_settings') }}</span>
                                                </a>
                                            </li>
                                            <li class="sidebar-menu-item {{ $page == 'super_admin.settings.mailbuxserver' ? 'active' : ''}}">
                                                <a class="sidebar-menu-button" href="{{ route('super_admin.settings.mailbuxserver') }}">
                                                    <span class="sidebar-menu-text">{{ __('messages.mailbuxserver_settings') }}</span>
                                                </a>
                                            </li>
                                            <li class="sidebar-menu-item {{ $page == 'super_admin.settings.payment' ? 'active' : ''}}">
                                                <a class="sidebar-menu-button" href="{{ route('super_admin.settings.payment') }}">
                                                    <span class="sidebar-menu-text">{{ __('messages.payment_settings') }}</span>
                                                </a>
                                            </li>
                                            <li class="sidebar-menu-item {{ $page == 'super_admin.settings.theme' ? 'active' : ''}}">
                                                <a class="sidebar-menu-button" href="{{ route('super_admin.settings.theme', get_system_setting('theme')) }}">
                                                    <span class="sidebar-menu-text">{{ __('messages.theme_settings') }}</span>
                                                </a>
                                            </li>
                                            <li class="sidebar-menu-item {{ $page == 'super_admin.settings.custom_css_js' ? 'active' : ''}}">
                                                <a class="sidebar-menu-button" href="{{ route('super_admin.settings.custom_css_js') }}">
                                                    <span class="sidebar-menu-text">{{ __('messages.custom_css_js') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="sidebar-menu-item d-xl-none d-md-none d-lg-none">
                                        <a class="sidebar-menu-button" href="{{ route('logout') }}">
                                            <span class="sidebar-menu-text">{{ __('messages.logout') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            @else
                            <div class="d-flex align-items-center sidebar-p-a border-bottom sidebar-account">
                                <a href="{{ route('settings.company', ['company_uid' => $currentCompany->uid]) }}" class="flex d-flex align-items-center text-underline-0 text-body">
                                    <span class="avatar mr-3">
                                        <img src="{{ $currentCompany->avatar }}" alt="avatar" class="avatar-img rounded">
                                    </span>
                                    <span class="flex d-flex flex-column">
                                        <strong>{{ $currentCompany->name }}</strong>
                                    </span>
                                </a>
                            </div>

                            <div class="sidebar-heading sidebar-m-t">Menu</div>
                            <ul class="sidebar-menu">
                                @can('view dashboard')
                                <li class="sidebar-menu-item {{ $page == 'dashboard' ? 'active' : ''}}">
                                    <a class="sidebar-menu-button" href="{{ route('dashboard', ['company_uid' => $currentCompany->uid]) }}">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons dashboard_cc_nav">dashboard</i>
                                        <span class="sidebar-menu-text">{{ __('messages.dashboard') }}</span>
                                    </a>
                                </li>
                                @endcan
                                @can('view customers')
                                <li class="sidebar-menu-item {{ $page == 'customers' ? 'active' : ''}}">
                                    <a class="sidebar-menu-button" href="{{ route('customers', ['company_uid' => $currentCompany->uid]) }}">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons account_box_cc_nav">account_box</i>
                                        <span class="sidebar-menu-text">{{ __('messages.customers') }}</span>
                                    </a>
                                </li>
                                @endcan
                                @can('view products')
                                <li class="sidebar-menu-item {{ $page == 'products' ? 'active' : ''}}">
                                    <a class="sidebar-menu-button" href="{{ route('products', ['company_uid' => $currentCompany->uid]) }}">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons store_cc_nav">store</i>
                                        <span class="sidebar-menu-text">{{ __('messages.products') }}</span>
                                    </a>
                                </li>
                                @endcan
                                @can('view invoices')
                                <li class="sidebar-menu-item {{ $page == 'invoices' ? 'active' : ''}}">
                                    <a class="sidebar-menu-button" href="{{ route('invoices', ['company_uid' => $currentCompany->uid]) }}">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons receipt_cc_nav">receipt</i>
                                        <span class="sidebar-menu-text">{{ __('messages.invoices') }}</span>
                                    </a>
                                </li>
                                @endcan
                                @can('view credit notes')
                                <li class="sidebar-menu-item {{ $page == 'credit_notes' ? 'active' : ''}}">
                                    <a class="sidebar-menu-button" href="{{ route('credit_notes', ['company_uid' => $currentCompany->uid]) }}">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons control_point_duplicate_cc_nav">control_point_duplicate</i>
                                        <span class="sidebar-menu-text">{{ __('messages.credit_notes') }}</span>
                                    </a>
                                </li>
                                @endcan
                                @can('view estimates')
                                <li class="sidebar-menu-item {{ $page == 'estimates' ? 'active' : ''}}">
                                    <a class="sidebar-menu-button" href="{{ route('estimates', ['company_uid' => $currentCompany->uid]) }}">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons description_cc_nav">description</i>
                                        <span class="sidebar-menu-text">{{ __('messages.estimates') }}</span>
                                    </a>
                                </li>
                                @endcan
                                @can('view payments')
                                <li class="sidebar-menu-item {{ $page == 'payments' ? 'active' : ''}}">
                                    <a class="sidebar-menu-button" href="{{ route('payments', ['company_uid' => $currentCompany->uid]) }}">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons payment_cc_nav">payment</i>
                                        <span class="sidebar-menu-text">{{ __('messages.payments') }}</span>
                                    </a>
                                </li>
                                @endcan
                                @can('view expenses')
                                <li class="sidebar-menu-item {{ $page == 'expenses' ? 'active' : ''}}">
                                    <a class="sidebar-menu-button" href="{{ route('expenses', ['company_uid' => $currentCompany->uid]) }}">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons monetization_on_cc_nav">monetization_on</i>
                                        <span class="sidebar-menu-text">{{ __('messages.expenses') }}</span>
                                    </a>
                                </li>
                                @endcan
                                @can('view vendors')
                                <li class="sidebar-menu-item {{ $page == 'vendors' ? 'active' : ''}}">
                                    <a class="sidebar-menu-button" href="{{ route('vendors', ['company_uid' => $currentCompany->uid]) }}">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons local_shipping_cc_nav">local_shipping</i>
                                        <span class="sidebar-menu-text">{{ __('messages.vendors') }}</span>
                                    </a>
                                </li>
                                @endcan


                                <li class="sidebar-menu-item {{ $page == 'settings' ? 'active' : ''}}">
                                    <a class="sidebar-menu-button" href="{{ route('settings.account', ['company_uid' => $currentCompany->uid]) }}">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons settings_cc_nav">settings</i>
                                        <span class="sidebar-menu-text">{{ __('messages.settings') }}</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item d-xl-none d-md-none d-lg-none">
                                    <a class="sidebar-menu-button" href="{{ route('logout') }}">
                                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons exit_to_app_cc_nav">exit_to_app</i>
                                        <span class="sidebar-menu-text">{{ __('messages.logout') }}</span>
                                    </a>
                                </li>
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="simplebar-placeholder"></div>
            </div>
        </div>
    </div>
</div>