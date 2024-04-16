@php
use Carbon\Carbon;
@endphp
@extends('layouts.main')

@push('styles')
    <style>
        form div.required label.control-label:after {
            content: " * ";
            color: red;
        }
    </style>
@endpush

@section('title','Register product with URA')

    @php
        $categories = [
            '101' => 'Yes',
            '102' => 'No'
        ];

        $efItem = empty($efrisItem) ? null : $efrisItem[0];

        // Assign default values
        if ($efItem) {
            $efris->unitOfMeasure = $efItem->measureUnit;
            $efris->currency = $efItem->currency;
            $efris->commodityCategoryId = $efItem->commodityCategoryCode;
            $efris->havePieceUnit = 102;
            $efris->haveOtherUnit = 102;
            $efris->pieceMeasureUnit = @$efItem->pieceMeasureUnit;
            $efris->pieceScaledValue = @$efItem->pieceScaledValue;
            $efris->packageScaledValue = @$efItem->packageScaledValue;
            $efris->item_tax_rule = "URA";
            $efris->haveExciseTax = 102;
//            $efris->exciseDutyCode = $efItem->exciseDutyCode;
        }

        if ($redo == "yes") {
            // Are we re-registering? First remove the record from the DB
            \DB::table('efris_items')->where('id', $item->Item->Id)->delete();
        }
    @endphp

@section('content')
    <!-- PAGE HEADER -->
    <div class="page-header d-sm-flex d-block">
        <ol class="breadcrumb mb-sm-0 mb-3">
            <!-- breadcrumb -->
            <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
            <li class="breadcrumb-item" aria-current="page">Quickbooks</li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('goods.all') }}">Goods</a></li>
        </ol><!-- End breadcrumb -->

    </div>
    <!-- END PAGE HEADER -->
    @if (session('danger'))
        <div class="alert alert-danger" role="alert">
            {{ session('danger') }}
        </div>
    @endif

    <div class="bg-white p-2 rounded-2 my-2 align-items-start d-flex justify-content-between">
        <h2>Register product</h2>
        <a class="btn btn-sm btn-gray-dark" href="{{ url()->previous() }}">
            <i class="fa  fa-arrow-circle-o-left"></i>
            <span>Back</span>
        </a>
    </div>

