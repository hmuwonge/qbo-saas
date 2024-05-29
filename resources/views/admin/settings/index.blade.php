@php
    $lang = \App\Facades\UtilityFacades::getValByName('default_language');
    $roles = App\Models\Role::whereNotIn('name', ['Super Admin', 'Admin'])
        ->pluck('name', 'name')
        ->all();
    $primary_color = \App\Facades\UtilityFacades::getsettings('color');
$color = $primary_color ?? 'theme-4';
@endphp
@extends('layouts.main')
@section('title', __('Settings'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Settings') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top stick-top">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#app_setting" class="border-0 list-group-item list-group-item-action">
                                {{ __('App Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#general_setting" class="border-0 list-group-item list-group-item-action">
                                {{ __('General Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#domain_setting" class="border-0 list-group-item list-group-item-action">
                                {{ __('Change Domain Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#storage_setting" class="border-0 list-group-item list-group-item-action">
                                {{ __('Storage Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#email_setting" class="border-0 list-group-item list-group-item-action">
                                {{ __('Email Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#chat_setting" class="border-0 list-group-item list-group-item-action">
                                {{ __('Chat Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#cookie_setting" class="list-group-item list-group-item-action border-0">
                                {{ __('Cookie Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#cache_setting" class="list-group-item list-group-item-action border-0">
                                {{ __('Cache Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#seo_setting" class="list-group-item list-group-item-action border-0">
                                {{ __('SEO Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#google_calender_setting" class="border-0 list-group-item list-group-item-action">
                                {{ __('Google Calender Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#sms-setting" class="border-0 list-group-item list-group-item-action">
                                {{ __('Sms Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#notification_setting"
                                class="list-group-item list-group-item-action border-0">{{ __('Notification Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            <a href="#social_setting" class="border-0 list-group-item list-group-item-action">
                                {{ __('Social Setting') }}
                                <div class="float-end">
                                    <i class="ti ti-chevron-right"></i>
                                </div>
                            </a>
                            @if (\Auth::user()->type == 'Super Admin' || \Auth::user()->type == 'Admin')
                                <a href="#payment_setting" class="border-0 list-group-item list-group-item-action">
                                    {{ __('Payment Setting') }}
                                    <div class="float-end">
                                        <i class="ti ti-chevron-right"></i>
                                    </div>
                                </a>
                            @endif
                            @if (\Auth::user()->type == 'Super Admin' || \Auth::user()->type == 'Admin')
                                <a href="#qbo_settings" class="border-0 list-group-item list-group-item-action">
                                    {{ __('Quickbooks Setting') }}
                                    <div class="float-end">
                                        <i class="ti ti-chevron-right"></i>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="app_setting">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('App Setting') }}</h5>
                            </div>
                            {!! Form::open([
                                'route' => ['settings.appname.update'],
                                'method' => 'POST',
                                'enctype' => 'multipart/form-data',
                                'data-validate',
                            ]) !!}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('App Dark Logo') }}</h5>
                                            </div>
                                            <div class="pt-0 card-body">
                                                <div class="inner-content">
                                                    <div class="py-2 mt-4 text-center logo-content dark-logo-content">
                                                        <a href="{{ Utility::getpath(Utility::getsettings('app_dark_logo')) }}"
                                                            target="_blank">
                                                            <img src="{{ Utility::getpath(Utility::getsettings('app_dark_logo')) }}"
                                                                id="app_dark">
                                                        </a>
                                                    </div>
                                                    <div class="mt-3 text-center choose-files">
                                                        <label for="app_dark_logo">
                                                            <div class="bg-primary company_logo_update"> <i
                                                                    class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            {{ Form::file('app_dark_logo', ['class' => 'form-control file', 'id' => 'app_dark_logo', 'onchange' => "document.getElementById('app_dark').src = window.URL.createObjectURL(this.files[0])", 'data-filename' => 'app_dark_logo']) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('App Light Logo') }}</h5>
                                            </div>
                                            <div class="pt-0 card-body bg-primary">
                                                <div class="inner-content">
                                                    <div class="py-2 mt-4 text-center logo-content light-logo-content">
                                                        
                                                        <a href="{{ Utility::getpath(Utility::getsettings('app_logo')) }}"
                                                            target="_blank">
                                                            <img src="{{ Utility::getpath(Utility::getsettings('app_logo')) }}"
                                                                id="app_light" alt="{{Utility::getpath(Utility::getsettings('app_logo'))}}">
                                                        </a>
                                                    </div>
                                                    <div class="mt-3 text-center choose-files">
                                                        <label for="app_logo">
                                                            <div class="company_logo_update w-logo"> <i
                                                                    class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            {{ Form::file('app_logo', ['class' => 'form-control file', 'id' => 'app_logo', 'onchange' => "document.getElementById('app_light').src = window.URL.createObjectURL(this.files[0])", 'data-filename' => 'app_logo']) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('App Favicon Logo') }}</h5>
                                            </div>
                                            <div class="pt-0 card-body">
                                                <div class="inner-content">
                                                    <div class="py-2 mt-4 text-center logo-content">
                                                        <a href="{{ Utility::getpath(Utility::getsettings('favicon_logo')) }}"
                                                            target="_blank">
                                                            <img height="35px"
                                                                src="{{ Utility::getpath(Utility::getsettings('favicon_logo')) }}"
                                                                id="app_favicon">
                                                        </a>
                                                    </div>
                                                    <div class="mt-3 text-center choose-files">
                                                        <label for="favicon_logo">
                                                            <div class="bg-primary company_logo_update"> <i
                                                                    class="px-1 ti ti-upload"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            {{ Form::file('favicon_logo', ['class' => 'form-control file', 'id' => 'favicon_logo', 'onchange' => "document.getElementById('app_favicon').src = window.URL.createObjectURL(this.files[0])", 'data-filename' => 'favicon_logo']) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('app_name', __('Application Name'), ['class' => 'form-label']) }}
                                        {!! Form::text('app_name', Utility::getsettings('app_name'), [
                                            'class' => 'form-control',
                                            'placeholder' => __('Enter application name'),
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div id="general_setting">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('General Setting') }}</h5>
                            </div>
                            {!! Form::open([
                                'route' => ['settings.auth.settings.update'],
                                'method' => 'POST',
                                'data-validate',
                            ]) !!}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <strong class="d-block">{{ __('Two Factor Authentication') }}</strong>
                                                    {{ !Utility::getsettings('2fa') ? 'Activate' : 'Deactivate' }}
                                                    {{ __('Two Factor Authentication') }}
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    {!! Form::checkbox('two_factor_auth', null, Utility::getsettings('2fa') ? true : false, [
                                                        'data-toggle' => 'switchbutton',
                                                        'data-onstyle' => 'primary',
                                                    ]) !!}
                                                </div>
                                                @if (!extension_loaded('imagick'))
                                                    <small>
                                                        {{ __('Note: for 2FA your server must have Imagick.') }} <a
                                                            href="https://www.php.net/manual/en/book.imagick.php"
                                                            target="_new">{{ __('Imagick Document') }}</a>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <strong class="d-block">{{ __('Email Verify Setting') }}</strong>
                                                    {{ Utility::getsettings('email_verification') == 0 ? __('Activate') : __('Deactivate') }}
                                                    {{ __('Email Verification Setting For Application') }}
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    {!! Form::checkbox('email_verification', null, Utility::getsettings('email_verification') == 1 ? true : false, [
                                                        'data-toggle' => 'switchbutton',
                                                        'data-onstyle' => 'primary',
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <strong class="d-block">{{ __('Sms Verification') }}</strong>
                                                    {{ Utility::getsettings('sms_verification') == 0 ? __('Activate') : __('Deactivate') }}
                                                    {{ __('Sms Verification Setting For Application') }}
                                                </div>
                                                <div class="col-sm-4 text-end">
                                                    {!! Form::checkbox('sms_verification', null, Utility::getsettings('sms_verification') == '1' ? true : false, [
                                                        'data-onstyle' => 'primary',
                                                        'data-toggle' => 'switchbutton',
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <strong class="d-block">{{ __('Landing Page Setting') }}</strong>
                                                    {{ Utility::getsettings('landing_page_status') == '1' ? __('Deactivate') : __('Activate') }}
                                                    {{ __('Landing Page Setting For Application.') }}
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    {!! Form::checkbox(
                                                        'landing_page_status',
                                                        null,
                                                        Utility::getsettings('landing_page_status') == 1 ? true : false,
                                                        [
                                                            'data-toggle' => 'switchbutton',
                                                            'data-onstyle' => 'primary',
                                                        ],
                                                    ) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <strong class="d-block">{{ __('RTL Setting') }}</strong>
                                                    {{ Utility::getsettings('rtl') == '0' ? __('Activate') : __('Deactivate') }}
                                                    {{ __('Rtl Setting For Application.') }}
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    {!! Form::checkbox('rtl_setting', null, Utility::getsettings('rtl') == '1' ? true : false, [
                                                        'data-toggle' => 'switchbutton',
                                                        'data-onstyle' => 'primary',
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <strong class="d-block">{{ __('Register Setting') }}</strong>
                                                    {{ Utility::getsettings('register_setting') == '1' ? __('Deactivate') : __('Activate') }}
                                                    {{ __('Register Setting For Application.') }}
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    {!! Form::checkbox('register_setting', null, Utility::getsettings('register_setting') == 1 ? true : false, [
                                                        'data-toggle' => 'switchbutton',
                                                        'data-onstyle' => 'primary',
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h5 class="my-3">{{ __('Theme Customizer') }}</h5>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <h6>
                                                        <i data-feather="credit-card"
                                                            class="me-2"></i>{{ __('Primary color Settings') }}
                                                    </h6>
                                                    <hr class="my-2">
                                                    <div class="theme-color themes-color">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-1' ? 'active_color' : '' }}"
                                                            data-value="theme-1" onclick="check_theme('theme-1')"></a>
                                                        <input type="radio" class="theme_color tm-color" name="color"
                                                            value="theme-1">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-2' ? 'active_color' : '' }} "
                                                            data-value="theme-2" onclick="check_theme('theme-2')"></a>
                                                        <input type="radio" class="theme_color tm-color" name="color"
                                                            value="theme-2">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-3' ? 'active_color' : '' }}"
                                                            data-value="theme-3" onclick="check_theme('theme-3')"></a>
                                                        <input type="radio" class="theme_color tm-color" name="color"
                                                            value="theme-3">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-4' ? 'active_color' : '' }}"
                                                            data-value="theme-4" onclick="check_theme('theme-4')"></a>
                                                        <input type="radio" class="theme_color tm-color" name="color"
                                                            value="theme-4">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-5' ? 'active_color' : '' }}"
                                                            data-value="theme-5" onclick="check_theme('theme-5')"></a>
                                                        <input type="radio" class="theme_color tm-color" name="color"
                                                            value="theme-5">
                                                        <br>
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-6' ? 'active_color' : '' }}"
                                                            data-value="theme-6" onclick="check_theme('theme-6')"></a>
                                                        <input type="radio" class="theme_color tm-color" name="color"
                                                            value="theme-6">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-7' ? 'active_color' : '' }}"
                                                            data-value="theme-7" onclick="check_theme('theme-7')"></a>
                                                        <input type="radio" class="theme_color tm-color" name="color"
                                                            value="theme-7">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-8' ? 'active_color' : '' }}"
                                                            data-value="theme-8" onclick="check_theme('theme-8')"></a>
                                                        <input type="radio" class="theme_color tm-color" name="color"
                                                            value="theme-8">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-9' ? 'active_color' : '' }}"
                                                            data-value="theme-9" onclick="check_theme('theme-9')"></a>
                                                        <input type="radio" class="theme_color tm-color" name="color"
                                                            value="theme-9">
                                                        <a href="#!"
                                                            class="{{ $color == 'theme-10' ? 'active_color' : '' }}"
                                                            data-value="theme-10" onclick="check_theme('theme-10')"></a>
                                                        <input type="radio" class="theme_color tm-color" name="color"
                                                            value="theme-10">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <h6>
                                                        <i data-feather="layout"
                                                            class="me-2"></i>{{ __('Sidebar Settings') }}
                                                    </h6>
                                                    <hr class="my-2">
                                                    <div class="form-check form-switch">
                                                        {!! Form::checkbox(
                                                            'transparent_layout',
                                                            null,
                                                            Utility::getsettings('transparent_layout') == '1' ? true : false,
                                                            [
                                                                'id' => 'cust-theme-bg',
                                                                'class' => 'form-check-input',
                                                            ],
                                                        ) !!}
                                                        {!! Form::label('cust-theme-bg', __('Transparent layout'), ['class' => 'form-check-label f-w-600 pl-1 me-2']) !!}
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <h6>
                                                        <i data-feather="sun"
                                                            class="me-2"></i>{{ __('Layout Settings') }}
                                                    </h6>
                                                    <hr class="my-2">
                                                    <div class="mt-2 form-check form-switch">
                                                        {!! Form::checkbox('dark_mode', null, Utility::getsettings('dark_mode') == 'on' ? true : false, [
                                                            'id' => 'cust-darklayout',
                                                            'class' => 'form-check-input',
                                                        ]) !!}
                                                        {!! Form::label('cust-darklayout', __('Dark Layout'), ['class' => 'form-check-label f-w-600 pl-1 me-2']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('default_language', __('Default Language'), ['class' => 'form-label']) }}
                                            <select class="form-control" id="default_language" data-trigger
                                                name="default_language"
                                                placeholder="{{ __('This is a search placeholder') }}">
                                                @foreach (\App\Facades\UtilityFacades::languages() as $language)
                                                    <option @if ($lang == $language) selected @endif
                                                        value="{{ $language }}">
                                                        {{ Str::upper($language) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('date_format', __('Date Format'), ['class' => 'form-label']) }}
                                            <select name="date_format" id="date_format" class="form-control"
                                                data-trigger>
                                                <option value="M j, Y"
                                                    {{ Utility::getsettings('date_format') == 'M j, Y' ? 'selected' : '' }}>
                                                    {{ __('Jan 1, 2020') }}</option>
                                                <option value="d-M-y"
                                                    {{ Utility::getsettings('date_format') == 'd-M-y' ? 'selected' : '' }}>
                                                    {{ __('01-Jan-20') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('time_format', __('Time Format'), ['class' => 'form-label']) }}
                                            <select name="time_format" id="time_format" class="form-control"
                                                data-trigger>
                                                <option value="g:i A"
                                                    {{ Utility::getsettings('time_format') == 'g:i A' ? 'selected' : '' }}>
                                                    {{ __('hh:mm AM/PM') }}</option>
                                                <option value="H:i:s"
                                                    {{ Utility::getsettings('time_format') == 'H:i:s' ? 'selected' : '' }}>
                                                    {{ __('HH:mm:ss') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('roles', __('User Registration Role'), ['class' => 'form-label']) }}
                                            {!! Form::select('roles', $roles, Utility::getsettings('roles'), [
                                                'class' => 'form-control',
                                                'data-trigger',
                                            ]) !!}
                                            <div class="invalid-feedback">
                                                {{ __('Role is required') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="time_format" class="form-label">{{ __('Invoice Print Type') }}</label>
                                            <select name="invoice_print_type" class="form-select">
                                                <option value="A4"
                                                    {{ Utility::getsettings('invoice_print_type') == 'A4' ? 'selected' : '' }}>
                                                    {{ __('A4') }}</option>
                                                <option value="58mm"
                                                    {{ Utility::getsettings('invoice_print_type') == '58mm' ? 'selected' : '' }}>
                                                    {{ __('58 mmm') }}</option>

                                                <option value="72mm"
                                                    {{ Utility::getsettings('invoice_print_type') == '72mm' ? 'selected' : '' }}>
                                                    {{ __('72mm') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="time_format" class="form-label">{{ __('Industry Code') }}</label>
                                            <select name="industry_code" class="form-select">
                                                <option value="101"
                                                    {{ Utility::getsettings('industry_code') == '101' ? 'selected' : '' }}>
                                                    {{ __('General Industry') }}</option>
                                                <option value="102"
                                                    {{ Utility::getsettings('industry_code') == '102' ? 'selected' : '' }}>
                                                    {{ __('Export') }}</option>

                                                <option value="104"
                                                    {{ Utility::getsettings('industry_code') == '104' ? 'selected' : '' }}>
                                                    {{ __('Imported Service') }}</option>

                                                <option value="105"
                                                    {{ Utility::getsettings('industry_code') == '105' ? 'selected' : '' }}>
                                                    {{ __('Telecom') }}</option>

                                                <option value="106"
                                                    {{ Utility::getsettings('industry_code') == '106' ? 'selected' : '' }}>
                                                    {{ __('Stamp Duty') }}</option>

                                                <option value="107"
                                                    {{ Utility::getsettings('industry_code') == '107' ? 'selected' : '' }}>
                                                    {{ __('Hotel Service') }}</option>

                                                <option value="108"
                                                    {{ Utility::getsettings('industry_code') == '108' ? 'selected' : '' }}>
                                                    {{ __('Other Taxes') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="time_format" class="form-label">{{ __('Buyer Type') }}</label>
                                            <select name="buyer_type" class="form-select">
                                                <option value="1"
                                                    {{ Utility::getsettings('buyer_type') == '1' ? 'selected' : '' }}>
                                                    {{ __('Consumer') }}</option>
                                                <option value="0"
                                                    {{ Utility::getsettings('buyer_type') == '0' ? 'selected' : '' }}>
                                                    {{ __('Business') }}</option>

                                                <option value="2"
                                                    {{ Utility::getsettings('buyer_type') == '2' ? 'selected' : '' }}>
                                                    {{ __('Foreigner') }}</option>
                                                <option value="3"
                                                    {{ Utility::getsettings('buyer_type') == '3' ? 'selected' : '' }}>
                                                    {{ __('Government') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div id="domain_setting">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Send Change Domain Request Setting') }}</h5>
                            </div>
                            {!! Form::open([
                                'route' => ['settings.change.domain'],
                                'method' => 'POST',
                                'data-validate',
                            ]) !!}
                            <div class="card-body">
                                <div class="col-12">
                                    <p class="text-sm">
                                        {{ __('Note: If you want to change your domain name, send a request to the super admin to keep the domain name.') }}
                                    </p>
                                </div>
                                @if (isset($order) && $order->status == 0)
                                    <div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <span
                                                        class="p-2 px-3 badge rounded-pill bg-warning">{{ __('Request Pending') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    {{ Form::label('domain_name', __('Domain Name'), ['class' => 'form-label']) }}
                                                    {!! Form::text('domain_name', null, [
                                                        'class' => 'form-control',
                                                        'id' => 'domain_name',
                                                        'placeholder' => __('Enter domain name'),
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {!! Form::button(__('Send'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div id="storage_setting">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Storage Setting') }}</h5>
                                <small class="text-muted">{{ __('Aws,S3 Storage Configuration') }}</small>
                            </div>
                            {!! Form::open([
                                'route' => ['settings.s3.setting.update'],
                                'method' => 'POST',
                                'data-validate',
                            ]) !!}
                            <div class="card-body">
                                <small
                                class="text">{{ __('Notes: If you Add S3 & wasabi Storage settings you have to store images First.') }}</small>
                                <div class="form-group mt-2">
                                    <div class="d-flex">
                                        <div class="pe-2">
                                            {!! Form::radio('storage_type', 'local', Utility::getsettings('storage_type') == 'local' ? true : false, [
                                                'class' => 'btn-check',
                                                'id' => 'local-outlined',
                                            ]) !!}
                                            {!! Form::label('local-outlined', __('Local'), ['class' => 'btn btn-outline-primary']) !!}
                                        </div>
                                        <div class="pe-2">
                                            {!! Form::radio('storage_type', 's3', Utility::getsettings('storage_type') == 's3' ? true : false, [
                                                'class' => 'btn-check',
                                                'id' => 's3-outlined',
                                            ]) !!}
                                            {!! Form::label('s3-outlined', __('AWS S3'), ['class' => 'btn btn-outline-primary']) !!}
                                        </div>

                                        <div class="pe-2">
                                            {!! Form::radio('storage_type', 'wasabi', Utility::getsettings('storage_type') == 'wasabi' ? true : false, [
                                                'class' => 'btn-check',
                                                'id' => 'wasabi-outlined',
                                            ]) !!}
                                            {!! Form::label('wasabi-outlined', __('Wasabi'), ['class' => 'btn btn-outline-primary']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div
                                            class="local-setting {{ Utility::getsettings('storage_type') == 'local' ? 'block' : 'd-none' }}">
                                        </div>
                                        <div
                                            class="s3-setting {{ Utility::getsettings('storage_type') == 's3' ? 'block' : 'd-none' }}">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('s3_key', __('S3 Key'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_key', Utility::getsettings('s3_key'), [
                                                            'placeholder' => __('Enter s3 key'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('s3_secret', __('S3 Secret'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_secret', Utility::getsettings('s3_secret'), [
                                                            'placeholder' => __('Enter s3 secret'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('s3_region', __('S3 Region'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_region', Utility::getsettings('s3_region'), [
                                                            'placeholder' => __('Enter s3 region'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('s3_bucket', __('S3 Bucket'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_bucket', Utility::getsettings('s3_bucket'), [
                                                            'placeholder' => __('Enter s3 bucket'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('s3_url', __('S3 URL'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_url', Utility::getsettings('s3_url'), [
                                                            'placeholder' => __('Enter s3 url'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('s3_endpoint', __('S3 Endpoint'), ['class' => 'form-label']) }}
                                                        {!! Form::text('s3_endpoint', Utility::getsettings('s3_endpoint'), [
                                                            'placeholder' => __('Enter s3 endpoint'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="wasabi-setting {{ Utility::getsettings('storage_type') == 'wasabi' ? 'block' : 'd-none' }}">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_key', __('Wasabi Key'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_key', Utility::getsettings('wasabi_key'), [
                                                            'placeholder' => __('Enter wasabi key'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_secret', __('Wasabi Secret'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_secret', Utility::getsettings('wasabi_secret'), [
                                                            'placeholder' => __('Enter wasabi secret'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_region', __('Wasabi Region'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_region', Utility::getsettings('wasabi_region'), [
                                                            'placeholder' => __('Enter wasabi region'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_bucket', __('Wasabi Bucket'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_bucket', Utility::getsettings('wasabi_bucket'), [
                                                            'placeholder' => __('Enter wasabi bucket'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_url', __('Wasabi URL'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_url', Utility::getsettings('wasabi_url'), [
                                                            'placeholder' => __('Enter wasabi url'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        {{ Form::label('wasabi_root', __('Wasabi Endpoint'), ['class' => 'form-label']) }}
                                                        {!! Form::text('wasabi_root', Utility::getsettings('wasabi_root'), [
                                                            'placeholder' => __('Enter wasabi endpoint'),
                                                            'class' => 'form-control',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div id="email_setting">
                        <div class="card">
                            <div class="card-header">
                                {!! Form::open([
                                    'route' => ['settings.email.setting.update'],
                                    'method' => 'POST',
                                    'data-validate',
                                ]) !!}
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h5> {{ __('Email Setting') }}</h5>
                                        <small
                                            class="text-muted">{{ __('Email Smtp Settings, Notifications And Others Related To Email.') }}</small>
                                    </div>
                                    <div class="col-lg-4 d-flex justify-content-end">
                                        <div class="form-switch custom-switch-v1 d-inline-block">
                                            {!! Form::checkbox(
                                                'email_setting_enable',
                                                null,
                                                Utility::getsettings('email_setting_enable') == 'on' ? true : false,
                                                [
                                                    'class' => 'custom-control custom-switch form-check-input input-primary',
                                                    'data-onstyle' => 'primary',
                                                    'data-toggle' => 'switchbutton',
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('mail_mailer', __('Mail Mailer'), ['class' => 'form-label']) }}
                                            {!! Form::text('mail_mailer', Utility::getsettings('mail_mailer'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter mail mailer'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-label']) }}
                                            {!! Form::text('mail_host', Utility::getsettings('mail_host'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter mail host'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-label']) }}
                                            {!! Form::text('mail_port', Utility::getsettings('mail_port'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter mail port'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-label']) }}
                                            {!! Form::text('mail_username', Utility::getsettings('mail_username'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter mail username'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('mail_password', __('Mail Password'), ['class' => 'form-label']) }}
                                            <input class="form-control"
                                                value="{{ Utility::getsettings('mail_password') }}"
                                                placeholder="{{ __('Enter mail password') }}" name="mail_password"
                                                type="password" id="mail_password">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-label']) }}
                                            {!! Form::text('mail_encryption', Utility::getsettings('mail_encryption'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter mail encryption'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-label']) }}
                                            {!! Form::text('mail_from_address', Utility::getsettings('mail_from_address'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter mail from address'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-label']) }}
                                            {!! Form::text('mail_from_name', Utility::getsettings('mail_from_name'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter mail from name'),
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {!! Form::button(__('Send Test Mail'), [
                                        'class' => 'btn btn-info send_mail float-start',
                                        'data-url' => route('test.mail'),
                                        'id' => 'test-mail',
                                    ]) !!}
                                    {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <div id="chat_setting">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Chat Setting') }}</h5>
                            </div>
                            {!! Form::open([
                                'route' => ['settings.pusher.setting.update'],
                                'method' => 'POST',
                                'data-validate',
                            ]) !!}
                            <div class="card-body">
                                <p class="text-sm"> {{ __('Pusher Setting') }} <a href="https://pusher.com/"
                                        target="_blank">{{ __('Document') }}</a></p>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('pusher_id', __('Pusher App ID'), ['class' => 'form-label']) }}
                                            {!! Form::text('pusher_id', Utility::getsettings('pusher_id'), [
                                                'placeholder' => __('Enter pusher app id'),
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('pusher_key', __('Pusher Key'), ['class' => 'form-label']) }}
                                            {!! Form::text('pusher_key', Utility::getsettings('pusher_key'), [
                                                'placeholder' => __('Enter pusher key'),
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('pusher_secret', __('Pusher Secret'), ['class' => 'form-label']) }}
                                            {!! Form::text('pusher_secret', Utility::getsettings('pusher_secret'), [
                                                'placeholder' => __('Enter pusher secret'),
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('pusher_cluster', __('Pusher Cluster'), ['class' => 'form-label']) }}
                                            {!! Form::text('pusher_cluster', Utility::getsettings('pusher_cluster'), [
                                                'placeholder' => __('Enter pusher cluster'),
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label for="pusher_status"
                                                        class="form-label">{{ __('Status') }}</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="mt-2 form-switch float-end custom-switch-v1">
                                                        <input type="checkbox" name="pusher_status"
                                                            class="form-check-input input-primary" id="pusher_status"
                                                            {{ Utility::getsettings('pusher_status') ? 'checked' : 'unchecked' }}>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div id="cookie_setting">
                        <div class="card">
                            <div class="card-header">
                                {!! Form::open([
                                    'route' => 'settings.cookie.setting.update',
                                    'method' => 'Post',
                                    'enctype' => 'multipart/form-data',
                                    'data-validate',
                                ]) !!}
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h5> {{ __('Cookie Setting') }}</h5>
                                    </div>
                                    <div class="col-lg-4 d-flex justify-content-end">
                                        <div class="form-switch custom-switch-v1 d-inline-block">
                                            {!! Form::checkbox(
                                                'cookie_setting_enable',
                                                null,
                                                Utility::getsettings('cookie_setting_enable') == 'on' ? true : false,
                                                [
                                                    'class' => 'custom-control custom-switch form-check-input input-primary',
                                                    'data-onstyle' => 'primary',
                                                    'data-toggle' => 'switchbutton',
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch custom-switch-v1" id="cookie_log">
                                            <input type="checkbox" name="cookie_logging"
                                                class="form-check-input input-primary cookie_setting" id="cookie_logging"
                                                {{ Utility::getsettings('cookie_logging') == 'on' ? ' checked ' : '' }}>
                                            <label class="form-check-label" for="cookie_logging">
                                                {{ __('Enable logging') }}
                                            </label>
                                        </div>
                                        <small class="text">
                                            {{ __('Notes: After enabling logging, user cookie data will be stored in CSV file.') }}
                                        </small>
                                        <div class="form-group mt-2">
                                            {{ Form::label('cookie_title', __('Cookie Title'), ['class' => 'form-label']) }}
                                            {!! Form::text('cookie_title', Utility::getsettings('cookie_title'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter cookie title'),
                                            ]) !!}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('cookie_description', __('Cookie Description'), ['class' => 'form-label']) }}
                                            {!! Form::text('cookie_description', Utility::getsettings('cookie_description'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter cookie description'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch custom-switch-v1 my-2">
                                            <input type="checkbox" name="necessary_cookies"
                                                class="form-check-input input-primary cookie_setting"
                                                id="necessary_cookies"
                                                {{ Utility::getsettings('necessary_cookies') == 'on' ? ' checked ' : '' }}>
                                            <label class="form-check-label"
                                                for="necessary_cookies">{{ __('Strictly necessary cookies') }}</label>
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('strictly_cookie_title', __('Strictly Cookie Title'), ['class' => 'form-label']) }}
                                            {!! Form::text('strictly_cookie_title', Utility::getsettings('strictly_cookie_title'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter strictly cookie title'),
                                            ]) !!}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('strictly_cookie_description', __('Strictly Cookie Description'), ['class' => 'form-label']) }}
                                            {!! Form::text('strictly_cookie_description', Utility::getsettings('strictly_cookie_description'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter strictly cookie description'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <h5> {{ __('More Information') }}</h5>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('contact_us_description', __('Contact Us Description'), ['class' => 'form-label']) }}
                                            {!! Form::text('contact_us_description', Utility::getsettings('contact_us_description'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter contact us description'),
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {{ Form::label('contact_us_url', __('Contact Us Url'), ['class' => 'form-label']) }}
                                            {!! Form::text('contact_us_url', Utility::getsettings('contact_us_url'), [
                                                'class' => 'form-control',
                                                'placeholder' => __('Enter contact us url'),
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-6">
                                        @if (Utility::getsettings('cookie_logging') == 'on')
                                            @if (Storage::url('cookie-csv/cookie_data.csv'))
                                                <label for="file"
                                                    class="form-label">{{ __('Download cookie accepted data') }}</label>
                                                <a href="{{ Storage::url('cookie-csv/cookie_data.csv') }}"
                                                    class="btn btn-primary mr-3">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-6 text-end">
                                        <input class="btn btn-print-invoice btn-primary cookie_btn" type="submit"
                                            value="{{ __('Save Changes') }}">
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div id="cache_setting">
                        <div class="card">
                            <div class="card-header">
                                {!! Form::open([
                                    'route' => 'config.cache',
                                    'method' => 'Post',
                                    'data-validate',
                                ]) !!}
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h5> {{ __('Cache Setting') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 form-group">
                                        {{ Form::label('Current cache size', __('Current cache size'), ['class' => 'col-form-label']) }}
                                        <div class="input-group">
                                            <input type="text" class="form-control"
                                                value="{{ Utility::CacheSize() }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">{{ __('MB') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {{ Form::button(__('Cache Clear'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div id="seo_setting">
                        <div class="card">
                            <div class="card-header">
                                {!! Form::open([
                                    'route' => 'setting.seo.save',
                                    'method' => 'Post',
                                    'enctype' => 'multipart/form-data',
                                    'data-validate',
                                ]) !!}
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h5> {{ __('SEO Setting') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('meta_title', __('Meta Title'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('meta_title', Utility::getsettings('meta_title'), ['class' => 'form-control ', 'required', 'placeholder' => 'Meta Title']) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('meta_keywords', __('Meta Keywords'), ['class' => 'col-form-label']) }}
                                            {{ Form::textarea('meta_keywords', Utility::getsettings('meta_keywords'), ['class' => 'form-control ', 'required', 'placeholder' => 'Meta Keywords', 'rows' => 2]) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('meta_description', __('Meta Description'), ['class' => 'col-form-label']) }}
                                            {{ Form::textarea('meta_description', Utility::getsettings('meta_description'), ['class' => 'form-control ', 'required', 'placeholder' => 'Meta Description', 'rows' => 3]) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('Meta Image', __('Meta Image'), ['class' => 'col-form-label ms-4']) }}
                                            <div class="pt-0 card-body">
                                                <div class="setting_card">
                                                    <div class="logo-content">
                                                        <a href="{{ Utility::getsettings('meta_image_logo')
                                                        ? Utility::getpath(Utility::getsettings('meta_image_logo'))
                                                        : Storage::url('seeder-image/meta-image-logo.jpg') }}"
                                                            target="_blank">
                                                            <img id="meta-image-logo"
                                                                src="{{ Utility::getsettings('meta_image_logo')
                                                                ? Utility::getpath(Utility::getsettings('meta_image_logo'))
                                                                : Storage::url('seeder-image/meta-image-logo.jpg') }}">
                                                        </a>
                                                    </div>
                                                    <div class="mt-4 choose-files">
                                                        <label for="meta_image">
                                                            <div class="bg-primary logo input-img-div">
                                                                <i class="px-1 ti ti-upload"></i>
                                                                {{ __('Choose file here') }}
                                                                <input type="file"
                                                                    class="form-control file image-input"
                                                                    accept="image/png, image/gif, image/jpeg, image/jpg"
                                                                    id="meta_image" property="og:image"
                                                                    onchange="document.getElementById('meta-image-logo').src = window.URL.createObjectURL(this.files[0])"
                                                                    data-fileproperty="og:image">
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div id="google_calender_setting">
                        <div class="card">
                            <div class="col-md-12">
                                {{ Form::open([
                                    'route' => 'settings.google.calender.update',
                                    'enctype' => 'multipart/form-data',
                                ]) }}
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5>{{ __('Google Calendar Setting') }}</h5>
                                        </div>
                                        <div class="col-6 text-end">
                                            <div class="form-switch custom-switch-v1 d-inline-block">
                                                {!! Form::checkbox(
                                                    'google_setting_enable',
                                                    null,
                                                    Utility::getsettings('google_setting_enable') == 'on' ? true : false,
                                                    [
                                                        'class' => 'custom-control custom-switch form-check-input input-primary',
                                                        'data-onstyle' => 'primary',
                                                        'data-toggle' => 'switchbutton',
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                {{ Form::label('google_calender_id', __('Google Calendar Id'), ['class' => 'form-label']) }}
                                                {{ Form::text('google_calender_id', Utility::getsettings('google_calender_id'), ['class' => 'form-control', 'placeholder' => __('Enter google calendar id'), 'required' => 'required']) }}
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                {{ Form::label('google_calender_json_file', __('Google Calendar Json File'), ['class' => 'form-label']) }}
                                                {!! Form::file('google_calender_json_file', ['class' => 'form-control', 'id' => 'google_calender_json_file']) !!}
                                            </div>
                                        </div>
                                        @if (Utility::getsettings('google_calender_json_file'))
                                            <div class="col-lg-12">
                                                <a download
                                                    href="{{ Storage::url(tenant('id') . '/' . Utility::getsettings('google_calender_json_file')) }}"
                                                    class="btn btn-primary">{{ __('Download Json') }}</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-end">
                                        {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn-submit btn btn-primary']) !!}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    <div id="sms-setting">
                        <div class="card">
                            {!! Form::open([
                                'route' => 'settings.sms.setting.update',
                                'method' => 'POST',
                            ]) !!}
                            <div class="card-header">
                                <div class="row d-flex align-items-center">
                                    <div class="col-6 d-flex justify-content-start">
                                        <h5>{{ __('Sms Setting') }}</h5>
                                    </div>
                                    <div class="col-6 d-flex justify-content-end">
                                        <div class="form-switch custom-switch-v1 d-inline-block">
                                            {!! Form::checkbox(
                                                'sms_setting_enable',
                                                null,
                                                Utility::getsettings('sms_setting_enable') == 'on' ? true : false,
                                                [
                                                    'class' => 'custom-control custom-switch form-check-input input-primary',
                                                    'data-onstyle' => 'primary',
                                                    'data-toggle' => 'switchbutton',
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="d-flex">
                                                <div class="pe-2">
                                                    {!! Form::radio('smssetting', 'twilio', Utility::getsettings('smssetting') == 'twilio' ? true : false, [
                                                        'class' => 'btn-check',
                                                        'id' => 'smssetting_twilio',
                                                    ]) !!}
                                                    {{ Form::label('smssetting_twilio', __('Twilio'), ['class' => 'btn btn-outline-primary']) }}
                                                </div>
                                                <div class="pe-2">
                                                    {!! Form::radio('smssetting', 'nexmo', Utility::getsettings('smssetting') == 'nexmo' ? true : false, [
                                                        'class' => 'btn-check',
                                                        'id' => 'smssetting_nexmo',
                                                    ]) !!}
                                                    {{ Form::label('smssetting_nexmo', __('Nexmo'), ['class' => 'btn btn-outline-primary']) }}
                                                </div>
                                                <div class="pe-2">
                                                    {!! Form::radio('smssetting', 'fast2sms', Utility::getsettings('smssetting') == 'fast2sms' ? true : false, [
                                                        'class' => 'btn-check',
                                                        'id' => 'smssetting_fast2sms',
                                                    ]) !!}
                                                    {{ Form::label('smssetting_fast2sms', __('FAST2SMS'), ['class' => 'btn btn-outline-primary']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div id="twilio"
                                            class="desc {{ Utility::getsettings('smssetting') == 'twilio' ? 'block' : 'd-none' }}">
                                            <div>
                                                <div class="row">
                                                    <div class="form-group">
                                                        {{ Form::label('twilio_sid', __('Twilio SID'), ['class' => 'form-label']) }}
                                                        {!! Form::text('twilio_sid', Utility::getsettings('twilio_sid'), [
                                                            'class' => 'form-control',
                                                            'id' => 'twilio_sid',
                                                            'placeholder' => __('Enter twilio sid'),
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('twilio_auth_token', __('Twilio Auth Token'), ['class' => 'form-label']) }}
                                                        {!! Form::text('twilio_auth_token', Utility::getsettings('twilio_auth_token'), [
                                                            'class' => 'form-control',
                                                            'id' => 'twilio_auth_token',
                                                            'placeholder' => __('Enter twilio auth token'),
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('twilio_verify_sid', __('Twilio Verify SID'), ['class' => 'form-label']) }}
                                                        {!! Form::text('twilio_verify_sid', Utility::getsettings('twilio_verify_sid'), [
                                                            'class' => 'form-control',
                                                            'id' => 'twilio_verify_sid',
                                                            'placeholder' => __('Enter verify sid'),
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('twilio_number', __('Twilio Number'), ['class' => 'form-label']) }}
                                                        {!! Form::text('twilio_number', Utility::getsettings('twilio_number'), [
                                                            'class' => 'form-control',
                                                            'id' => 'twilio_number',
                                                            'placeholder' => __('Enter twilio number'),
                                                        ]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="nexmo"
                                            class="desc {{ Utility::getsettings('smssetting') == 'nexmo' ? 'block' : 'd-none' }}">
                                            <div>
                                                <div class="row">
                                                    <div class="form-group">
                                                        {{ Form::label('nexmo_key', __('Nexmo Key'), ['class' => 'form-label']) }}
                                                        {!! Form::text('nexmo_key', Utility::getsettings('nexmo_key'), [
                                                            'placeholder' => __('Enter nexmo key'),
                                                            'class' => 'form-control',
                                                            'id' => 'nexmo_key',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('nexmo_secret', __('Nexmo Secret'), ['class' => 'form-label']) }}
                                                        {!! Form::text('nexmo_secret', Utility::getsettings('nexmo_secret'), [
                                                            'placeholder' => __('Enter nexmo secret'),
                                                            'class' => 'form-control',
                                                            'id' => 'nexmo_secret',
                                                        ]) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('nexmo_url', __('Nexmo Url'), ['class' => 'form-label']) }}
                                                        {!! Form::text('nexmo_url', Utility::getsettings('nexmo_url'), [
                                                            'placeholder' => __('Enter nexmo url'),
                                                            'class' => 'form-control',
                                                            'id' => 'nexmo_url',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="fast2sms"
                                            class="desc {{ Utility::getsettings('smssetting') == 'fast2sms' ? 'block' : 'd-none' }}">
                                            <div class="row">
                                                <div class="form-group">
                                                    {{ Form::label('fast2sms_api_key', __('FAST2SMS Api Key'), ['class' => 'form-label']) }}
                                                    {!! Form::text('fast2sms_api_key', Utility::getsettings('fast2sms_api_key'), [
                                                        'placeholder' => __('Enter fast2sms api key'),
                                                        'class' => 'form-control',
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div id="notification_setting">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Notifications setting') }}</h5>
                                <small
                                    class="text-muted">{{ __('Here you can setup and manage your integration settings.') }}</small>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive mt-0">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Title') }}</th>
                                                <th class="w-auto text-end">{{ __('Email') }}</th>
                                                <th class="w-auto text-end">{{ __('Notification') }}</th>
                                            </tr>
                                        </thead>
                                        @foreach ($notificationsSettings as $notificationsSetting)
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <span name="title" class="form-control"
                                                                placeholder="Enter title"
                                                                value="{{ $notificationsSetting->id }}">
                                                                {{ $notificationsSetting->title }}</span>
                                                        </div>
                                                    </td>
                                                    @if ($notificationsSetting->email_notification != 2)
                                                        <td class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {!! Form::checkbox('email_notification', null, $notificationsSetting->email_notification == 1 ? true : false, [
                                                                    'class' => 'form-check-input chnageEmailNotifyStatus',
                                                                    'data-url' => route('notification.status.change', $notificationsSetting->id),
                                                                ]) !!}
                                                            </div>
                                                        </td>
                                                    @else
                                                        <td></td>
                                                    @endif
                                                    <td class="text-end">
                                                        <div class="form-check form-switch d-inline-block">
                                                            {!! Form::checkbox('notify', null, $notificationsSetting->notify == 1 ? true : false, [
                                                                'class' => 'form-check-input chnageNotifyStatus',
                                                                'data-url' => route('notification.status.change', $notificationsSetting->id),
                                                            ]) !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="social_setting">
                        <div class="card">
                            <div class="card-header">
                                <h5> {{ __('Social Setting') }}</h5>
                            </div>
                            <div class="card-body">
                                {!! Form::open([
                                    'route' => ['settings.social.setting.update'],
                                    'method' => 'POST',
                                    'data-validate',
                                ]) !!}
                                <div class="faq justify-content-center">
                                    <div class="row">
                                        <div class="col-sm-12 col-xxl-12">
                                            <div class="accordion accordion-flush" id="accordionExamples">
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading111">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse111"
                                                            aria-expanded="true" aria-controls="collapse111">
                                                            <span class="flex-1 d-flex align-items-center">
                                                                <i class="ti ti-brand-google text-primary"></i>
                                                                {{ __('Google') }}
                                                            </span>
                                                            @if (Utility::getsettings('googlesetting') == 'on')
                                                                <a
                                                                    class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                            @endif
                                                        </button>
                                                    </h2>
                                                    <div id="collapse111" class="accordion-collapse collapse"
                                                        aria-labelledby="heading111" data-bs-parent="#accordionExamples">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="py-2 col-6">
                                                                    <p class="text-sm">
                                                                        {{ __('How To Enable Login With Google') }}
                                                                        <a href="{{ Storage::url('pdf/login with google.pdf') }}"
                                                                            target="_blank">{{ __('Document') }}</a>
                                                                    </p>
                                                                </div>
                                                                <div class="py-2 col-6 text-end">
                                                                    <div class="form-check form-switch d-inline-block">
                                                                        {!! Form::checkbox('socialsetting[]', 'google', Utility::getsettings('googlesetting') == 'on' ? true : false, [
                                                                            'class' => 'form-check-input mx-2',
                                                                            'id' => 'is_google_enabled',
                                                                        ]) !!}
                                                                        {{ Form::label('is_google_enabled', __('Enable'), ['class' => 'form-check-label']) }}
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        {{ Form::label('google_client_id', __('Google Client Id'), ['class' => 'form-label']) }}
                                                                        {!! Form::text('google_client_id', Utility::getsettings('google_client_id'), [
                                                                            'placeholder' => __('Enter google client id'),
                                                                            'class' => 'form-control',
                                                                        ]) !!}

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        {{ Form::label('google_client_secret', __('Google Client Secret'), ['class' => 'form-label']) }}
                                                                        {!! Form::text('google_client_secret', Utility::getsettings('google_client_secret'), [
                                                                            'placeholder' => __('Enter google client secret'),
                                                                            'class' => 'form-control',
                                                                        ]) !!}

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        {{ Form::label('google_redirect', __('Google Redirect Url'), ['class' => 'form-label']) }}
                                                                        {!! Form::text('google_redirect', Utility::getsettings('google_redirect'), [
                                                                            'placeholder' => __('https://demo.test.com/callback/google'),
                                                                            'class' => 'form-control',
                                                                        ]) !!}

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading112">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse112"
                                                            aria-expanded="true" aria-controls="collapse112">
                                                            <span class="flex-1 d-flex align-items-center">
                                                                <i class="ti ti-brand-facebook text-primary"></i>
                                                                {{ __('Facebook') }}
                                                            </span>
                                                            @if (Utility::getsettings('facebooksetting') == 'on')
                                                                <a
                                                                    class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                            @endif
                                                        </button>
                                                    </h2>
                                                    <div id="collapse112" class="accordion-collapse collapse"
                                                        aria-labelledby="heading112" data-bs-parent="#accordionExamples">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="py-2 col-6">
                                                                    <p class="text-sm">
                                                                        {{ __('How To Enable Login With Facebook') }}
                                                                        <a href="{{ Storage::url('pdf/login with facebook.pdf') }}"
                                                                            target="_blank">{{ __('Document') }}</a>
                                                                    </p>
                                                                </div>
                                                                <div class="py-2 col-6 text-end">
                                                                    <div class="form-check form-switch d-inline-block">
                                                                        {!! Form::checkbox(
                                                                            'socialsetting[]',
                                                                            'facebook',
                                                                            Utility::getsettings('facebooksetting') == 'on' ? true : false,
                                                                            [
                                                                                'class' => 'form-check-input mx-2',
                                                                                'id' => 'is_facebook_enabled',
                                                                            ],
                                                                        ) !!}
                                                                        {{ Form::label('is_facebook_enabled', __('Enable'), ['class' => 'form-check-label']) }}
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        {{ Form::label('facebook_client_id', __('Facebook Client Id'), ['class' => 'form-label']) }}
                                                                        {!! Form::text('facebook_client_id', Utility::getsettings('facebook_client_id'), [
                                                                            'placeholder' => __('Enter facebook client id'),
                                                                            'class' => 'form-control',
                                                                        ]) !!}

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        {{ Form::label('facebook_client_secret', __('Facebook Client Secret'), ['class' => 'form-label']) }}
                                                                        {!! Form::text('facebook_client_secret', Utility::getsettings('facebook_client_secret'), [
                                                                            'placeholder' => __('Enter facebook client secret'),
                                                                            'class' => 'form-control',
                                                                        ]) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        {{ Form::label('facebook_redirect', __('Facebook Redirect Url'), ['class' => 'form-label']) }}
                                                                        {!! Form::text('facebook_redirect', Utility::getsettings('facebook_redirect'), [
                                                                            'placeholder' => __('https://demo.test.com/callback/facebook'),
                                                                            'class' => 'form-control',
                                                                        ]) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="accordion-item card">
                                                    <h2 class="accordion-header" id="heading113">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse113"
                                                            aria-expanded="true" aria-controls="collapse113">
                                                            <span class="flex-1 d-flex align-items-center">
                                                                <i class="ti ti-brand-github text-primary"></i>
                                                                {{ __('Github') }}
                                                            </span>
                                                            @if (Utility::getsettings('githubsetting') == 'on')
                                                                <a
                                                                    class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                            @endif
                                                        </button>
                                                    </h2>
                                                    <div id="collapse113" class="accordion-collapse collapse"
                                                        aria-labelledby="heading113" data-bs-parent="#accordionExamples">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="py-2 col-6">
                                                                    <p class="text-sm">
                                                                        {{ __('How To Enable Login With Github') }}
                                                                        <a href="{{ Storage::url('pdf/login with github.pdf') }}"
                                                                            target="_blank">{{ __('Document') }}
                                                                        </a>
                                                                    </p>
                                                                </div>
                                                                <div class="py-2 col-6 text-end">
                                                                    <div class="form-check form-switch d-inline-block">
                                                                        {!! Form::checkbox('socialsetting[]', 'github', Utility::getsettings('githubsetting') == 'on' ? true : false, [
                                                                            'class' => 'form-check-input mx-2',
                                                                            'id' => 'is_github_enabled',
                                                                        ]) !!}
                                                                        {{ Form::label('is_github_enabled', __('Enable'), ['class' => 'form-check-label']) }}
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        {{ Form::label('github_client_id', __('Github Client Id'), ['class' => 'form-label']) }}
                                                                        {!! Form::text('github_client_id', Utility::getsettings('github_client_id'), [
                                                                            'placeholder' => __('Enter github client id'),
                                                                            'class' => 'form-control',
                                                                        ]) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        {{ Form::label('github_client_secret', __('Github Client Secret'), ['class' => 'form-label']) }}
                                                                        {!! Form::text('github_client_secret', Utility::getsettings('github_client_secret'), [
                                                                            'placeholder' => __('Enter github client secret'),
                                                                            'class' => 'form-control',
                                                                        ]) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        {{ Form::label('github_redirect', __('Github Redirect Url'), ['class' => 'form-label']) }}
                                                                        {!! Form::text('github_redirect', Utility::getsettings('github_redirect'), [
                                                                            'placeholder' => __('https://demo.test.com/callback/github'),
                                                                            'class' => 'form-control',
                                                                        ]) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-end">
                                    {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    @if (\Auth::user()->type == 'Super Admin' || \Auth::user()->type == 'Admin')
                        <div id="payment_setting">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Payment Settings') }}</h5>
                                </div>
                                {!! Form::open([
                                    'route' => ['settings.payment.setting.update'],
                                    'method' => 'POST',
                                    'data-validate',
                                ]) !!}
                                <div class="card-body">
                                    <div class="faq justify-content-center">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    {{ Form::label('currency', __('Currency'), ['class' => 'form-label']) }}
                                                    {!! Form::text('currency', Utility::getsettings('currency'), [
                                                        'class' => 'form-control',
                                                        'placeholder' => __('Enter currency'),
                                                        'required',
                                                    ]) !!}
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    {{ Form::label('currency_symbol', __('Currency Symbol'), ['class' => 'form-label']) }}
                                                    {!! Form::text('currency_symbol', Utility::getsettings('currency_symbol'), [
                                                        'class' => 'form-control',
                                                        'placeholder' => __('Enter currency symbol'),
                                                        'required',
                                                    ]) !!}
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-xxl-12">
                                                <div class="accordion accordion-flush" id="accordionExample">


                                                    <!-- FLUTTERWAVE -->
                                                    <div class="accordion-item card">
                                                        <h2 class="accordion-header" id="heading4">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapse4"
                                                                aria-expanded="true" aria-controls="collapse4">
                                                                <span class="flex-1 d-flex align-items-center">
                                                                    <i class="ti ti-credit-card text-primary"></i>
                                                                    {{ __('Flutterwave') }}
                                                                </span>
                                                                @if (Utility::getsettings('flutterwavesetting') == 'on')
                                                                    <a
                                                                        class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                                @endif
                                                            </button>
                                                        </h2>
                                                        <div id="collapse4" class="accordion-collapse collapse"
                                                            aria-labelledby="heading4" data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <div class="py-2 col-12 text-end">
                                                                        <div class="form-check form-switch d-inline-block">
                                                                            {!! Form::checkbox(
                                                                                'paymentsetting[]',
                                                                                'flutterwave',
                                                                                Utility::getsettings('flutterwavesetting') == 'on' ? true : false,
                                                                                [
                                                                                    'class' => 'form-check-input mx-2',
                                                                                    'id' => 'is_flutterwave_enabled',
                                                                                ],
                                                                            ) !!}
                                                                            {{ Form::label('is_flutterwave_enabled', __('Enable'), ['class' => 'form-check-label']) }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('flutterwave_key', __('Flutterwave Key'), ['class' => 'col-form-label']) }}
                                                                            {!! Form::text('flutterwave_key', Utility::getsettings('flutterwave_key'), [
                                                                                'class' => 'form-control',
                                                                                'placeholder' => __('Enter flutterwave key'),
                                                                            ]) !!}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('flutterwave_secret', __('Flutterwave Secret'), ['class' => 'col-form-label']) }}
                                                                            {!! Form::text('flutterwave_secret', Utility::getsettings('flutterwave_secret'), [
                                                                                'class' => 'form-control',
                                                                                'placeholder' => __('Enter flutterwave secret'),
                                                                            ]) !!}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('flutterwave_description', __('Flutterwave Description'), ['class' => 'col-form-label']) }}
                                                                            {{ Form::text('flutterwave_description', Utility::getsettings('flutterwave_description'), ['class' => 'form-control', 'id' => 'flutterwave_description', 'placeholder' => __('Enter description')]) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <!-- Mollie -->
                                                    <div class="accordion-item card">
                                                        <h2 class="accordion-header" id="heading18">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapse18"
                                                                aria-expanded="true" aria-controls="collapse18">
                                                                <span class="flex-1 d-flex align-items-center">
                                                                    <i class="ti ti-credit-card text-primary"></i>
                                                                    {{ __('Mollie') }}
                                                                </span>
                                                                @if (Utility::getsettings('molliesetting') == 'on')
                                                                    <a
                                                                        class="text-white btn btn-sm btn-primary float-end me-3">
                                                                        {{ __('Active') }}
                                                                    </a>
                                                                @endif
                                                            </button>
                                                        </h2>
                                                        <div id="collapse18" class="accordion-collapse collapse"
                                                            aria-labelledby="heading18"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <div class="py-2 col-12 text-end">
                                                                        <div
                                                                            class="form-check form-switch d-inline-block">
                                                                            {!! Form::checkbox('paymentsetting[]', 'mollie', Utility::getsettings('molliesetting') == 'on' ? true : false, [
                                                                                'class' => 'form-check-input mx-2',
                                                                                'id' => 'payment_mollie',
                                                                            ]) !!}
                                                                            {{ Form::label('payment_mollie', __('Enable'), ['class' => 'form-check-label']) }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('mollie_api_key', __('Mollie Api Key'), ['class' => 'form-label']) }}
                                                                            {!! Form::text('mollie_api_key', Utility::getsettings('mollie_api_key'), [
                                                                                'class' => 'form-control',
                                                                                'placeholder' => __('Enter mollie api key'),
                                                                                'id' => 'mollie_api_key',
                                                                            ]) !!}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('mollie_profile_id', __('Mollie Profile Id'), ['class' => 'form-label']) }}
                                                                            {!! Form::text('mollie_profile_id', Utility::getsettings('mollie_profile_id'), [
                                                                                'class' => 'form-control',
                                                                                'placeholder' => __('Enter mollie profile id'),
                                                                                'id' => 'mollie_profile_id',
                                                                            ]) !!}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('mollie_partner_id', __('Mollie Partner Id'), ['class' => 'form-label']) }}
                                                                            {!! Form::text('mollie_partner_id', Utility::getsettings('mollie_partner_id'), [
                                                                                'class' => 'form-control',
                                                                                'placeholder' => __('Enter mollie partner id'),
                                                                                'id' => 'mollie_partner_id',
                                                                            ]) !!}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            {{ Form::label('mollie_description', __('Mollie Description'), ['class' => 'form-label']) }}
                                                                            {{ Form::text('mollie_description', Utility::getsettings('mollie_description'), ['class' => 'form-control ', 'placeholder' => __('Enter mollie description')]) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <!-- OFFLINE -->
                                                    <div class="accordion-item card">
                                                        <h2 class="accordion-header" id="heading100">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapse100"
                                                                aria-expanded="true" aria-controls="collapse100">
                                                                <span class="flex-1 d-flex align-items-center">
                                                                    <i class="ti ti-credit-card text-primary"></i>
                                                                    {{ __('Offline') }}
                                                                </span>
                                                                @if (Utility::getsettings('offlinesetting') == 'on')
                                                                    <a
                                                                        class="text-white btn btn-sm btn-primary float-end me-3">{{ __('Active') }}</a>
                                                                @endif
                                                            </button>
                                                        </h2>
                                                        <div id="collapse100" class="accordion-collapse collapse"
                                                            aria-labelledby="heading100"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <div class="py-2 col-12 text-end">
                                                                        <div
                                                                            class="form-check form-switch d-inline-block">
                                                                            {!! Form::checkbox(
                                                                                'paymentsetting[]',
                                                                                'offline',
                                                                                Utility::getsettings('offlinesetting') == 'on' ? true : false,
                                                                                [
                                                                                    'class' => 'form-check-input mx-2',
                                                                                    'id' => 'is_offline_enabled',
                                                                                ],
                                                                            ) !!}
                                                                            {{ Form::label('is_offline_enabled', __('Enable'), ['class' => 'form-check-label']) }}
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="form-group">
                                                                            {{ Form::label('payment_details', __('Payment Details'), ['class' => 'col-form-label']) }}
                                                                            {!! Form::textarea('payment_details', Utility::getsettings('payment_details'), [
                                                                                'class' => 'form-control',
                                                                                'rows' => '3',
                                                                                'id' => 'payment_details',
                                                                                'placeholder' => __('Enter payment details'),
                                                                            ]) !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-end">
                                        {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    @endif

                    <div id="qbo_settings" class="my-2">
                        {{ Form::open([
                                   'route' => 'settings.quickbooks.update',
                               ]) }}

                            <div class="card" id="qbo_settings">
                                <div class="card-header">
                                    <h5> {{ __('QuickBooks Setting') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                {{ Form::label('client_secrete', __('Client Secrete Key'), ['class' => 'form-label']) }}
                                                {!! Form::text('client_secrete', Utility::getsettings('client_secrete'), [
                                                    'placeholder' => __('Enter Client secrete api key'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">

                                                {{ Form::label('client_id', __('Client ID'), ['class' => 'form-label']) }}
                                                {!! Form::text('client_id', Utility::getsettings('client_id'), [
                                                    'placeholder' => __('Enter Client ID'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                {{ Form::label('qborealm_id', __('QBO REALM ID'), ['class' => 'form-label']) }}
                                                {!! Form::text('qborealm_id', Utility::getsettings('qborealm_id'), [
                                                    'placeholder' => __('Enter QBOREALM ID'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                {{ Form::label('company_id', __('INUIT COMPANY ID'), ['class' => 'form-label']) }}
                                                {!! Form::text('company_id', Utility::getsettings('company_id'), [
                                                    'placeholder' => __('Enter INUIT COMPANY ID'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                {{ Form::label('efris_middleware_api_url', __('EFRIS MIDDLEWARE API URL'), ['class' => 'form-label']) }}
                                                {!! Form::text('efris_middleware_api_url', Utility::getsettings('efris_middleware_api_url'), [
                                                    'placeholder' => __('EFRIS MIDDLEWARE API URL'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                {{ Form::label('qbo_base_url', __('QBO BASE API URL'), ['class' => 'form-label']) }}
                                                {!! Form::text('qbo_base_url', Utility::getsettings('qbo_base_url'), [
                                                    'placeholder' => __('Quickbooks api url'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="name"
                                                       class="form-label">{{ __('Environment') }}</label>
                                                <select name="environment" id="environment" class="form-select">
                                                    <option value="development">Development</option>
                                                    <option value="Production">Production</option>
                                                </select>

                                            </div>
                                        </div>

                                        <p>VAT Settings</p>
                                        <div class="col-lg-12 d-inline-flex gap-2">

                                            {{--                                set standard--}}
                                            <div class="form-group">

                                                {{ Form::label('STANDARD Tax Rate', __('Standard VAT'), ['class' => 'form-label']) }}
                                                {!! Form::text('standard_tax_rate', Utility::getsettings('standard_tax_rate'), [
                                                    'placeholder' => __('Standard Tax Rate'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>

                                            {{--                                set deemed --}}
                                            <div class="form-group">
                                                {{ Form::label('DEEMED Tax Rate', __('DEEMED VAT'), ['class' => 'form-label']) }}
                                                {!! Form::text('deemed_tax_rate', Utility::getsettings('deemed_tax_rate'), [
                                                    'placeholder' => __('DEEMED Tax Rate'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>

                                            {{--                                set exempted--}}
                                            <div class="form-group">
                                                {{ Form::label('EXEMPT Tax Rate', __('EXEMPT VAT'), ['class' => 'form-label']) }}
                                                {!! Form::text('exempt_tax_rate', Utility::getsettings('exempt_tax_rate'), [
                                                    'placeholder' => __('EXEMPT Tax Rate'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>

                                            {{--                                set zero rated--}}
                                            <div class="form-group">
                                                {{ Form::label('Zero Tax Rate', __('Zero Rated VAT'), ['class' => 'form-label']) }}
                                                {!! Form::text('zero_tax_rate', Utility::getsettings('zero_tax_rate'), [
                                                    'placeholder' => __('ZERO Tax Rate'),
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>

                                            <div class="form-group">

                                                <label for="name"
                                                       class="form-label">{{ __('Is Deemed flag registered') }}</label>
                                                <select name="is_deemedflag_registered" class="form-select">
                                                    <option value="101"
                                                        {{ Utility::getsettings('is_deemedflag_registered') == '101' ? 'selected' : '' }}>
                                                        {{ __('Yes') }}</option>
                                                    <option value="102"
                                                        {{ Utility::getsettings('is_deemedflag_registered') == '102' ? 'selected' : '' }}>
                                                        {{ __('No') }}</option>
                                                </select>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-12 d-inline-flex gap-2">

                                        {{--                                is deemed registered--}}


                                        <div class="form-group">

                                            {{ Form::label('Quickbooks deemed tax code ref', __('Quickbooks deemed tax code ref'), ['class' => 'form-label']) }}
                                            {!! Form::text('quickbooks_deemed_taxcoderef', Utility::getsettings('quickbooks_deemed_taxcoderef'), [
                                                'placeholder' => __('Enter quickbooks deemed tax code reference value'),
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="float-end">
                                        <input type="hidden" name="tenant_id" value="{{ tenant('id') }}">
                                        <button class="btn btn-primary " type="submit"
                                                id="save-btn">{{ __('Save Changes') }}</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
@endpush
@push('javascript')
    <script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,
        });

        document.addEventListener('DOMContentLoaded', function() {
            var genericExamples = document.querySelectorAll('[data-trigger]');
            for (i = 0; i < genericExamples.length; ++i) {
                var element = genericExamples[i];
                new Choices(element, {
                    placeholderValue: 'This is a placeholder set in the config',
                    searchPlaceholderValue: 'Select Option',
                });
            }
        });

        function check_theme(color_val) {
            $('.theme-color').prop('checked', false);
            $('input[value="' + color_val + '"]').prop('checked', true);
        }

        // theme color
        var themescolors = document.querySelectorAll(".themes-color > a");
        for (var h = 0; h < themescolors.length; h++) {
            var c = themescolors[h];

            c.addEventListener("click", function(event) {
                var targetElement = event.target;
                if (targetElement.tagName == "SPAN") {
                    targetElement = targetElement.parentNode;
                }
                var temp = targetElement.getAttribute("data-value");
                removeClassByPrefix(document.querySelector("body"), "theme-");
                document.querySelector("body").classList.add(temp);
            });
        }

        // transprent background
        var custthemebg = document.querySelector("#cust-theme-bg");
        custthemebg.addEventListener("click", function() {
            if (custthemebg.checked) {
                document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.add("transprent-bg");
            } else {
                document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.remove("transprent-bg");
            }
        });

        // dark layout
        var custdarklayout = document.querySelector("#cust-darklayout");
        custdarklayout.addEventListener("click", function() {
            if (custdarklayout.checked) {
                document.querySelector(".m-headers > .b-brand > img").setAttribute("src",
                    "{{ Utility::getpath('logo/app-logo.png') }}");
                document.querySelector("#main-style-link").setAttribute("href",
                    "{{ asset('assets/css/style-dark.css') }}");
            } else {
                document.querySelector(".m-headers > .b-brand > img").setAttribute("src",
                    "{{ Utility::getpath('logo/app-dark-logo.png') }}");
                document.querySelector("#main-style-link").setAttribute("href",
                    "{{ asset('assets/css/style.css') }}");
            }
        });

        $(document).on('click', "input[name$='smssetting']", function() {
            var test = $(this).val();
            $("#twilio").fadeOut(500);
            if (test == 'twilio') {
                $("#twilio").fadeIn(500);
                $("#twilio").removeClass('d-none');
                $("#nexmo").addClass('d-none');
                $("#fast2sms").addClass('d-none');
                $("#nexmo").fadeOut(500);
                $("#fast2sms").fadeOut(500);
            } else if (test == 'nexmo') {
                $("#nexmo").fadeIn(500);
                $("#twilio").addClass('d-none');
                $("#nexmo").removeClass('d-none');
                $("#fast2sms").addClass('d-none');
                $("#twilio").fadeOut(500);
                $("#fast2sms").fadeOut(500);
            } else if (test == 'fast2sms') {
                $("#fast2sms").fadeIn(500);
                $("#twilio").addClass('d-none');
                $("#nexmo").addClass('d-none');
                $("#fast2sms").removeClass('d-none');
                $("#nexmo").fadeOut(500);
                $("#twilio").fadeOut(500);
            }
        });

        $(document).on('change', ".socialsetting", function() {
            var test = $(this).val();
            if ($(this).is(':checked')) {
                if (test == 'google') {
                    $("#google").fadeIn(500);
                    $("#google").removeClass('d-none');
                } else if (test == 'facebook') {
                    $("#facebook").fadeIn(500);
                    $("#facebook").removeClass('d-none');
                } else if (test == 'github') {
                    $("#github").fadeIn(500);
                    $("#github").removeClass('d-none');
                } else if (test == 'linkedin') {
                    $("#linkedin").fadeIn(500);
                    $("#linkedin").removeClass('d-none');
                }
            } else {
                if (test == 'google') {
                    $("#google").fadeOut(500);
                    $("#google").addClass('d-none');
                } else if (test == 'facebook') {
                    $("#facebook").fadeOut(500);
                    $("#facebook").addClass('d-none');
                } else if (test == 'github') {
                    $("#github").fadeOut(500);
                    $("#github").addClass('d-none');
                } else if (test == 'linkedin') {
                    $("#linkedin").fadeOut(500);
                    $("#linkedin").addClass('d-none');
                }
            }
        });

        $('body').on('click', '.send_mail', function() {
            var action = $(this).data('url');
            var modal = $('#common_modal');
            $.get(action, function(response) {
                modal.find('.modal-title').html('{{ __('Test Mail') }}');
                modal.find('.body').html(response);
                modal.modal('show');
            })
        });

        $(document).on('click', "input[name='storage_type']", function() {
            if ($(this).val() == 's3') {
                $('.s3-setting').removeClass('d-none');
                $('.wasabi-setting').addClass('d-none');
                $('.local-setting').addClass('d-none');
            } else if ($(this).val() == 'wasabi') {
                $('.s3-setting').addClass('d-none');
                $('.wasabi-setting').removeClass('d-none');
                $('.local-setting').addClass('d-none');
            } else {
                $('.s3-setting').addClass('d-none');
                $('.wasabi-setting').addClass('d-none');
                $('.local-setting').removeClass('d-none');
            }
        });

        // notification status
        $(document).on("change", ".chnageEmailNotifyStatus", function(e) {
            var csrf = $("meta[name=csrf-token]").attr("content");
            var email = $(this).parent().find("input[name=email_notification]").is(":checked");
            var action = $(this).attr("data-url");
            $.ajax({
                type: "POST",
                url: action,
                data: {
                    _token: csrf,
                    type: 'email',
                    email_notification: email,
                },
                success: function(response) {
                    if (response.warning) {
                        show_toastr("Warning!", response.warning, "warning");
                    }
                    if (response.is_success) {
                        show_toastr("Success!", response.message, "success");
                    }
                },
            });
        });
        $(document).on("change", ".chnagesmsNotifyStatus", function(e) {
            var csrf = $("meta[name=csrf-token]").attr("content");
            var sms = $(this).parent().find("input[name=sms_notification]").is(":checked");
            var action = $(this).attr("data-url");
            $.ajax({
                type: "POST",
                url: action,
                data: {
                    _token: csrf,
                    type: 'sms',
                    sms_notification: sms,
                },
                success: function(response) {
                    if (response.warning) {
                        show_toastr("Warning!", response.warning, "warning");
                    }
                    if (response.is_success) {
                        show_toastr("Success!", response.message, "success");
                    }
                },
            });
        });
        $(document).on("change", ".chnageNotifyStatus", function(e) {
            var csrf = $("meta[name=csrf-token]").attr("content");
            var notify = $(this).parent().find("input[name=notify]").is(":checked");
            var action = $(this).attr("data-url");
            $.ajax({
                type: "POST",
                url: action,
                data: {
                    _token: csrf,
                    type: 'notify',
                    notify: notify,
                },
                success: function(response) {
                    if (response.warning) {
                        show_toastr("Warning!", response.warning, "warning");
                    }
                    if (response.is_success) {
                        show_toastr("Success!", response.message, "success");
                    }
                },
            });
        });
    </script>
@endpush
