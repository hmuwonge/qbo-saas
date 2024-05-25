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
@section('title','Validation Errors')
@section('content')
    <!-- PAGE HEADER -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title">
              <h4 class="m-b-10">{{ __('All Quickbooks  Invoice Validation Errors') }}</h4>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item "><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
              <li class="breadcrumb-item">{{ __('Quickbooks') }}</li>
              <li class="breadcrumb-item active">{{ __('Invoice Validation') }}</li>
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
                    <h3 class="card-title">Invoices That Passed Validations</h3>
                </div>
                <div class="card-body">


                    <p>Found <code class="highlighter-rouge">{{count($data['filteredList'])}}</code></p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-nowrap text-md-nowrap  mb-0">
                            <thead class="">
                                <tr>
                                  <th scope="col" class="px-6 py-3 text-dark">Ref Number</th>
                                  <th scope="col" class="px-6 py-3">Transaction Date</th>
                                  <th scope="col" class="px-6 py-3">Customer Details</th>

                                  <th scope="col" class="px-6 py-3">Validation Errors</th>
                                </tr>
                              </thead>
                            <tbody>
                                @forelse ($data['filteredList'] as $invoice)
                                <tr class="">
                                    <th scope="row" class="font-medium whitespace-nowrap">
                                      {{ $invoice->refNumber }}
                                    </th>
                                    <td class="px-6 py-4">
                                      {{ $invoice->transactionDate }}
                                    </td>
                                    <td class="px-6 py-4">
                                      {!! $invoice->customerDetails !!}>

                                    </td>
                                    <td class="px-6 py-2 font-bold text-sm">
                                      {!! $invoice->validationErrors !!}
                                    </td>
                                  </tr>
                                @empty
                                @endforelse

                            </tbody>
                        </table>
                    </div>

{{--                    @if ($data['filteredList']->hasPages())--}}
                        <div class="pagination-wrapper my-1">
                            <nav aria-label="Page navigation">
                                {!!  $data['links'] !!}
                            </nav>
                        </div>
{{--                    @endif--}}
                </div>
            </div>
        </div>
    </div>
{{--    </div>--}}
    <!-- END ROW -->
@endsection

@section('scripts')

@endsection
