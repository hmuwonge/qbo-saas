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
            <li class="breadcrumb-item " aria-current="page">Quickbooks</li>
            <li class="breadcrumb-item " aria-current="page">Invoices</li>
            <li class="breadcrumb-item active" aria-current="page">ValidationErrors</li>
        </ol><!-- End breadcrumb -->

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


                    <p>Found <code class="highlighter-rouge">{{$data['total']}}</code></p>
                    <div class="table-responsive">
                        <table class="table border  text-nowrap text-md-nowrap table-striped mb-0">
                            <thead class="text-base text-black uppercase bg-gray-300">
                                <tr>
                                  <th scope="col" class="px-6 py-3">Ref Number</th>
                                  <th scope="col" class="px-6 py-3">Transaction Date</th>
                                  <th scope="col" class="px-6 py-3">Customer Details</th>

                                  <th scope="col" class="px-6 py-3">Validation Errors</th>
                                </tr>
                              </thead>
                            <tbody>
                                @forelse ($data['filteredList'] as $invoice)
                                <tr class="hover:bg-gray-100 hover:cursor-pointer">
                                    <th scope="row" class="px-6 py-4 font-medium whitespace-nowrap">
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
                                    <p>nothing found</p>
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

@endsection
