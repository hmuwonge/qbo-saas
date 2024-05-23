@extends('layouts.main')


@section('title', 'All Invoices')

@section('content')

    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h4 class="m-b-10">{{ __('All Quickbooks Invoice') }}</h4>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item "><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item">{{ __('Quickbooks') }}</li>
                        <li class="breadcrumb-item active">{{ __('Invoice') }}</li>
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
                    <h3 class="card-title">QuickBooks Invoices</h3>
                </div>

                <div class="d-flex justify-content-between m-2">

                    <div class="d-inline-flex gap-1">
                        <div class="col-auto">
                            {{ Form::select('buyer_type', $buyerType, null, ['class' => 'form-select form-control control-sm ',
'id' => 'buyer_type', 'style' => 'width:250px;', 'onchange' => 'InvoiceBuyerType()', 'prompt' => 'Update Buyer Type...']) }}
                        </div>
                        <div class="col-auto">
                            {{ Form::select('industry_code', $industryCode, null, ['class' => 'form-select control-sm', 'id' => 'industry_code', 'style' => 'width:250px;', 'onchange' => 'IndustryCode()', 'prompt' => 'Update Industry Code...']) }}
                        </div>
                        <div class="d-inline-flex gap-1 col-auto">
                            {!! Form::open([
                                'route' => ['qbo.invoices.range', 'validate' => 'no'],
                                'method' => 'get',
                                'class' => 'form-horizontal  row g-3',
                            ]) !!}

                            <div class="input-group ">
                                {{ Form::text('invoice_period', null, ['class' => 'form-control form-control-sm col-4 date', 'id' => 'date', 'style' => '']) }}
                                <button type="submit" class="btn btn-sm btn-primary">Choose From
                                    Date Range</button>

                            </div>

                            {!! Form::close() !!}
                        </div>
                    </div>

                    <div class="d-inline-flex gap-1">
                        <div class="">
                            <a type="button" class="btn btn-sm btn-primary" href="{{ route('invoices.sync') }}">Sync
                                Invoices From Efris</a>
                        </div>
                        <div class="">
                            <a type="button" class="btn btn-sm btn-primary"
                               href="{{ route('validate.invoices') }}">Validate Invoices</a>
                        </div>

                        <div class=" ml-2">
                            <a type="button" class="btn btn-sm btn-primary"
                               href="{{ route('autosync.invoices.fiscalise') }}">Fiscalise ready</a>
                        </div>

                    </div>

                </div>

                <div class="mx-2 card-body table-border-style">

                    <div class="table-responsive table-border-style">
                        <table class="table table-bordered table-striped  mb-0">
                            <thead>
                                <tr class="bg-secondary fw-bolder">
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
                                @forelse ($data['filteredList'] as $invoice)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="itemCheckbox" id="row{{ $invoice['Id'] }}"
                                                data-id="{{ $invoice['Id'] }}">
                                        </td>
                                        <td>{{ $invoice->industryCode }}</td>
                                        <td>{{ $invoice->refNumber }}</td>
                                        <td>{{ $invoice->transactionDate }}</td>
                                        <td>
                                            {!! $invoice->customerDetails !!}
                                        </td>
                                        <td>{{ $invoice->buyerType }}</td>
                                        <td>{{ $invoice->totalAmount }}</td>
                                        <td>{!! $invoice->fiscalStatus !!}</td>
                                        <td>
                                            {!! $invoice['invoiceOptions'] !!}

                                        </td>
                                    </tr>
                                @empty
                                    <div class="justify-content-center align-items-center flex-column mb-3">
                                        <p class="mt-3 text-black font-bold text-lg my-10">
                                            No Invoices Data Available
                                        </p>
                                    </div>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper my-1">
                        <nav aria-label="Page navigation">
                            {!! $data['links'] !!}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--    </div> --}}
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
        // import Swal from "laravel-mix/src/Dispatcher";

        function getCsrfToken() {
            return document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }
        const csrfToken = getCsrfToken();
        // Initialize an array to hold selected IDs
        let selectedIds = [];

        // Function to add selected ID to the array
        function getSelectedRows() {
            const checkboxes = document.querySelectorAll('input.itemCheckbox:checked');
            return Array.from(checkboxes).map(function(checkbox) {
                // Extract the ID from the checkbox ID
                const checkboxId = checkbox.id;
                const itemId = checkboxId.replace('row', ''); // Remove 'row' prefix
                return itemId;
            });
        }

        function updateInvoiceBuyerType() {
            const keys = getSelectedRows();
            console.log(keys)
            const tin = {{ $tin }};

            if (keys.length > 0) {
                const invoices = {
                    buyerType: $('#buyer_type').val(),
                    invoiceIds: keys
                };
                // Get the CSRF token
                // console.log(invoices)

                fetch("{{route('invoices.update.buyerType')}}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'tin': tin,
                            'Connection': 'keep-alive'
                        },
                        body: JSON.stringify(invoices)
                    }).then(response => response.json())
                    .then(response => {
                        if (response.status === true) {
                            Swal.fire({
                                // title: 'Are you sure?',
                                text: response.payload,
                                icon: 'success',
                            })
                            // You can also choose to hide the message after a few seconds if needed
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        } else {
                            Swal.fire({
                                title: 'Something Wrong occured',
                                text: response.payload,
                                icon: 'warning',
                            })
                        }
                    });
            }
        }

        // for updating industry codes
        function updateIndustryCode() {
            const tin = <?= json_encode($tin) ?>;
            const keys = getSelectedRows();

            if (keys.length > 0) {
                const invoice_mod = {
                    industryCode: $('#industry_code').val(),
                    invoiceIds: keys

                };

                fetch("{{route('update.industrycode')}}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'tin': tin,
                            'Connection': 'keep-alive'
                        },
                        body: JSON.stringify(invoice_mod)
                    }).then(response => response.json())
                    .then(response => {
                        if (response.status === true) {
                            Swal.fire({
                                // title: 'Are you sure?',
                                text: response.payload,
                                icon: 'success',
                            })
                            // You can also choose to hide the message after a few seconds if needed
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        } else {
                            Swal.fire({
                                title: 'Something Wrong occured',
                                text: response.payload,
                                icon: 'warning',
                            })
                        }
                    });
            }
        }

        // $(document).ready(function() {
            function IndustryCode() {
                updateIndustryCode();
            }
        // })

        function InvoiceBuyerType() {
            updateInvoiceBuyerType();
        }

        $('.date').daterangepicker({
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
