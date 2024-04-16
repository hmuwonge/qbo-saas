@extends('layouts.main')

@section('title')
   QuickBooks Goods & Services
@endsection

@section('styles')
@endsection

@section('content')
  <div class="page-header">
    <div class="page-block">
      <div class="row align-items-center">
        <div class="col-md-12">
          <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Quickbooks Goods & Services') }}</h4>
          </div>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Quickbooks') }}</a></li>
            <li class="breadcrumb-item active">{{ __(' Goods & Services') }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
{{--    <h4 class="fw-bold py-3 mb-4">--}}
{{--        <span class="text-muted fw-light"><a href="{{ url('index') }}">Home</a>/ QuickBooks /</span> Goods & Services--}}
{{--      </h4>--}}
    <!-- END PAGE HEADER -->

    <!-- ROW -->
    <div class="row">

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header d-inline-flex justify-content-between">
                    <h3 class="card-title">Quickbooks Goods & Services</h3>

                    <div class="d-inline-flex justify-between col-auto">
                        <a type="button" class="btn btn-sm btn-outline-primary p-2 col-auto" href="{{route('goods.syncItems')}}">
                            Verify With Efris
                        </a>

                        <button class="btn btn-sm btn-primary ml-1" href="{{route('goods.syncItems')}}">
                            Fetch From QBO
                        </button>
                    </div>

                  <div class="d-inline-flex gap-1 col-auto ">
                    {!! Form::open([
                        'route' => ['goods.all'],
                        'method' => 'get',
                        'class' => 'form-horizontal  row g-3'
                    ]) !!}

                    <div class="input-group ">
                      {{ Form::text('q', null, ['class' => 'form-control form-control-sm col-4', 'style' => '']) }}
                      <button type="submit" class="btn btn-sm btn-primary">Search for Item</button>
                    </div>

                    {!! Form::close() !!}
                  </div>
                </div>
                <div class="card-body">
{{--
                    <p>Found <code class="highlighter-rouge">{{ $data['total'] }}</code>tbetween <code
                            class="highlighter-rouge">{{ $data['startdate'] }} and {{ $data['enddate'] }}</code>.</p> --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-nowrap text-md-nowrap mb-0">
                            <thead class="bg-secondary">
                                <tr>
                                  <th scope="col" class=" whitespace-nowrap">
                                      Item code
                                  </th>
                                  <th scope="col" class=" whitespace-nowrap">
                                    Commodity Name
                                  </th>
                                  <th scope="col" class="">
                                    <div class="flex items-center justify-center">Unit Price</div>
                                  </th>
                                  <th scope="col" class="">
                                    <div class="flex items-center justify-center">Type</div>
                                  </th>
                                  <th scope="col" class="">
                                    <div class="flex items-center justify-center">URA Reg. Status</div>
                                  </th>

                                  <th scope="col" class="">
                                    <div class="flex items-center justify-center">Stock Levels</div>
                                  </th>

                                  <th scope="col" class="">
                                    <div class="flex items-center justify-center">Opening Stock</div>
                                  </th>
                                  <th scope="col" class="">Options</th>
                                </tr>
                              </thead>
                            <tbody>
                                @forelse ($data as $item)
                                    <tr class="">
                                        <th class=" text-left">
                                            {{ $item['Sku']  ?? 'Not Set' }}
                                        </th>
                                        <th class="text-start fs-6">
                                            {{ $item['Name'] }}
                                        </th>
                                        <td class="text-start">
                                            {!! $item['UnitPriceAmount'] !!}
                                        </td>
                                        <td class="text-center">
                                            {{ $item['Type'] }}
                                        </td>
                                        <td class="text-center">
                                           {!! $item['EfrisRegStatus'] !!}
                                        </td>
                                        <td class="text-center">
                                            {!!  $item['stockLevel'] !!}
                                        </td>

                                        <td class="text-center">
                                            {!! $item['OpeningStock'] !!}
                                        </td>
                                        <td class="text-center">
                                            <div class="content-center">
                                               {!! $item['ItemOptions'] !!}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                            {{-- <img src="../../../../assets/folder.png" width="100"> --}}
                                            <p class="mt-3 text-black font-bold text-lg my-10">
                                                No Data Available
                                            </p>
                                    </tr>

                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    {{-- @if ($data->hasPages()) --}}
                        <div class="pagination-wrapper my-1">
                            <nav aria-label="Page navigation">
                                {!! $links !!}

                            </nav>
                        </div>
                    {{-- @endif --}}
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- END ROW -->
@endsection

@section('scripts')

@endsection
