@extends('layouts.main')

@section('styles')
@endsection

@section('title')
    {{ $data->Name }}
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

    </div>
    <!-- END PAGE HEADER -->

    <!-- ROW -->
    <div class="row">

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="bg-white d-flex justify-content-between p-2 content-center rounded ">
                <h2 class="font-weight-bold fs-13">
                    Product Details
                </h2>
                <div class="">
                    <a type="button" class="btn btn-sm btn-primary" href="{{ route('quickbooks.register-product', ['id' => $data->Id, 'redo' => 'yes']) }}">Modify Product</a>
                </div>
            </div>

            <div class="card my-2">
               <div class="card-body">
                <div class="bg-white drop-shadow-sm rounded  mt-10">

                    <div class="d-flex justify-content-between p-0 border rounded fw-bolder bg-info p-2">
                        <div class=" font-weight-500 fs-13">
                            Are you getting an error that this item is not registered
                            with the EFRIS platform?
                        </div>
                        <div class="">
                            <a type="button" class="btn btn-sm btn-success" href="{{ route('quickbooks.register-product',$data->Id) }}">Register Item</a>

                        </div>
                    </div>

                    <div class="rounded-sm mt-2">
                        <ul class="list-group fw-bolder">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Goods Name
                                <span class="text-start font-bold">{{ $data->Name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Item Code
                                <span class="text-start">{{ $data->Sku }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Has Alternative Unit of Measure
                                <span class="">
                                    {{ !isset($product->havePieceUnit) ? "Not Yet" : ($product->havePieceUnit == "102" ? "No" : "Yes") }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                UNSPSC
                                <span class="">
                                    {{
                                       !isset($data->AdditionalDetails) ? "Not Set" :($data->AdditionalDetails->commodityCategoryId)
                                    }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Currency
                                <span class="">
                                    {{ !isset($product->currency) ? "Not Set" : ($product->currency == "101" ? "UGX": "UGX") }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Alternative Unit Price
                                <span class="">
                                   {{
                                       !isset($data->AdditionalDetails) ? "Not Set" :
                                        ($data->AdditionalDetails->pieceUnitPrice)
                                    }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Piece Scaled Value
                                <span class="">
                                   {{ $product->pieceScaledValue }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Unit of Measure
                                <span class="">
                                   {{ $product->unitOfMeasure }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Unit Price
                                <span class="">
                                   {{ $data->UnitPrice }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Have Other Unit
                                <span class="">
                                  {{ !isset($product->haveOtherUnit)  ? "Not Set" : ($product->haveOtherUnit == "102" ? "No" :" Yes") }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Package Scaled Value
                                <span class="">
                                  {{ !isset($product->haveOtherUnit)  ? "Not Set" : ($product->haveOtherUnit == "102" ? "No" :" Yes") }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Package Scaled Value
                                <span class="">
                                {{ $product->packageScaleValue }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Has Excise Tax
                                <span class="">
                               {{ !isset($data->AdditionalDetails) ? "Not Yet" : ($data->AdditionalDetails->haveExciseTax == "102" ? "No" : "Yes") }}
                                </span>
                            </li>
                        </ul>

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
