
@extends('layouts.main')

@section('title','Fiscalise Receipts')

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
  <div class="row" id="app">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"> Receipts which have been fiscalised via the EFRIS platform</h3>
      </div>
      <div class="card-body">
        <div class="d-flex row form-group py-2">
          {!! Form::open([
              'route' => 'efris.goods.get',
              'method' => 'Get',
              'class' => 'd-flex row',
              'enctype' => 'multipart/form-data',
          ]) !!}
          <input name="pageSize" value="99" type="hidden" />
          <div class="col-lg-3">
            {{ Form::text('goodsCode', null, ['class' => 'form-control', 'id' => 'buyer_type', 'style' => 'width:250px;', 'placeholder' => 'Search by invoice number']) }}
          </div>
          <div class="col-lg-3">
            {{ Form::text('goodsName', null, ['class' => 'form-control', 'id' => 'industry_code', 'style' => 'width:250px;', 'placeholder' => 'Search by invoice no.']) }}
          </div>
          <div class="col-lg-2">
            <button type="submit" class="btn btn-primary">Fetch Invoices</button>
            {!! Form::close() !!}
          </div>

        </div>


        {{-- <p>Found <code class="highlighter-rouge">{{ $page_data['totalSize'] }}</code>tbetween <code
                class="highlighter-rouge">{{ $data['startdate'] }} and {{ $data['enddate'] }}</code>.</p> --}}
        <div class="table-responsive">
          <table class="table table-bordered text-nowrap text-md-nowrap  table-info mb-0">
            <thead class="text-base text-black uppercase bg-gray-300 ">
            <tr>
              <th scope="col" class="p-4">
                FDN
              </th>
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
            <tr class=" hover:bg-gray-50">
            @forelse ($data as $item)
              <tr>
                <td>
                  {{ $item['id'] }}
                </td>
                <td>{{ $item['invoiceNo'] }}</td>
                <td>{{ $item['referenceNo'] }}</td>
                <td>{{ $item['issuedDate'] }}</td>
                <td>{{ $item['buyerBusinessName']??null }}</td>
                <td>{{ $item['currency'] }}</td>
                <td>{{ number_format($item['grossAmount'],2)}}</td>
                <td>{{ number_format($item['taxAmount'],2) }}</td>
                <td>
                  <a href="{{route('invoice.download.rt',$item['invoiceNo'])}}" class="btn btn-sm btn-primary hover:bg-green-900
                                                            font-normal rounded-sm text-sm px-3 py-1 mr-2 mb-2
                                                            " target="_blank">Preview
                  </a>

                </td>
              </tr>
            @empty
              <p class="mt-3 text-black font-bold text-lg my-10">
                No items Data Available
              </p>
            @endforelse

            </tr>
            </tbody>
          </table>
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

@section('scripts')

@endsection
