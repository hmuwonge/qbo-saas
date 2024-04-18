@extends('layouts.main')

@section('title','Registered Goods & Services')

@section('content')
    <!-- PAGE HEADER -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
{{--            <div class="page-header-title">--}}
{{--              <h4 class="m-b-10">{{ __('All Efris goods and services') }}</h4>--}}
{{--            </div>--}}
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
              <li class="breadcrumb-item" aria-current="page">URA Reports</li>
              <li class="breadcrumb-item active" aria-current="page">
                All Efris goods and services</li>
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
                <h3 class="card-title"> Efris Synced Goods and Services</h3>
            </div>
            <div class="card-body">
                <div class="d-flex row ">
                    {!! Form::open([
                        'route' => 'efris.goods.get',
                        'method' => 'Get',
                        'class' => 'd-flex row form-group',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
{{--                    <input name="pageSize" value="99" type="hidden" />--}}
                    <div class="col-lg-3">
                        {{ Form::text('goodsCode', null, ['class' => 'form-control col-auto', 'id' => 'buyer_type', 'style' => 'width:250px;', 'placeholder' => 'Search with goods code']) }}
                    </div>
                    <div class="col-lg-3">
                        {{ Form::text('goodsName', null, ['class' => 'form-control col-auto', 'id' => 'industry_code', 'style' => 'width:250px;', 'placeholder' => 'Search with goods name']) }}
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary col-auto">Fetch</button>
                        {!! Form::close() !!}
                    </div>

                    <p class="my-2">Found <code class="highlighter-rouge">{{ $total }}</code> Records</p>

                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-nowrap text-md-nowrap mb-0">
                        <thead class="bg-secondary">
                            <tr>
                                <th scope="col" class="">
                                    Commodity Category Code
                                </th>
                                <th scope="col" class="">
                                    Commodity Category Name
                                </th>
                                <th scope="col" class="">
                                    Goods Code
                                </th>

                                <th scope="col" class="">
                                    Item Name
                                </th>

                                <th scope="col" class="">
                                    Stock
                                </th>

                                <th scope="col" class="">
                                    Measure Unit
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $item)
                                <tr>
                                    <td>{{ $item['commodityCategoryCode'] }}</td>
                                    <td>{{ $item['commodityCategoryName'] }}</td>
                                    <td>{{ $item['goodsCode'] }}</td>
                                    <td>{{ $item['goodsName'] }}</td>
                                    <td>{{ number_format($item['stock']) }}</td>
                                    <td>{{ $item['measureUnit'] }}</td>

                                </tr>
                            @empty
                                <p class="mt-3 text-black font-bold text-lg my-10">
                                    No items Data Available
                                </p>
                            @endforelse

                        </tbody>
                    </table>
                </div>

{{--                @if ($records->hasPages())--}}
                    <div class="pagination-wrapper my-1">
                        <nav aria-label="Page navigation">
                          {!! $links !!}
                        </nav>
                    </div>
{{--                @endif--}}
            </div>


        </div>
    </div>
    <!-- END ROW -->
@endsection

@section('scripts')
@endsection
