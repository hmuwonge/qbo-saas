@php
use Carbon\Carbon;
$users = \Auth::user();
$currantLang = $users->currentLanguage();
@endphp
@extends('layouts.main')
@section('title', __('Dashboard'))
@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/jqvmap/dist/jqvmap.min.css') }}">
@endpush
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h4 class="m-b-10">{{ __('Quickbooks Dashboard') }}</h4>
                    </div>
                    <ul class="breadcrumb">
                        <li class="section-header-breadcrumb"></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div>
        {{-- <quickbooks-integrator :url="'{{$url}}'"></quickbooks-integrator> --}}
        <div class="py-12 box-border h-full card">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 card-body">
                <div class="overflow-hidden text-center ">
                    <div class="p-6">
                        <h2 class="font-semibold text-2xl opacity-90">Welcome to the EFRIS System to System API?</h2>
                        <span>
                            <h4>When you see page it means that your QuickBooks Authentication has expired</h4>
                        </span>

                        <div
                            class="card border" >
                            <div class="card-body">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10" style="width: 80px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>

                            </div>
                            <p class="font-bold text-lg">We did not find any authentication details for your Quickbooks account. Possible cause could be the
                                authentication between us and your quickbooks account expired.</p>
                        </div>
                        {{-- <span class="text-red-600 font-bold italic">{{ $url }}</span> --}}

                        <div class="flex flex-row justify-between w-96 mx-auto">

                            <span class="box-border items-center">

                                <button type="button"
                                    class="btn btn-primary
                             "
                                    @click="qboInit()">
                                    <a class="button" href="{{$url}}">

                                        <span class="text-white"> Please click here to
                                            re-autheticate. </span>
                                    </a>

                                </button>
                            </span>

                        </div>
                    </div>

            </div>
        </div>
    </div>
    </div>
@endsection
@push('javascript')


@endpush
