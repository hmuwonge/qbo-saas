<?php
// Assuming $non and $non1 are the data you want to pass to JavaScript
$data = [
    'fiscalisedInvoices' => $non,
    'nonFiscalisedInvoices' => $non1,
];
?>

@extends('layouts.main')

@section('title', 'Quickbooks Main Dashboard')

@section('page-style')
{{--    <link rel="stylesheet" href="{{ asset(mix('assets/css/style.css')) }}">--}}
@endsection

@section('content')
    <div class="row col-lg-12 col-md-4 order-1">
{{--        <div class="row">--}}
            <div class="col-lg-3 col-md-12 col-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('assets/img/icons/unicons/chart-success.png') }}" alt="chart success"
                                    class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Invoices fiscalised with ura</span>
                        <h3 class="card-title mb-1">{{$counters['invoices_fiscalised']}}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-12 col-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                    class="rounded">
                            </div>
                        </div>
                        <span>Goods & Services Not yet fiscalised</span>
                        <h3 class="card-title text-nowrap mb-1">{{ $counters['items'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-12 col-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('assets/img/icons/unicons/paypal.png') }}" alt="Credit Card"
                                    class="rounded">
                            </div>
                        </div>
                        <span class="d-block mb-1">Purchases</span>
                        <h3 class="card-title text-nowrap mb-1">{{ $counters['purchases'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-12 col-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                    class="rounded">
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Invoices</span>
                        <h3 class="card-title mb-2">{{ $counters['invoice_count'] }}</h3>
                    </div>
                </div>
            </div>

{{--        </div>--}}
    </div>
        <!-- END ROW -->
        <!-- ROW -->
        <div class="row">
            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Invoice Statistics</h3>

                    </div>
                    <div class="card-body">
                        <div id="Qboechart" class="chartsh chart-dropshadow"></div>
                        <div class="row mt-2">
                            <div class="col text-center mt-4">
                                <p class="mb-1 fw-semibold text-muted-dark">Total Fiscalised Invoices</p>
                                <h5 class="mb-0 fw-semibold">{{$counters['invoices_fiscalised']}}</h5>
                            </div>
                            <div class="col text-center mt-4">
                                <p class="mb-1 fw-semibold text-muted-dark">Invoices Not Yet Fiscalised
                                </p>
                                <h5 class="mb-0 fw-semibold">{{$counters['invoice_not_fiscalised']}}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END ROW -->

        <!--        Recent validation erros -->
        @if (count($errors) > 0)
            <div class="row ">
                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="card my-2">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <header class="card-title mb-0">Recent Validation Errors(Last 3) </header>

                            <a href="{{ route('autosync.all') }}" class="btn btn-sm text-white  bg-primary fw-bold">
                                View all
                            </a>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-group bg-label-danger list-none  border-red-200">

                                @foreach ($errors as $error)
                                    <li class="list-group-item  border-red-200 rounded-sm">
                                        <div style="line-height: 1;" class="d-flex justify-content-between">
                                            @if ($error->auto_sync)
                                                @if ($error->auto_sync->sync_category)
                                                    <span class="badge rounded-pill bg-label-danger">
                                                        {{ $error->auto_sync->sync_category }}</span>
                                                @endif
                                            @endif


                                            &nbsp;&nbsp;<b class="font-bold text-base">{{ $error->message }}</b>
                                              <i class="text-dark ">{{ $error->activity_time }}</i>
                                        </div>
                                        <br />
                                        {{ $error->short_server_response }}
                                    </li>
                                @endforeach

                            </ul>
                        </div>

                    </div>

                </div>


            </div>
        @endif


        <!-- ROW -->
        <div class="row">
            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="card my-2">
                    <div class="card-header d-flex justify-content-between align-items-center ">
                        <h3 class="card-title ">Recent Fiscalised Invoices (Latest 10)</h3>

                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped bg-success mb-0 text-nowrap">
                                <thead class="bg-dark">
                                    <tr>
                                        <th scope="col" class="text-light fw-semibold px-5 fs-13 w-3">
                                            Fiscal Number
                                        </th>
                                        <th scope="col" class="text-light fw-semibold fs-13">
                                            Date Issued
                                        </th>
                                        <th scope="col" class="text-light fw-semibold fs-13">
                                            Name of Buyer
                                        </th>
                                        <th scope="col" class="text-light fw-semibold fs-13">
                                            Gross Amount
                                        </th>
                                        <th scope="col" class=" text-light fw-semibold fs-13">
                                            Tax Amount
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-dark">
                                    @forelse ($latestInvoices as $item)
                                        <tr class="bg-label-hover-success">
                                            <th scope="row"
                                                class=" font-bold font-medium text-gray-900 whitespace-nowrap ">
                                                <span class="font-bold text-lg fs-5 fw-bold"> {{ $item->fiscalNumber }}</span>
                                            </th>
                                            <td class="">
                                                {{ $item->qb_created_at }}

                                            </td>
                                            <td class="">
                                                {{ $item->customerName }}

                                            </td>
                                            <td class="">
                                                {{ $item->amount }}
                                            </td>
                                            <td class="">
                                              {{ $item->tax_amount }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr> No invoices found</tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END ROW -->
    @endsection
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
@endpush
    @push('javascript')
        <script src="{{ asset('vendor/modules/moment.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
        <script src="{{ asset('vendor/daterangepicker/daterangepicker.min.js') }}"></script>

        <script>
            $(function(e) {
                'use strict'

              const jsonData = @json($data);
                const fiscalisedInvoices = jsonData.fiscalisedInvoices;
                const nonFiscalisedInvoices = jsonData.nonFiscalisedInvoices;

                const labels1 = fiscalisedInvoices.original.lable;
                //  const labels2 = fiscalisedInvoices.original.lable;

                const counts = fiscalisedInvoices.original.value;
                const counts1 = nonFiscalisedInvoices.original.value;
                const lableValues = Object.values(labels1);
                const fiscalised = Object.values(counts);
                const notFiscalised = Object.values(counts1);

                //   console.log(fiscalisedInvoices.original.value);
                //   console.log(arrayCount);

                /*echart2*/
                const chartdata = [{
                        name: 'Non Fiscalised Invoices',
                        type: 'bar',
                        data: notFiscalised,
                        symbolSize: 10,
                        itemStyle: {
                            normal: {
                                barBorderRadius: [100, 100, 0, 0],
                            }
                        },
                    },
                    {
                        name: ' Fiscalised Invoices',
                        type: 'bar',
                        data: fiscalised,
                        symbolSize: 10,
                        itemStyle: {
                            normal: {
                                barBorderRadius: [100, 100, 0, 0],
                                barBorderWidth: ['2']
                            }
                        },
                    }
                ];

                var chart = document.getElementById('Qboechart');
                var barChart = echarts.init(chart);

                const option = {
                    grid: {
                        top: '6',
                        right: '0',
                        bottom: '17',
                        left: '25',
                        borderColor: 'rgba(119, 119, 142, 0.08)',
                    },
                    xAxis: {
                        data: lableValues,

                        axisLine: {
                            lineStyle: {
                                color: 'rgba(161, 161, 161,0.3)'
                            }
                        },
                        axisLabel: {
                            fontSize: 10,
                            color: '#a1a1a1'
                        }
                    },
                    tooltip: {
                        show: true,
                        showContent: true,
                        alwaysShowContent: true,
                        triggerOn: 'mousemove',
                        trigger: 'axis',
                        axisPointer: {
                            label: {
                                show: false,
                            }
                        }

                    },
                    yAxis: {
                        splitLine: {
                            lineStyle: {
                                color: 'rgba(119, 119, 142, 0.08)'
                            }
                        },
                        axisLine: {
                            lineStyle: {
                                color: 'rgba(119, 119, 142, 0.08)'
                            }
                        },
                        axisLabel: {
                            fontSize: 10,
                            color: '#a1a1a1'
                        }
                    },
                    series: chartdata,
                    color: ['#467fcf', '#5eba00', ]
                };

                barChart.setOption(option);
                window.addEventListener('resize', function() {
                    barChart.resize();
                })

                var chart = new ApexCharts(document.querySelector("#visit-by-departments"), options);
                chart.render();
            });
        </script>
    @endpush
