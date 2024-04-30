{{-- @extends('invoices::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('invoices.name') !!}
    </p>
@endsection --}}
@extends('layouts.main')

@section('styles')
@endsection

@section('content')
    <!-- PAGE HEADER -->
    <div class="page-header d-sm-flex d-block">
        <ol class="breadcrumb mb-sm-0 mb-3">
            <!-- breadcrumb -->
            <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
            <li class="breadcrumb-item" aria-current="page">Quickbooks</li>
            <li class="breadcrumb-item" aria-current="page">Invoices</li>
            <li class="breadcrumb-item active" aria-current="page">Faile validations</li>
        </ol><!-- End breadcrumb -->

    </div>
    <!-- END PAGE HEADER -->

    <!-- ROW -->
    <div class="row">

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Failded Validations</h3>
                </div>
                <div class="card-body">

                    <p>Found <code class="highlighter-rouge">{{ $data['total'] }}</code>tbetween <code
                            class="highlighter-rouge">{{ $data['startdate'] }} and {{ $data['enddate'] }}</code>.</p>
                    <div class="table-responsive">
                        <table class="table border table-primary text-nowrap text-md-nowrap table-striped mb-0">
                            <thead>
                                <tr class="bg-secondary">
                                    <th scope="col" class="p-4">
                                        <div class="flex items-center">
                                            #

                                            {{-- <check-box @click="selectAllViaCheckBox
                                                    " v-model="allSelect"
                                                    class="w-4 h-4 text-black bg-gray-100 rounded  focus:ring-gray-500 focus:ring-1" /> --}}
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap">
                                        Industry Code
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap">
                                        Ref. Number
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap">
                                        Transaction Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap">
                                        Customer Details
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap">
                                        Buyer Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap">
                                        Total Amount
                                    </th>

                                    <th scope="col" class="px-6 py-3 whitespace-nowrap">
                                        Fiscal Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['filteredList'] as $invoice)
                                    <tr>
                                        {{-- <td> --}}
                                        <td>
                                            <input type="checkbox" name="invoice_checkbox"
                                                value="{{ $invoice->checkboxField }}" id="row{{ $invoice->checkboxField }}">
                                        {{-- </td> --}}
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
                                            @if ($invoice->fisStatus === 'fiscalise')
                                                <span>
                                                    <span
                                                        class="bg-gray-600 text-white rounded-md py-1 px-1.5 cursor-pointer"
                                                        onclick="fiscaliseInvoice({{ $invoice['Id'] }})"
                                                        id="invoice-{{ $invoice['Id'] }}">
                                                        Fiscalise
                                                    </span>
                                                </span>
                                            @elseif(isset($invoice['invoiceOptions']['preview']))
                                                <span class="ml-1">
                                                    {!! $invoice['invoiceOptions']['preview'] !!}
                                                </span>
                                            @else
                                                <span>{!! $invoice['invoiceOptions'] !!}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <div class="justify-content-center align-items-center flex-column mb-3">
                                        {{-- <img src="../../../../assets/folder.png" width="100"> --}}
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
    </div>
    <!-- END ROW -->
@endsection

@section('scripts')
    <!-- APEXCHART JS -->
    <script src="{{ asset('build/assets/plugins/apexcharts/apexcharts.min.js') }}"></script>

    <!-- ECHARTS JS -->
    <script src="{{ asset('build/assets/plugins/echarts/echarts.js') }}"></script>

    <!-- INDEX JS -->
    <script>
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
            const tin = <?= json_encode($tin) ?>;
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
                            'tin': tin,
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
            const tin = <?= json_encode($tin) ?>;
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
                            'tin': tin,
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

        $('#demo').daterangepicker({
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
            "startDate": "07/22/2023",
            "endDate": "07/28/2023"
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format(
                'YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
    </script>
@endsection
