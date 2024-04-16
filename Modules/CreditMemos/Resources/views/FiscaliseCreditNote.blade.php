
@extends('layouts.main')

@section('styles')
    <style>
        .hidden {
            display: none;
        }
    </style>
@endsection

@section('title', 'Fiscalise Credit Note')

@section('content')
  <div class="page-header">
    <div class="page-block">
      <div class="row align-items-center">
        <div class="col-md-12">
          <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Fiscalise Credit Memo') }}</h4>
          </div>
          <ul class="breadcrumb">
            <li class="breadcrumb-item "><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Quickbooks') }}</li>
            <li class="breadcrumb-item active">{{ __('fiscalise Credit Memos') }}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div id="app">
    <fiscal-note-frame :creditmemo="{{json_encode($creditmemo)}}" :reasons="{{json_encode($reasons)}}"></fiscal-note-frame>
  </div>
@endsection

