@extends('layouts.main')

@section('styles')
  <link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker.css') }}" />
@endsection

@push('javascript')
  <script src="{{ asset('assets/js/daterangepicker/daterangepicker.js') }}"></script>
@endpush


@section('title', 'All Receipts Date ranges')

@section('content')
    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h4 class="m-b-10">{{ __('All Quickbooks Receipts By Date Range') }}</h4>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item "><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item">{{ __('Quickbooks') }}</li>
                        <li class="breadcrumb-item active">{{ __('Receipts') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE HEADER -->

    <!-- ROW -->
    <div class="row">

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Quickbooks Receipts</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between row-sm my-3">
                    <div class="d-flex justify-content-between">
                        <div class="col-sm-3 mr-1">
                            {{ Form::select('buyer_type', $buyerType, null, ['class' => 'form-control form-control-sm', 'id' => 'buyer_type', 'style' => 'width:200px;', 'onchange' => 'InvoiceBuyerType()', 'prompt' => 'Update Buyer Type...']) }}
                        </div>
                        <div class="col-sm-3">
                            {{ Form::select('industry_code', $industryCode, null, ['class' => 'form-control form-control-sm ', 'id' => 'industry_code', 'style' => 'width:200px;', 'onchange' => 'IndustryCode()', 'prompt' => 'Update Industry Code...']) }}
                        </div>
                      <div class="d-inline-flex gap-1 col-auto">
                        {!! Form::open([
                            'route' => ['qbo.receipts.range', 'validate' => 'no'],
                            'method' => 'get',
                            'class' => 'form-horizontal  row g-3'
                        ]) !!}

                        <div class="input-group ">
                          {{ Form::text('invoice_period', null, ['class' => 'form-control form-control-sm col-4', 'id' => 'date', 'style' => '']) }}
                          <button type="submit" class="btn btn-sm btn-primary">Choose From
                            Date Range</button>

                        </div>

                        {!! Form::close() !!}
                      </div>

                    </div>

                      <div class="d-flex justify-content-between">
                        {{-- <div class="d-flex justify-content-between"> --}}
                        <div class="">

                          <a type="button" class="btn btn-sm btn-primary"
                             href="{{ route('qbo.receipts.range', ['receipts_period' => $invoicePeriod, 'yes']) }}">Sync
                            selected Receipts</a>

                        </div>

                      </div>
                </div>


                   <p class="font-16">Found <code class="highlighter-rouge">{{ $data['total'] }} Receipts </code>between <code
                            class="highlighter-rouge">{{ $data['startdate'] }} and {{ $data['enddate'] }}</code>.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped  mb-0">
                            <thead>
                                <tr class="bg-secondary">
                                    <th scope="col" class="">
                                        <div class="flex items-center">
                                            #

                                            {{-- <check-box @click="selectAllViaCheckBox
                                                    " v-model="allSelect"
                                                    class="w-4 h-4 text-black bg-gray-100 rounded  focus:ring-gray-500 focus:ring-1" /> --}}
                                        </div>
                                    </th>
                                    <th scope="col" class="whitespace-nowrap">
                                        Industry Code
                                    </th>
                                    <th scope="col" class="whitespace-nowrap">
                                        Ref. Number
                                    </th>
                                    <th scope="col" class="whitespace-nowrap">
                                        Transaction Date
                                    </th>
                                    <th scope="col" class="whitespace-nowrap">
                                        Customer Details
                                    </th>
                                    <th scope="col" class="whitespace-nowrap">
                                        Buyer Type
                                    </th>
                                    <th scope="col" class="whitespace-nowrap">
                                        Total Amount
                                    </th>

                                    <th scope="col" class="whitespace-nowrap">
                                        Fiscal Status
                                    </th>
                                    <th scope="col" class="whitespace-nowrap">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['filteredList'] as $receipt)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="invoice_checkbox" value="{{ $receipt['Id'] }}"
                                                id="row{{ $receipt['Id'] }}">
                                        </td>
                                        <td>{{ $receipt->industryCode }}</td>
                                        <td>{{ $receipt->refNumber }}</td>
                                        <td>{{ $receipt->transactionDate }}</td>
                                        <td>
                                            {!! $receipt->customerDetails !!}
                                        </td>
                                        <td>{{ $receipt->buyerType }}</td>
                                        <td>{{ $receipt->totalAmount }}</td>
                                        <td>{!! $receipt->fiscalStatus !!}</td>
                                        <td>
                                          {!! $receipt->invoiceOptions !!}
                                        </td>
                                    </tr>
                                @empty
                                    <div class="justify-content-center align-items-center flex-column mb-3">
                                        {{-- <img src="../../../../assets/folder.png" width="100"> --}}
                                        <p class="mt-3 text-black font-bold text-lg my-10">
                                            No Receipts Data Available
                                        </p>
                                    </div>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
{{--                  @if ($data['filteredList']->hasPages())--}}
{{--                    <div class="pagination-wrapper my-1">--}}
{{--                      <nav aria-label="Page navigation">--}}
{{--                        {{ $data['filteredList']->links() }}--}}
{{--                      </nav>--}}
{{--                    </div>--}}
{{--                  @endif--}}
                  <div class="pagination-wrapper my-1">
                    <nav aria-label="Page navigation">
                        {{ $data['filteredList']->links() }}
                    </nav>
                  </div>
                </div>
            </div>
        </div>
    </div>

    <!-- END ROW -->
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
@endpush
@push('javascript')
    <script src="{{ asset('vendor/modules/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/daterangepicker/daterangepicker.min.js') }}"></script>
    <!-- INDEX JS -->
    <script>
        // import moment from "moment";

        function getCsrfToken() {
            return document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        function getSelectedRows() {
            return $('input:checked').map(function() {
                return this.value;
            }).get();
        }

        const csrfToken = getCsrfToken();

        function getSelectedInvoices(_keys) {
            return _keys.map(function(_key) {
                return $('#row' + _key).val();
            });
        }

        function updateInvoiceBuyerType() {
            const keys = getSelectedRows();

            if (keys.length > 0) {
                const invoice_mod = {
                    invoices: {
                        buyerType: $('#buyer_type').val(),
                        id: getSelectedInvoices(keys)
                    }
                };
                // Get the CSRF token

                fetch(route('update.buyerType'), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Connection': 'keep-alive'
                        },
                        body: JSON.stringify(invoice_mod)
                    }).then(response => response.json())
                    .then(response => {
                        if (response.Message >= 1) {
                            location.reload();
                        } else {
                            InvoiceBuyerType();
                        }
                    });
            }
        }

        // for updating industry codes
        function updateIndustryCode() {
            const keys = getSelectedRows();

            if (keys.length > 0) {
                const invoice_mod = {
                    invoices: {
                        industryCode: $('#industry_code').val(),
                        id: getSelectedInvoices(keys)
                    }
                };

                fetch(route('update.industrycode'), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Connection': 'keep-alive'
                        },
                        body: JSON.stringify(invoice_mod)
                    }).then(response => response.json())
                    .then(response => {
                        if (response.Message >= 1) {
                            location.reload();
                        } else {
                            IndustryCode();
                        }
                    });
            }
        }

        function IndustryCode() {
            updateIndustryCode();
        }

        function InvoiceBuyerType() {
            updateInvoiceBuyerType();
        }

        $('#date').daterangepicker({
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
              'month')]
          },
          "alwaysShowCalendars": true,
          "startDate": "01/01/2023",
          "endDate": "07/28/2023"
        }, function(start, end, label) {
          console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format(
            'YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
    </script>
@endpush
