<?php
function returnApprovalStatus($code) {
  switch ($code) {
    case "101":
      return "Approved";
    case "102":
      return "Submitted";
    case "103":
      return "Rejected";
    case "104":
      return "Voided";
    default:
      return "Other";
  }
}


?>
@extends('layouts.main')

@section('title','Issued Credit Notes')

@section('content')
  <!-- PAGE HEADER -->
  <div class="page-header">
    <div class="page-block">
      <div class="row align-items-center">
        <div class="col-md-12">
          <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Issued Credit Notes') }}</h4>
          </div>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
            <li class="breadcrumb-item" aria-current="page">Quickbooks</li>
            <li class="breadcrumb-item active" aria-current="page">
              All Issued Credit Notes</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- END PAGE HEADER -->

  <!-- ROW -->
  <div class="row" id="app">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"> Issued credit notes via the EFRIS platform</h3>
      </div>
      <div class="card-body">
{{--        <div class="d-flex row form-group py-2">--}}
{{--          {!! Form::open([--}}
{{--              'route' => 'efris.goods.get',--}}
{{--              'method' => 'Get',--}}
{{--              'class' => 'd-flex row',--}}
{{--              'enctype' => 'multipart/form-data',--}}
{{--          ]) !!}--}}
{{--          <input name="pageSize" value="99" type="hidden" />--}}
{{--          <div class="col-lg-3">--}}
{{--            {{ Form::text('goodsCode', null, ['class' => 'form-control', 'id' => 'buyer_type', 'style' => 'width:250px;', 'placeholder' => 'Search by invoice number']) }}--}}
{{--          </div>--}}
{{--          <div class="col-lg-3">--}}
{{--            {{ Form::text('goodsName', null, ['class' => 'form-control', 'id' => 'industry_code', 'style' => 'width:250px;', 'placeholder' => 'Search by invoice no.']) }}--}}
{{--          </div>--}}
{{--          <div class="col-lg-2">--}}
{{--            <button type="submit" class="btn btn-primary">Fetch Invoices</button>--}}
{{--            {!! Form::close() !!}--}}
{{--          </div>--}}

{{--        </div>--}}



      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-striped-columns text-nowrap text-md-nowrap mb-0">
          <thead class="bg-secondary">
          <tr>
            <th scope="col" class="">Reference Number</th>
            <th scope="col" class="">
              Original Invoice Number
            </th>
            <th scope="col" class="">Approval Status</th>

            <th scope="col" class="">Gross Amount</th>

            <th scope="col" class="">
              Application Date
            </th>

            <th scope="col" class="px-6 py-3">Options</th>
          </tr>
          </thead>
          <tbody>
          @foreach($records as $credit)
            <tr class="hover:bg-gray-50" >
              <th scope="row" class="font-medium text-gray-900">{{ $credit['referenceNo'] }}</th>
              <td class="">{{ $credit['oriInvoiceNo'] }}</td>
              <td class="">{{ returnApprovalStatus($credit['approveStatus']) }}</td>
              <td class="">{{ $credit['grossAmount'] }}</td>
              <td class="">{{ $credit['applicationTime'] }}</td>
              <td>
                <div class="d-flex flex-column justify-content-around">
                  @if ($credit['approveStatus'] === '101')
                                    <a href="{{ 'fiscal-creditnote-download/' . $credit['id'] }}"
                                       class="btn-sm btn btn-primary"
                                       target="_blank">
{{--                                      <x-heroicon-o-arrow-left class="w-2 h-2"/> --}}
                                      Download
                                    </a>

                    <a  href="{{route('creditnote.cancel.view', $credit['id']) }}"
                          class="btn btn-sm btn-secondary  cursor-pointer"
                          >
                      Cancel
{{--                      <x-heroicon-o-arrow-left class="w-4 h-4 text-blue"/>--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"--}}
{{--                                         stroke="currentColor" class="w-6 h-6 self-center hover:bg-red-300">--}}
{{--                                        <path stroke-linecap="round" stroke-linejoin="round"--}}
{{--                                              d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />--}}
{{--                                    </svg>--}}
                                </a>

{{--                    <span class="text-blue-800 font-extrabold py-1 cursor-pointer"--}}
{{--                          @click="showCreditNote(true, {{ $credit['id'] }})">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"--}}
{{--                                         stroke="currentColor" class="text-blue-500 self-center w-6 h-6">--}}
{{--                                        <path stroke-linecap="round" stroke-linejoin="round"--}}
{{--                                              d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />--}}
{{--                                        <path stroke-linecap="round" stroke-linejoin="round"--}}
{{--                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />--}}
{{--                                    </svg>--}}
{{--                                </span>--}}
                  @endif

{{--                  @if ($credit['approveStatus'] === '102')--}}
{{--                    <span class="text-blue-800 font-extrabold py-1 cursor-pointer"--}}
{{--                          @click="showCreditNote(true, {{ $credit['id'] }})">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"--}}
{{--                                         stroke="currentColor" class="text-blue-500 self-center w-6 h-6">--}}
{{--                                        <path stroke-linecap="round" stroke-linejoin="round"--}}
{{--                                              d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />--}}
{{--                                        <path stroke-linecap="round" stroke-linejoin="round"--}}
{{--                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />--}}
{{--                                    </svg>--}}
{{--                                </span>--}}
{{--                  @endif--}}
                </div>
              </td>
            </tr>
          @endforeach

          </tbody>
        </table>
      </div>

{{--      @if ($data->hasPages())--}}
        <div class="pagination-wrapper my-1">
          <nav aria-label="Page navigation">
            {!! $links !!}
          </nav>
        </div>
{{--      @endif--}}

      {{-- <example-component></example-component> --}}

    </div>
  </div>
  <!-- END ROW -->
@endsection

@section('scripts')

@endsection
