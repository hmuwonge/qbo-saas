@extends('layouts.main')


@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
@endpush

@section('title', __('Fiscalised Invoices'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('index') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('plans.myplan') }}">{{ __('Quickbooks') }}</a></li>
    <li class="breadcrumb-item">{{ __('All Efris Invoices') }}</li>
@endsection

@section('content')

    <!-- END PAGE HEADER -->

    <!-- ROW -->
    <div class="row" id="app">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"> {{ __('Fiscalised Invoices') }}</h3>
            </div>

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
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-nowrap text-md-nowrap mb-0">
                        <thead class="bg-secondary text-white">
                            <tr class="">
                                <th scope="col" class="">
                                    Invoice No
                                </th>
                                <th scope="col" class="">
                                    Reference No
                                </th>
                                <th scope="col" class="">
                                    Date issued
                                </th>

                                <th scope="col" class="">
                                    Customer
                                </th>

                                <th scope="col" class="">
                                    Currency
                                </th>

                                <th scope="col" class="">
                                    Gross Amount
                                </th>
                                <th scope="col" class="">
                                    Tax Amount
                                </th>
                                <th scope="col" class="">
                                    Download
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $item)
                                <tr>
                                    <td>{{ $item['invoiceNo'] }}</td>
                                    <td>{{ $item['referenceNo']??null }}</td>
                                    <td>{{ Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $item['issuedDate'])->format('d-m-Y') }}
                                    </td>
                                    <td>{{ $item['buyerBusinessName']??null }}</td>
                                    <td>{{ $item['currency'] }}</td>
                                    <td>{{ number_format($item['grossAmount'], 2) }}</td>
                                    <td>{{ number_format($item['taxAmount'], 2) }}</td>
                                    <td>
{{--                                        <a href="{{ route('invoice.download.rt', $item['invoiceNo']) }}"--}}
{{--                                            class="btn btn-sm btn-primary hover:bg-green-900--}}
{{--                                                            font-normal rounded-sm text-sm px-3 py-1 mr-2 mb-2--}}
{{--                                                            "--}}
{{--                                            target="_blank">Preview--}}
{{--                                        </a>--}}
                                        <button class="preview-btn btn btn-primary btn-sm" data-id="{{ $item['invoiceNo'] }}">Preview</button>
                                    </td>
                                </tr>
                            @empty
                                <p class="mt-3 text-black font-bold text-lg my-10">
                                    No items Data Available
                                </p>
                            @endforelse

                        </tbody>
                    </table>
                    @include('invoice_preview_modal')
                </div>

                <div class="pagination-wrapper my-1">
                    <nav aria-label="Page navigation">
                        {!! $links !!}
                    </nav>
                </div>
            </div>


        </div>
    </div>
    <!-- END ROW -->
@endsection

@push('javascript')
    <script src="{{ asset('vendor/modules/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/daterangepicker/daterangepicker.min.js') }}"></script>
  <!-- INDEX JS -->
  <script>
    function getCsrfToken() {
      return document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    $(document).ready(function(){

    $('#date').daterangepicker({
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
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
      const encodedDateRange = moment(picker.startDate).format('YYYY-MM-DD') + ' to ' + picker.endDate.format(
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

    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.preview-btn');
        buttons.forEach(function(button) {
            button.addEventListener('click',async function() {
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
        });//ow the modal
    }
  </script>
@endpush
