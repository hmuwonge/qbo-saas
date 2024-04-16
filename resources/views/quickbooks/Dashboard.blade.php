@extends('layouts.main')

@section('styles')
@endsection

@section('content')
    <!-- PAGE HEADER -->
    <div class="page-header d-sm-flex d-block">
        <ol class="breadcrumb mb-sm-0 mb-3">
            <!-- breadcrumb -->
            <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Quickbooks</li>
            <li class="breadcrumb-item active" aria-current="page">Index</li>
        </ol><!-- End breadcrumb -->
        <div class="ms-auto">
            <div>
                <a href="javascript:void(0);" class="btn bg-secondary-transparent text-secondary btn-sm"
                    data-bs-toggle="tooltip" title="" data-bs-placement="bottom" data-bs-original-title="Rating">
                    <span>
                        <i class="fa fa-star"></i>
                    </span>
                </a>
                <a href="{{ url('lockscreen') }}" class="btn bg-primary-transparent text-primary mx-2 btn-sm"
                    data-bs-toggle="tooltip" title="" data-bs-placement="bottom" data-bs-original-title="lock">
                    <span>
                        <i class="fa fa-lock"></i>
                    </span>
                </a>
                <a href="javascript:void(0);" class="btn bg-warning-transparent text-warning btn-sm"
                    data-bs-toggle="tooltip" title="" data-bs-placement="bottom" data-bs-original-title="Add New">
                    <span>
                        <i class="fa fa-plus"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>
    <!-- END PAGE HEADER -->

    <!-- ROW -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex">
                        <div>
                            <p class="mb-0 text-dark fw-semibold">Invoices fiscalised with ura</p>
                            <h3 class="mt-1 mb-1 text-dark fw-semibold">25.2K</h3>

                        </div>
                        <span class="ms-auto my-auto bg-danger-transparent avatar avatar-lg brround text-danger">
                            <i class="fe fe-folder fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex">
                        <div>
                            <p class="mb-0 text-dark fw-semibold">Goods & Services Not yet fiscalised</p>
                            <h3 class="mt-1 mb-1 text-dark fw-semibold">19,584</h3>

                        </div>
                        <span class="ms-auto my-auto bg-primary-transparent avatar avatar-lg brround text-primary">
                            <i class="fe fe-user fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex">
                        <div>
                            <p class="mb-0 text-dark fw-semibold">Daily Updates</p>
                            <h3 class="mt-1 mb-1 fw-semibold">626</h3>

                        </div>
                        <span class="ms-auto my-auto bg-secondary-transparent avatar avatar-lg brround text-secondary">
                            <i class="fe fe-bar-chart-2 fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-lg-6 col-sm-6">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex">
                        <div>
                            <p class="mb-0 text-dark fw-semibold">Daily Operations</p>
                            <h3 class="mt-1 mb-1 text-dark fw-semibold">46</h3>

                        </div>
                        <span class="ms-auto my-auto bg-info-transparent avatar avatar-lg brround text-info">
                            <i class="fe fe-scissors fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW -->


    <!-- ROW -->
    <div class="row">
        <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center border-bottom-0">
                    <h3 class="card-title mb-0">Upcoming Appoinments</h3>
                    <div class="dropdown">
                        <button type="button" class="d-flex align-items-center btn btn-sm bg-primary-transparent fw-bold"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            This week<i class="fe fe-chevron-down fw-semibold mx-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" role="menu" data-popper-placement="bottom-end">
                            <li><a href="javascript:void(0);">Last week</a></li>
                            <li><a href="javascript:void(0);">Monthly</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table border-0 mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-top-0 text-dark fw-semibold px-5 fs-13 w-3">Patient Name</th>
                                    <th class="border-top-0 text-dark fw-semibold fs-13">Gender</th>
                                    <th class="border-top-0 text-dark fw-semibold fs-13">Disease</th>
                                    <th class="border-top-0 text-dark fw-semibold fs-13 text-center">Date</th>
                                    <th class="border-top-0 text-dark fw-semibold pe-5 text-end fs-13">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-dark">
                                <tr class="border-bottom">
                                    <td class="d-flex border-bottom-0">
                                        <div>
                                            <span class="avatar avatar-md mx-2"><img
                                                    src="{{ asset('build/assets/images/users/male/8.jpg') }}"
                                                    alt="img" class="rounded-circle cover-image"></span>
                                        </div>
                                        <div class="flex-1 my-auto">
                                            <h6 class="mb-0 fw-semibold">Robertson</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 fw-semibold">Male</h6>
                                    </td>
                                    <td>
                                        <span class="badge badge-info-transparent rounded-pill">Jaundice</span>
                                    </td>
                                    <td class="fw-semibold text-center fs-13">
                                        15 Jan 2021
                                    </td>
                                    <td class="text-end pe-5">
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-outline-default text-dark fw-semibold">Cancel</a>
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-primary fw-semibold">Re-schedule</a>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="d-flex border-bottom-0">
                                        <div>
                                            <span class="avatar avatar-md mx-2"><img
                                                    src="{{ asset('build/assets/images/users/female/11.jpg') }}"
                                                    alt="img" class="rounded-circle cover-image"></span>
                                        </div>
                                        <div class="flex-1 my-auto">
                                            <h6 class="mb-0 fw-semibold">Jenny Willson</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 fw-semibold">Female</h6>
                                    </td>
                                    <td>
                                        <span class="badge badge-purple-transparent rounded-pill">Diabetes</span>
                                    </td>
                                    <td class="fw-semibold text-center fs-13">
                                        05 Mar 2020
                                    </td>
                                    <td class="text-end pe-5">
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-outline-default text-dark fw-semibold">Cancel</a>
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-primary fw-semibold">Re-schedule</a>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="d-flex border-bottom-0">
                                        <div>
                                            <span class="avatar avatar-md mx-2"><img
                                                    src="{{ asset('build/assets/images/users/male/13.jpg') }}"
                                                    alt="img" class="rounded-circle cover-image"></span>
                                        </div>
                                        <div class="flex-1 my-auto">
                                            <h6 class="mb-0 fw-semibold">Steward</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 fw-semibold">Male</h6>
                                    </td>
                                    <td>
                                        <span class="badge badge-success-transparent rounded-pill">Bypass</span>
                                    </td>
                                    <td class="fw-semibold text-center fs-13">
                                        20 Apr 2020
                                    </td>
                                    <td class="text-end pe-5">
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-outline-default text-dark fw-semibold">Cancel</a>
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-primary fw-semibold">Re-schedule</a>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="d-flex border-bottom-0">
                                        <div>
                                            <span class="avatar avatar-md mx-2"><img
                                                    src="{{ asset('build/assets/images/users/male/5.jpg') }}"
                                                    alt="img" class="rounded-circle cover-image"></span>
                                        </div>
                                        <div class="flex-1 my-auto">
                                            <h6 class="mb-0 fw-semibold">Ralph Edward</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 fw-semibold">Male</h6>
                                    </td>
                                    <td>
                                        <span class="badge badge-pink-transparent rounded-pill">Jaundice</span>
                                    </td>
                                    <td class="fw-semibold text-center fs-13">
                                        24 Jan 2022
                                    </td>
                                    <td class="text-end pe-5">
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-outline-default text-dark fw-semibold">Cancel</a>
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-primary fw-semibold">Re-schedule</a>
                                    </td>
                                </tr>
                                <tr class="border-bottom-0">
                                    <td class="d-flex border-bottom-0">
                                        <div>
                                            <span class="avatar avatar-md mx-2"><img
                                                    src="{{ asset('build/assets/images/users/female/13.jpg') }}"
                                                    alt="img" class="rounded-circle cover-image"></span>
                                        </div>
                                        <div class="flex-1 my-auto">
                                            <h6 class="mb-0 fw-semibold">Mira Edora</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 fw-semibold">Female</h6>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning-transparent rounded-pill">Diabetes</span>
                                    </td>
                                    <td class="fw-semibold text-center fs-13">
                                        11 Dec 2021
                                    </td>
                                    <td class="text-end pe-5">
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-outline-default text-dark fw-semibold">Cancel</a>
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-primary fw-semibold">Re-schedule</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Country Wise Donors</h3>
                    <button type="button" class="btn btn-sm bg-primary-transparent fw-bold">
                        View all
                    </button>
                </div>
                <div class="card-body">
                    <div id="echart1" class="chartsh chart-dropshadow"></div>
                    <div class="row mt-2">
                        <div class="col text-center mt-4">
                            <p class="mb-1 fw-semibold text-muted-dark">Total Organ Donors</p>
                            <h5 class="mb-0 fw-semibold">63,254</h5>
                        </div>
                        <div class="col text-center mt-4">
                            <p class="mb-1 fw-semibold text-muted-dark">Males
                            </p>
                            <h5 class="mb-0 fw-semibold">32,548</h5>
                        </div>
                        <div class="col text-center mt-4">
                            <p class="mb-1 fw-semibold text-muted-dark">Females
                            </p>
                            <h5 class="mb-0 fw-semibold">30,706</h5>
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
    @vite('resources/assets/js/index4.js')
@endsection
