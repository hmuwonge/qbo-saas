@php
    use App\Facades\Utility;
    $users = \Auth::user();
    $currantLang = $users->currentLanguage();
    $languages = Utility::languages();
@endphp
<nav class="dash-sidebar bg-gray-4000 {{ Utility::getsettings('transparent_layout') == 1 ? 'transparent-bg' : '' }}">
    <div class="navbar-wrapper">
        <div class="m-headers logo-col">
            <a href="{{ route('home') }}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                @if ($users->dark_layout == 1)
                    <img
                        src="{{ Utility::getsettings('app_logo') ? Utility::getpath('logo/app-logo.png') : asset('assets/images/logo/app-logo.png') }}"
                        class="footer-light-logo">
                @else
                    <img
                        src="{{ Utility::getsettings('app_dark_logo') ? Utility::getpath('logo/app-dark-logo.png') : asset('assets/images/logo/app-dark-logo.png') }}"
                        class="footer-dark-logo">
                @endif
            </a>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar">
                <li class="dash-item dash-hasmenu">
                    <a href="{{ route('home') }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-home"></i></span>
                        <span class="dash-mtext">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                @if ($users->type == 'Super Admin')
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('users*') || request()->is('roles*') ? 'active dash-trigger' : 'collapsed' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-layout-2"></i></span><span
                                class="dash-mtext">{{ __('User Management') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @can('manage-user')
                                <li class="dash-item {{ request()->is('users*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('users.index') }}">{{ __('Admins') }}</a>
                                </li>
                            @endcan
                            @can('manage-role')
                                <li class="dash-item {{ request()->is('roles*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('roles.index') }}">{{ __('Roles') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('request-domain*') || request()->is('change-domain*') ? 'active dash-trigger' : 'collapsed' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-lock"></i></span><span
                                class="dash-mtext">{{ __('Domain Management') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @can('manage-domain-request')
                                <li class="dash-item {{ request()->is('request-domain*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('request.domain.index') }}">{{ __('Domain Requests') }}</a>
                                </li>
                            @endcan
                            @can('manage-domain-request')
                                <li class="dash-item {{ request()->is('change-domain*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('changedomain') }}">{{ __('Change Domain') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('coupon*') || request()->is('plans*') ? 'active dash-trigger' : 'collapsed' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-gift"></i></span><span
                                class="dash-mtext">{{ __('Subscription') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @can('manage-coupon')
                                <li class="dash-item {{ request()->is('coupon*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('coupon.index') }}">{{ __('Coupons') }}</a>
                                </li>
                            @endcan
                            @can('manage-plan')
                                <li
                                    class="dash-item {{ request()->is('plans*') || request()->is('payment*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('plans.index') }}">{{ __('Plans') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('offline*') || request()->is('sales*') ? 'active dash-trigger' : 'collapsed' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-clipboard-check"></i></span><span
                                class="dash-mtext">{{ __('Payment') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            <li class="dash-item {{ request()->is('offline*') ? 'active' : '' }}">
                                <a class="dash-link"
                                   href="{{ route('offline.index') }}">{{ __('Offline Payments') }}</a>
                            </li>
                            <li class="dash-item {{ request()->is('sales*') ? 'active' : '' }}">
                                <a class="dash-link" href="{{ route('sales.index') }}">{{ __('Transactions') }}</a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('support-ticket*') || request()->is('announcement*') ? 'active dash-trigger' : 'collapsed' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-database"></i></span><span
                                class="dash-mtext">{{ __('Support') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            <li class="dash-item {{ request()->is('support-ticket*') ? 'active' : '' }}">
                                <a class="dash-link"
                                   href="{{ route('support-ticket.index') }}">{{ __('Support Tickets') }}</a>
                            </li>
                            @can('manage-announcement')
                                <li class="dash-item {{ request()->is('announcement*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('announcement.index') }}">{{ __('Announcement') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    @can('manage-activity-log')
                        <li class="dash-item dash-hasmenu {{ request()->is('activity-log*') ? 'active' : '' }}">
                            <a href="{{ route('activity.log.index') }}" class="dash-link">
                                <span class="dash-micon">
                                    <i class="ti ti-activity">
                                    </i>
                                </span>
                                <span class="dash-mtext">{{ __('Activity Log') }}
                                </span>
                            </a>
                        </li>
                    @endcan
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('landingpage-setting*') ||
                        request()->is('faqs*') ||
                        request()->is('testimonial*') ||
                        request()->is('pagesetting*')
                            ? 'active dash-trigger'
                            : 'collapsed' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-table"></i></span><span
                                class="dash-mtext">{{ __('Frontend Setting') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @can('manage-landingpage')
                                <li class="dash-item {{ request()->is('landingpage-setting*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('landingpage.setting') }}">{{ __('Landing Page') }}</a>
                                </li>
                            @endcan
                            @can('manage-faqs')
                                <li class="dash-item {{ request()->is('faqs*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('faqs.index') }}">{{ __('Faqs') }}</a>
                                </li>
                            @endcan
                            @can('manage-testimonial')
                                <li class="dash-item {{ request()->is('testimonial*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('testimonial.index') }}">{{ __('Testimonials') }}</a>
                                </li>
                            @endcan
                            @can('manage-page-setting')
                                <li class="dash-item {{ request()->is('pagesetting*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('pagesetting.index') }}">{{ __('Page Settings') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('email-template*') || request()->is('manage-language*') || request()->is('create-language*') || request()->is('sms-template*') || request()->is('settings*') ? 'active dash-trigger' : 'collapsed' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-apps"></i></span><span
                                class="dash-mtext">{{ __('Account Setting') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @can('manage-email-template')
                                <li class="dash-item {{ request()->is('email-template*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('email-template.index') }}">{{ __('Email Templates') }}</a>
                                </li>
                            @endcan
                            @can('manage-sms-template')
                                <li class="dash-item {{ request()->is('sms-template*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('sms-template.index') }}">{{ __('Sms Templates') }}</a>
                                </li>
                            @endcan
                            @can('manage-langauge')
                                <li
                                    class="dash-item {{ request()->is('manage-language*') || request()->is('create-language*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('manage.language', [$currantLang]) }}">{{ __('Manage Languages') }}</a>
                                </li>
                            @endcan
                            @can('manage-setting')
                                <li class="dash-item {{ request()->is('settings*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('settings') }}">{{ __('Settings') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                @if ($users->type != 'Super Admin')
                    @canany(['manage-user', 'manage-role'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('users*') || request()->is('roles*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-layout-2"></i></span><span
                                    class="dash-mtext">{{ __('User Management') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-user')
                                    <li class="dash-item {{ request()->is('users*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('users.index') }}">{{ __('Users') }}</a>
                                    </li>
                                @endcan
                                @can('manage-role')
                                    <li class="dash-item {{ request()->is('roles*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('roles.index') }}">{{ __('Roles') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @can('manage-event')
                        <li class="dash-item dash-hasmenu {{ request()->is('event*') ? 'active' : '' }}">
                            <a class="dash-link" href="{{ route('event.index') }}"><span class="dash-micon">
                                    <i class="ti ti-calendar"></i></span>
                                <span class="dash-mtext">{{ __('Event') }}</span>
                            </a>
                        </li>
                    @endcan
                    @canany(['manage-document'])
                        @if (Auth::user()->type != 'Super Admin')
                            <li class="dash-item dash-hasmenu {{ request()->is('document*') ? 'active' : '' }}">
                                <a href="{{ route('document.index') }}" class="dash-link">
                                    <span class="dash-micon">
                                        <i class="ti ti-clipboard">
                                        </i>
                                    </span>
                                    <span class="dash-mtext">{{ __('Documents') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                    @endcanany
                    @canany(['manage-blog', 'manage-category'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('blogs*') || request()->is('category*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-forms"></i></span><span
                                    class="dash-mtext">{{ __('Blog') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-blog')
                                    <li class="dash-item {{ request()->is('blogs*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('blogs.index') }}">{{ __('Blogs') }}</a>
                                    </li>
                                @endcan
                                @can('manage-category')
                                    <li class="dash-item {{ request()->is('category*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                           href="{{ route('category.index') }}">{{ __('Categories') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @canany(['manage-coupon', 'manage-plan'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('coupon*') || request()->is('plans*') || request()->is('myplan*') || request()->is('payment*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-gift"></i></span><span
                                    class="dash-mtext">{{ __('Subscription') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-coupon')
                                    <li class="dash-item {{ request()->is('coupon*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('coupon.index') }}">{{ __('Coupons') }}</a>
                                    </li>
                                @endcan
                                @can('manage-plan')
                                    <li
                                        class="dash-item {{ request()->is('plans*') || request()->is('payment*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('plans.index') }}">{{ __('Plans') }}</a>
                                    </li>
                                @endcan
                                @if ($users->type == 'Admin')
                                    <li class="dash-item {{ request()->is('myplan*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                           href="{{ route('plans.myplan') }}">{{ __('My Plans') }}</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endcanany
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('offline*') || request()->is('sales*') ? 'active dash-trigger' : 'collapsed' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-clipboard-check"></i></span><span
                                class="dash-mtext">{{ __('Payment') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            <li class="dash-item {{ request()->is('offline*') ? 'active' : '' }}">
                                <a class="dash-link"
                                   href="{{ route('offline.index') }}">{{ __('Offline Payments') }}</a>
                            </li>
                            <li class="dash-item {{ request()->is('sales*') ? 'active' : '' }}">
                                <a class="dash-link"
                                   href="{{ route('sales.index') }}">{{ __('Transactions') }}</a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('chat*') || request()->is('show-announcement*') || request()->is('show-announcement-list*') || request()->is('support-ticket*') ? 'active dash-trigger' : 'collapsed' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-database"></i></span><span
                                class="dash-mtext">{{ __('Support') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @if (Utility::getsettings('pusher_status') == '1')
                                <li class="dash-item {{ request()->is('chat*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('chat') }}">{{ __('Chats') }}</a>
                                </li>
                            @endif
                            <li class="dash-item {{ request()->is('support-ticket*') ? 'active' : '' }}">
                                <a class="dash-link"
                                   href="{{ route('support-ticket.index') }}">{{ __('Support Tickets') }}</a>
                            </li>
                            @can('manage-announcement')
                                <li
                                    class="dash-item {{ request()->is('show-announcement-list*') || request()->is('show-announcement*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('show.announcement.list') }}">{{ __('Show Announcement List') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    @can('manage-activity-log')
                        <li class="dash-item dash-hasmenu {{ request()->is('activity-log*') ? 'active' : '' }}">
                            <a href="{{ route('activity.log.index') }}" class="dash-link">
                                <span class="dash-micon">
                                    <i class="ti ti-activity">
                                    </i>
                                </span>
                                <span class="dash-mtext">{{ __('Activity Log') }}
                                </span>
                            </a>
                        </li>
                    @endcan
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('landingpage-setting*') ||
                        request()->is('faqs*') ||
                        request()->is('testimonial*') ||
                        request()->is('pagesetting*')
                            ? 'active dash-trigger'
                            : 'collapsed' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-table"></i></span><span
                                class="dash-mtext">{{ __('Frontend Setting') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @can('manage-landingpage')
                                <li class="dash-item {{ request()->is('landingpage-setting*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('landingpage.setting') }}">{{ __('Landing Page') }}</a>
                                </li>
                            @endcan
                            @can('manage-faqs')
                                <li class="dash-item {{ request()->is('faqs*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('faqs.index') }}">{{ __('Faqs') }}</a>
                                </li>
                            @endcan
                            @can('manage-testimonial')
                                <li class="dash-item {{ request()->is('testimonial*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('testimonial.index') }}">{{ __('Testimonials') }}</a>
                                </li>
                            @endcan
                            <li class="dash-item {{ request()->is('pagesetting*') ? 'active' : '' }}">
                                <a class="dash-link"
                                   href="{{ route('pagesetting.index') }}">{{ __('Page Settings') }}</a>
                            </li>
                        </ul>
                    </li>
                    @canany(['manage-setting', 'manage-email-template', 'manage-sms-template'])
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('email-template*') || request()->is('sms-template*') || request()->is('settings*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-apps"></i></span><span
                                    class="dash-mtext">{{ __('Account Setting') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-email-template')
                                    <li class="dash-item {{ request()->is('email-template*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                           href="{{ route('email-template.index') }}">{{ __('Email Templates') }}</a>
                                    </li>
                                @endcan
                                @can('manage-sms-template')
                                    <li class="dash-item {{ request()->is('sms-template*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                           href="{{ route('sms-template.index') }}">{{ __('Sms Templates') }}</a>
                                    </li>
                                @endcan
                                @can('manage-setting')
                                    <li class="dash-item {{ request()->is('settings*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ url('settings') }}">{{ __('Settings') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                        <span class="dash-mtext; mx-auto font-extrabold text-gray-50 items-center">{{ __('QUICKBOOKS SECTION') }}</span>
                        <li
                            class="dash-item dash-hasmenu bg-green-50 {{ request()->is('quickbooks*') || request()->is('invoices*') || request()->is('receipts*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a href="#!" class="dash-link">
                            <span class="dash-micon">
                                <i class="ti ti-apps"></i>
                            </span>

                                <span  class="dash-mtext">{{ __('Quickbooks Reports') }}</span>
                                <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="dash-submenu">
                                @can('manage-email-template')

                                    <li class="dash-item dash-hasmenu {{ request()->is('invoices*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                           href="#!">{{ __('Invoices') }}</a>

                                        <ul class="dash-submenu">
                                            <li class="dash-item {{ request()->is('invoices*') ? 'active' : '' }}">
                                                <a class="dash-link"
                                                   href="{{ route('qbo.invoices.all') }}">{{ __('All') }}
                                                </a>
                                            </li>

                                            <li class="dash-item {{ request()->is('fiscalised*') ? 'active' : '' }}">
                                                <a class="dash-link"
                                                   href="{{ route('qbo.invoices.ura') }}">{{ __('Fiscalised') }}
                                                </a>
                                            </li>

                                            <li class="dash-item {{ request()->is('validation-errors*') ? 'active' : '' }}">
                                                <a class="dash-link"
                                                   href="{{ route('qbo.invoices.errors') }}">{{ __('Validation Errors') }}
                                                </a>
                                            </li>

                                            <li class="dash-item {{ request()->is('failed-validations*') ? 'active' : '' }}">
                                                <a class="dash-link"
                                                   href="{{ route('qbo.invoices.failed') }}">{{ __('Failed Validations') }}
                                                </a>
                                            </li>

                                            <li class="dash-item {{ request()->is('passed-validations*') ? 'active' : '' }}">
                                                <a class="dash-link"
                                                   href="{{ route('qbo.invoices.passed') }}">{{ __('Passed Validations') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan
                                @can('manage-sms-template')
                                    <li class="dash-item {{ request()->is('quickbooks/receipts*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                           href="#!">{{ __('Receipts') }}</a>

                                        <ul class="dash-submenu">
                                            <li class="dash-item {{ request()->is('receipts*') ? 'active' : '' }}">
                                                <a class="dash-link"
                                                   href="{{ route('qbo.receipts.index') }}">{{ __('All') }}
                                                </a>
                                            </li>

                                            <li class="dash-item {{ request()->is('fiscalised*') ? 'active' : '' }}">
                                                <a class="dash-link"
                                                   href="{{ route('qbo.receipts.ura') }}">{{ __('Fiscalised') }}
                                                </a>
                                            </li>

                                            <li class="dash-item {{ request()->is('validation-errors*') ? 'active' : '' }}">
                                                <a class="dash-link"
                                                   href="{{ route('qbo.receipts.errors') }}">{{ __('Validation Errors') }}
                                                </a>
                                            </li>

                                            <li class="dash-item {{ request()->is('failed-validations*') ? 'active' : '' }}">
                                                <a class="dash-link"
                                                   href="{{ route('qbo.receipts.failed') }}">{{ __('Failed Validations') }}
                                                </a>
                                            </li>

                                            <li class="dash-item {{ request()->is('passed-validations*') ? 'active' : '' }}">
                                                <a class="dash-link"
                                                   href="{{ route('qbo.receipts.passed') }}">{{ __('Passed Validations') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan
                                    <li class="dash-item {{ request()->is('quickbooks/goods*') ? 'active' : '' }}">
                                        <a class="dash-link" href="{{ route('goods.all') }}">{{ __('Goods/Services') }}</a>
                                    </li>

                                    <li class="dash-item {{ request()->is('issued-credit-notes*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                           href="{{ route('purchases.index') }}">{{ __('Purchases') }}</a>
                                    </li>
                                    <li class="dash-item {{ request()->is('quickbooks/stockadjustments*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                           href="{{ route('qbo.stockadjustments') }}">{{ __('Stock Adjustments') }}</a>
                                    </li>

                                    <li class="dash-item {{ request()->is('quickbooks/creditmemos*') ? 'active' : '' }}">
                                        <a class="dash-link"
                                           href="{{ route('qbo.creditnotes.index') }}">{{ __('Credit Notes') }}</a>
                                    </li>
                            </ul>
                        </li>


                        <span class="ml-3.5 mx-auto font-extrabold text-gray-50 items-center">{{ __('URA EFRIS SECTION') }}</span>
                    {{--                    efris reports--}}
                    {{--                        @canany(['manage-setting', 'manage-email-template', 'manage-sms-template'])--}}
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('efris-ura*') || request()->is('issued-credit-notes*') || request()->is('fiscalised-invoices*') ? 'active dash-trigger' : 'collapsed' }}">
                        <a href="#!" class="dash-link">
                            <span class="dash-micon">
                                <i class="ti ti-apps"></i>
                            </span>

                            <span  class="dash-mtext">{{ __('Efris Reports') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul class="dash-submenu">
                            @can('manage-email-template')
                                <li class="dash-item {{ request()->is('fiscalised-invoices*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('ura.invoices') }}">{{ __('Invoices') }}</a>
                                </li>

                            @endcan
                            @can('manage-sms-template')
                                <li class="dash-item {{ request()->is('fiscalised-receipts*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('ura.receipts') }}">{{ __('Receipts') }}</a>
                                </li>
                            @endcan
                            @can('manage-setting')
                                <li class="dash-item {{ request()->is('goods-services*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('ura.goods') }}">{{ __('Goods/Services') }}</a>
                                </li>

                                <li class="dash-item {{ request()->is('issued-credit-notes*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                       href="{{ route('ura.creditnotes') }}">{{ __('Credit Notes') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                    {{--                        @endcanany--}}
                @endif
            </ul>
        </div>
    </div>
</nav>
