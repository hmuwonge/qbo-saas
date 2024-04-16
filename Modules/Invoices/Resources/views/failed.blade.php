@extends('layouts.main')

@section('styles')
@endsection

@section('title', 'Failed validations')

@section('content')
    <!-- PAGE HEADER -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title">
              <h4 class="m-b-10">{{ __('All Quickbooks  Invoice Failed Validation') }}</h4>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item "><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
              <li class="breadcrumb-item">{{ __('Quickbooks') }}</li>
              <li class="breadcrumb-item active">{{ __('Invoice Failed Validation') }}</li>
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
                    <h3 class="card-title">Failded Validations</h3>
                </div>
                <div class="card-body">

{{--                    <p>Found <code class="highlighter-rouge">{{ $data['total'] }}</code>between <code--}}
{{--                            class="highlighter-rouge">{{ $data['startdate'] }} and {{ $data['enddate'] }}</code>.</p>--}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-nowrap text-md-nowrap mb-0">
                            <thead class="bg-secondary">
                                <tr class="bg-warning">
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
    <!-- END ROW -->
@endsection

