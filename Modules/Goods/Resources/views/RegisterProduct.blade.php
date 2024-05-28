@php
use Carbon\Carbon;
@endphp
@extends('layouts.main')

@push('styles')
    <style>
        form div.required label.control-label:after {
            content: " * ";
            color: red;
        }
    </style>
@endpush

@section('title','Register product with URA')
    @php
        $categories = [
            '101' => 'Yes',
            '102' => 'No'
        ];

        $efItem = empty($efrisItem) ? null : $efrisItem[0];
        // Assign default values
        if ($efItem) {
            $efris->unitOfMeasure = $efItem->measureUnit;
            $efris->currency = $efItem->currency;
            $efris->commodityCategoryId = $efItem->commodityCategoryCode;
            $efris->havePieceUnit = 102;
            $efris->haveOtherUnit = 102;
            $efris->pieceMeasureUnit = @$efItem->pieceMeasureUnit;
            $efris->pieceScaledValue = @$efItem->pieceScaledValue;
            $efris->packageScaledValue = @$efItem->packageScaledValue;
            $efris->item_tax_rule = "URA";
            $efris->haveExciseTax = 102;
//            $efris->exciseDutyCode = $efItem->exciseDutyCode;
        }
//
//        if ($redo == "yes") {
//            // Are we re-registering? First remove the record from the DB
//            \DB::table('efris_items')->where('id', $item->Item->Id)->delete();
//        }
    @endphp

@section('content')
    <!-- PAGE HEADER -->
    <div class="page-header d-sm-flex d-block">
        <ol class="breadcrumb mb-sm-0 mb-3">
            <!-- breadcrumb -->
            <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
            <li class="breadcrumb-item" aria-current="page">Quickbooks</li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('goods.all') }}">Goods</a></li>
        </ol><!-- End breadcrumb -->

    </div>
    <!-- END PAGE HEADER -->
    <div id="app">
      @if ($efItem && $redo == "no")
        <div class="alert alert-warning">
          <span class="micon dw dw-info"></span>
          This item is already registered in the URA EFRIS platform. Please click the submit button below to sync the item details
        </div>
      @endif

      @if ($redo == "yes")
        <div class="alert alert-info">
          <span class="micon dw dw-info"></span>
          This item <u>may not have been registered</u> with the URA EFRIS platform the first time. Please submit the form below to register it with the URA platform
        </div>
      @endif
      <register-product
        :item="{{json_encode($item)}}"
        :master-data="{{json_encode($masterdata)}}"
        :redo="{{json_encode($redo)}}"
      />
    </div>
@endsection