{{--@include('goods::includes.alerts')--}}
    <!-- ROW -->
    <div class="" id="app">
        @if ($efItem && $redo == "no")
            <div class="alert alert-warning">
                <span class="micon dw dw-info"></span>
                This item is already registered in the URA EFRIS platform. Please click the submit button below to sync the item details
            </div>
        @endif

        @if ($redo == "yes")
            <div class="alert alert-info">
                <span class="micon dw dw-info"></span>
                This item <u>may not have been registered</u> with the URA EFRIS platform the first time. Please submit the form below to register it with the URA platform
            </div>
        @endif
            <div class="bg-white rounded p-2">
            <div class="row">

                <div class="col-lg-6 fw-bolder">
                    <table class="table table-striped">
                        <tr>
                            <th>Name</th>
                            <td>{{ optional($item->Item)->Name }}</td>
                        </tr>
                        <tr>
                            <th>Product Code</th>
                            <td>{{ optional($item->Item)->Sku }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ optional($item->Item)->Description }}</td>
                        </tr>
                        <tr>
                            <th>Product Type</th>
                            <td>{{ optional($item->Item)->Type }}</td>
                        </tr>
                        <tr>
                            <th>Unit Price</th>
                            <td>{{ optional($item->Item)->UnitPrice }}</td>
                        </tr>
                        <tr>
                            <th>Purchase Cost</th>
                            <td>{{ optional($item->Item)->PurchaseCost }}</td>
                        </tr>
                        <tr>
                            <th>Preferred Vendor</th>
                            <td>{{ optional(optional($item->Item)->PrefVendorRef)->name }}</td>
                        </tr>
                        <tr>
                            <th>Quantity in hand</th>
                            <td>{{ optional($item->Item)->QtyOnHand }}</td>
                        </tr>
                        <tr>
                            <th>Re-order point</th>
                            <td>{{ optional($item->Item)->ReorderPoint }}</td>
                        </tr>
                        <tr>
                            <th>Inventory Start Date</th>
                            <td>{{ optional($item->Item)->InvStartDate }}</td>
                        </tr>
                        <tr>
                            <th>Registered on</th>
                            <td>{{ optional(optional($item->Item->MetaData)->CreateTime)->value }}</td>
                        </tr>
                        <tr>
                            <th>Last updated on</th>
                            <td>{{ optional(optional($item->Item->MetaData)->LastUpdatedTime)->value }}</td>
                        </tr>
                        <tr>
                            <th>Expense Account</th>
                            <td>{{ optional(optional($item->Item)->ExpenseAccountRef)->name }}</td>
                        </tr>
                        <tr>
                            <th>Assets Account</th>
                            <td>{{ optional(optional($item->Item)->AssetAccountRef)->name }}</td>
                        </tr>
                        <tr>
                            <th>Income Account</th>
                            <td>{{ optional(optional($item->Item)->IncomeAccountRef)->name }}</td>
                        </tr>
                        <tr>
                            <th>Is Taxable</th>
                            <td>{{ $item->Item->Taxable ? 'Yes' : 'No' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-lg-6 rounded-sm">
                    {!! Form::open(['id' => 'register-with-ura','class'=>'needs-validation', 'route' => ['quickbooks.register-product', 'id' => $item->Item->Id, 'redo' => $redo]]) !!}
                    <table class="table">
                        <tr class="info">
                            <td colspan="2">
                                <div class="bg-info p-2">
                                    <p class="text-danger">Fields marked with an asterisk (*) are Required</p>
                                    <p>Please fill in the form below to register {{ $item->Item->Name }} with EFRIS</p>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td style="width:50%;">
                                {!! Form::select('efris[currency]', collect($masterdata->data->currencyType)->pluck('name', 'value')->prepend('Select Currency', ''), null, ['class' => 'form-select select2']) !!}
                            </td>
                            <td>
                                {!! Form::select('efris[unitOfMeasure]', collect($masterdata->data->rateUnit)->pluck('name', 'value')->prepend('Select Unit of Measure', ''), null, ['class' => 'form-select select2']) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                {!! Form::text('efris[commodityCategoryId]', old('efris.commodityCategoryId'), ['class' => 'form-control', 'placeholder' => 'UNSPSC', 'required' => 'true']) !!}
                            </td>

                            <td>
                                {!! Form::select('efris[havePieceUnit]', ['101' => 'Yes', '102' => 'No'], null,['class' => 'form-select select2','id'=>'efrisitem-hasopeningstock', 'onChange'=>'handlePieceUnit()', 'placeholder' => 'Does item have other units of measure?']) !!}
                            </td>
                        </tr>
                        <tr class="peice_unit_options">
                            <td>
                                {!! Form::select('efris[pieceMeasureUnit]', collect($masterdata->data->rateUnit)->pluck('name', 'value')->prepend('Select Unit of Measure', ''),null, ['class' => 'form-select select2']) !!}
                                <div class="invalid-feedback">Please enter other unit of measure</div>
                            </td>
                            <td>
                                {!! Form::text('efris[pieceUnitPrice]', null, ['class' => 'form-control field-alternative-measure-unit', 'placeholder' => 'Alternative Measure Unit Price']) !!}
                                <div class="invalid-feedback">Please enter alternative measure unit</div>
                            </td>
                        </tr>
                        <tr class="peice_unit_options">
                            <td>
                                <div id="pieceScaledValueId">
                                    {!! Form::text('efris[pieceScaledValue]', null, ['class' => 'form-control field-piece-scaled-value','placeholder'=>'piece scaled value']) !!}
                                    <div class="invalid-feedback">Please enter piece scaled value</div>
                                </div>
                            </td>
                            <td>
                                <div id="pieceScaledValueId">
                                    {!! Form::text('efris[packageScaledValue]', null, ['class' => 'form-control field-package-scaled-value','placeholder'=>'package scaled value']) !!}
                                    <div class="invalid-feedback">Please enter package scaled value</div>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                {!! Form::select('efris[item_tax_rule]', [
                                    'URA' => 'URA Tax Rules',
                                    'STANDARD' => 'Standard Rated (18% VAT)',
                                    'ZERORATED' => 'Zero Rated',
                                    'EXEMPT' => 'Exempt'
                                ], null, ['class' => 'form-select select2', 'placeholder' => 'Specific Tax Calculation if URA allows multiple options']) !!}
                            </td>
                            <td>
                                {!! Form::select('efris[haveOtherUnit]', ['101' => 'Yes', '102' => 'No'], null, ['class' => 'form-select select2','id'=>'efrisitem-haveOtherUnit', 'onChange'=>'handleHaveOtherUnit()', 'placeholder' => 'Does item have other units of measure?']) !!}
                            </td>
                        </tr>
                        <tr class="other_unit_options">
                            <td>
                                {!! Form::select('efris[otherUnit]', collect($masterdata->data->rateUnit)->pluck('name', 'value')->prepend('Select Unit of Measure', ''), null, ['class' => 'field-other-unit form-select select2']) !!}
                            </td>
                            <td class="other_unit_options">
                                {!! Form::text('efris[otherPrice]', null, ['class' => 'form-control field-other-price ', 'placeholder' => 'Other Unit Price ']) !!}
                                <div class="invalid-feedback">Please enter other price value</div>
                            </td>
                        </tr>
                        <tr class="other_unit_options">
                            <td>
                                {!! Form::text('efris[otherScaled]', null, ['class' => 'form-control field-other-scaled','placeholder'=>'other scaled value']) !!}
                                <div class="invalid-feedback">Please enter other scaled value</div>
                            </td>
                            <td>
                                {!! Form::text('efris[packageScaled]', null, ['class' => 'form-control field-other-packagescaled','placeholder'=>'other package scaled value']) !!}
                                <div class="invalid-feedback">Please enter other package scaled value</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                {!! Form::select('efris[haveExciseTax]', ['101' => 'Yes', '102' => 'No'], null,['class' => 'form-select select2','id'=>'efrisitem-hasexcisetax','onChange'=>'handleHaveExciseTax()','placeholder' => 'Does item attract excise duty?']) !!}
                            </td>
                            <td class="excise_tax_options">
                                {!! Form::text('efris[exciseDutyCode]', null,['class' => 'form-control field-efrisitem-has-excisetax', 'id'=>'efrisitem-has-excisetax' ,'placeholder'=>'Excise duty code']) !!}
                                <div class="invalid-feedback">Please enter an excise duty tax value</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                {!! Form::hidden('efris[id]') !!}
                                {!! Form::hidden('efris[stockStatus]', 0) !!}
                                {!! Form::hidden('efris[created_at]', Carbon::now()) !!}
                                {!! Form::hidden('efris[itemCode]', $item->Item->Sku) !!}
                                {!! Form::hidden('isRegisteredInEfris', ($efItem && $redo == "no") ? 'yes' : 'no') !!}
                                {!! Form::submit('Submit', ['class' => 'btn btn-success btn-block', 'name' => 'product-button']) !!}
                            </td>
                        </tr>
                    </table>
                    {!! Form::close() !!}

                </div>
            </div>
            </div>

    <!-- END ROW -->
@endsection

@section('scripts')
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('.select2-single').select2();
        });
        $(document).ready(function () {
            // Hide piece unit options
            $('.peice_unit_options').hide();
            // Hide opening stock Options by default...
            $('.opening_stock_options').hide();
            // Hide other unit options
            $('.other_unit_options').hide();
            // Hide Excise Tax options
            $('.excise_tax_options').hide();
        });

        function handlePieceUnit() {
            // console.log('test',$('#efrisitem-hasopeningstock').val())
            if ($('#efrisitem-hasopeningstock').val() === '101') {
                $('.field-piece-scaled-value').addClass('is-invalid');
                // $('#efrisitem-stockin_quantity').prop("disabled", false);
                $('.field-package-scaled-value').addClass('is-invalid');
                $('.field-alternative-measure-unit').addClass('is-invalid');
                $('.peice_unit_options').show();
            } else {
                $('#efrisitem-stockin_quantity').prop("disabled", true);
                $('.field-efrisitem-stockin_quantity').removeClass('required');
                $('.field-efrisitem-stockin_price').removeClass('required');
                $('.field-efrisitem-stockin_supplier').removeClass('required');
                $('.field-efrisitem-stockin_measureunit').removeClass('required');
                $('.field-efrisitem-stockin_date').removeClass('required');
                $('.opening_stock_options').hide();
            }
        }


        // have other unit function
        function handleHaveOtherUnit() {
            if ($('#efrisitem-haveOtherUnit').val() === '101') {
                $('.field-other-unit').addClass('is-invalid');
                $('#efrisitem-stockin_quantity').prop("disabled", false);
                $('.field-other-scaled').addClass('is-invalid');
                $('.field-other-price').addClass('is-invalid');
                $('.field-other-packagescaled').addClass('is-invalid');
                $('.other_unit_options').show();
            } else {
                $('#efrisitem-stockin_quantity').prop("disabled", true);
                $('.field-other-scaled').removeClass('required');
                $('.field-other-unit').removeClass('required');
                $('.field-other-scaled').removeClass('required');
                $('.field-other-price').removeClass('required');
                $('.field-other-packagescaled').removeClass('required');
                $('.other_unit_options').hide();
            }
        }


        // for conditional rendering of the have excise tax field
        function handleHaveExciseTax() {
            if ($('#efrisitem-hasexcisetax').val() === '101') {
                $('.field-efrisitem-has-excisetax').addClass('is-invalid');
                $('#efrisitem-has-excisetax').prop("disabled", false);
                $('.excise_tax_options').show();
            } else {
                $('#efrisitem-has-excisetax').prop("disabled", true);
                $('.field-efrisitem-has-excisetax').removeClass('is-invalid');
                $('.excise_tax_options').hide();
            }
        }
    </script>
@endsection
