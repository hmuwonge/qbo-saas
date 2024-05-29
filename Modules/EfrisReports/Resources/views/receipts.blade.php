@extends('layouts.main')

@section('title', 'Fiscalise Receipts')

@section('content')
    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h4 class="m-b-10">{{ __('Fiscalised Receipts') }}</h4>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Quickbooks</li>
                        <li class="breadcrumb-item active" aria-current="page">
                            All Efris Receipts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- END PAGE HEADER -->

    <!-- ROW -->
    <div class="row">
        <div class="d-flex gap-1 col-auto py-2 mb-2">
            {!! Form::open([
                'route' => 'ura.invoices',
                'method' => 'Get',
                'class' => 'd-inline-flex row',
                'enctype' => 'multipart/form-data',
            ]) !!}
            <div class="d-inline-flex gap-1 col-auto">
                {{ Form::text('customer_name', null, [
                    'class' => 'form-control form-control-sm',
                    'style' => 'width:250px;',
                    'placeholder' => 'Search by customer name',
                ]) }}

                {{ Form::text('invoice_period', null, ['class' => 'form-control form-control-sm', 'id' => 'date', 'style' => 'width:250px']) }}

                <button type="submit" class="btn btn-sm btn-primary col-auto">Choose From
                    Date Range</button>
            </div>
            {!! Form::close() !!}
        </div>


        {{-- <p>Found <code class="highlighter-rouge">{{ $page_data['totalSize'] }}</code>tbetween <code
                class="highlighter-rouge">{{ $data['startdate'] }} and {{ $data['enddate'] }}</code>.</p> --}}
                <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-nowrap text-md-nowrap  table-info mb-0">
                <thead class="text-base text-black uppercase bg-gray-300 ">
                    <tr>
                        {{--              <th scope="col" class="p-4"> --}}
                        {{--                FDN --}}
                        {{--              </th> --}}
                        <th scope="col" class="px-6 py-3">
                            Receipt No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Date issued
                        </th>

                        <th scope="col" class="px-6 py-3">
                            Customer
                        </th>

                        <th scope="col" class="px-6 py-3">
                            Currency
                        </th>

                        <th scope="col" class="px-6 py-3">
                            Gross Amount
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Tax Amount
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Download
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {{--            <tr class=" hover:bg-gray-50"> --}}
                    @forelse ($data as $item)
                        <tr>
                            {{--                <td> --}}
                            {{--                  {{ $item['id'] }} --}}
                            {{--                </td> --}}
                            {{--                <td>{{ $item['invoiceNo'] }}</td> --}}
                            <td>{{ $item['referenceNo'] ?? null }}</td>
                            <td>{{ $item['issuedDate'] }}</td>
                            <td>{{ $item['buyerBusinessName'] ?? null }}</td>
                            <td>{{ $item['currency'] }}</td>
                            <td>{{ number_format($item['grossAmount'], 2) }}</td>
                            <td>{{ number_format($item['taxAmount'], 2) }}</td>
                            <td>
                                <a href="{{ route('invoice.download.rt', $item['invoiceNo']) }}"
                                    class="btn btn-sm btn-primary hover:bg-green-900
                                                            font-normal rounded-sm text-sm px-3 py-1 mr-2 mb-2
                                                            "
                                    target="_blank">Preview
                                </a>

                            </td>
                        </tr>

                    @empty
                        <p class="mt-3 text-black font-bold text-lg my-10">
                            No items Data Available
                        </p>
                    @endforelse
                </tbody>
            </table>
        </div>
      </div>

        @if ($data->hasPages())
            <div class="pagination-wrapper my-1">
                <nav aria-label="Page navigation">
                    {{ $data->links() }}
                </nav>
            </div>
        @endif
    </div>

    {{-- <example-component></example-component> --}}

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
        function getCsrfToken() {
            return document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        $(document).ready(function() {

            $('#date').daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf(
                            'month')],
                        'This Year': [moment().startOf('year'), moment().endOf('year')]
                    },
                    autoUpdateInput: true,
                    "alwaysShowCalendars": true,
                    "startDate": "01-01-2023",
                    "endDate": "07-28-2023"
                },
                function(start, end, label) {
                    start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD');
                });

            $('#date').on('apply.daterangepicker', function(ev, picker) {
                const encodedDateRange = moment(picker.startDate).format('YYYY-MM-DD') + ' to ' + picker
                    .endDate.format(
                        'YYYY-MM-DD');
                // Decode the URL-encoded string
                const decodedDateRange = decodeURIComponent(encodedDateRange);

                // Split the date range into start and end dates
                const dateParts = decodedDateRange.split(' to ');

                // Format the dates as desired (e.g., 'MM/DD/YYYY')
                const startDate = dateParts[0]; // Assuming 'MM/DD/YYYY' format
                const endDate = dateParts[1]; // Assuming 'MM/DD/YYYY' format
                $(this).val(startDate + ' to ' + endDate);
            });

            $('#date').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.preview-btn');
            buttons.forEach(function(button) {
                button.addEventListener('click', async function() {
                    const id = this.getAttribute('data-id');
                    var buttonText = this.innerText;
                    this.innerText = 'Loading...';
                    await previewPdf(id);
                    this.innerText = buttonText;
                    $('#pdfModal').modal('show'); //
                });
            });
        });

        function previewPdf(id) {
            var iframe = document.getElementById('pdfViewer');
            var url = `{{ route('invoice.download.rt', ['id' => 'id']) }}`.replace('id', id);
            iframe.src = url;
            return new Promise(resolve => {
                iframe.onload = () => resolve();
            }); //ow the modal
        }
    </script>
@endpush
