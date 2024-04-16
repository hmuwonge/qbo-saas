@extends('layouts.main')

@section('title', 'Register Opening Stock')

@section('page-style')

@endsection
<style>
    form div.required label.control-label:after{
        content: "*";
        color: red;
    }
</style>

@section('content')
    <div class="page-header d-sm-flex d-block">
        <ol class="breadcrumb mb-sm-0 mb-3">
            <!-- breadcrumb -->
            <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
            <li class="breadcrumb-item" aria-current="page">Quickbooks</li>
            <li class="breadcrumb-item " aria-current="page"> <a href="{{route('goods.all')}}">Goods</a></li>
            <li class="breadcrumb-item active" aria-current="page">Register Opening Stock</li>
        </ol>
    </div>

    <div class="bg-white p-2 rounded-2 my-2 align-items-start d-flex justify-content-between">
        <h2>Register Opening Stock</h2>
        <a class="btn btn-sm btn-gray-dark" href="{{ url()->previous() }}">
            <i class="fa  fa-arrow-circle-o-left"></i>
            <span>Back</span>
        </a>
    </div>
    <div class="bg-white card p-3">
        <div class="row">
            <div class="col-lg-6">
                <table class="table table-striped fw-bolder">
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

            <div class="col-lg-6">
                {!! Form::open(['id' => 'register-with-ura','class'=>'needs-validation', 'route' => ['quickbooks.register-stock.store', 'id' => $item->Item->Id]]) !!}
                <table class="table">
                    <tr class="bg-info opening_stock_options text-dark fs-16 font-weight-600">
                        <th colspan="2">OPENING STOCK DETAILS</th>
                    </tr>
                    <tr class="opening_stock_options">
                        <td>
                            {{ Form::label('stockin_quantity', 'Stockin Quantity') }}
                            {{ Form::text('stockinQuantity', $item->Item->QtyOnHand, ['readonly' => true, 'class' => 'form-control bg-secondary', 'placeholder' => 'Opening Stock Quantity']) }}
                        </td>
                        <td>
                            {{ Form::label('stockin_date', 'Stockin Date') }}
                            {{ Form::date('stockInDate', null, ['class' => 'form-control', 'placeholder' => 'Stock in Date']) }}
                        </td>
                    </tr>

                    <tr class="opening_stock_options ">
                        <td>
                            {{ Form::label('Stockin_Measure_Unit', 'Stockin Measure Unit') }}
                            {!! Form::select('stockinMeasureUnit',collect($measureunit->data->rateUnit)->pluck('name', 'value'), null,['placeholder' => 'Select Unit of Measure','id'=>'select2Basic', 'class' => 'select2 form-select', 'data-allow-clear' => 'true']
      ) !!}
                        </td>
                        <td>
                            {{ Form::label('stockin_price', 'Purchase Cost (Tax Inclusive)') }}
                            {{ Form::text('stockinPrice', null, ['class' => 'form-control']) }}
                        </td>
                    </tr>

                    <tr class="opening_stock_options">
                        <td>
                            {{ Form::label('stockin_supplier', 'Name Of Supplier') }}
                            {{ Form::text('stockInsupplier', null, ['class' => 'form-control', 'placeholder' => 'Name of Supplier', 'label' => 'Name of Supplier']) }}
                        </td>
                        <td>
                            {{ Form::label('stockin_supplier_tin', 'Supplier Tin') }}
                            {{ Form::text('stockinSupplierTin', null, ['class' => 'form-control', 'placeholder' => 'Supplier TIN', 'label' => 'Supplier TIN']) }}
                        </td>
                    </tr>
                    <tr class="opening_stock_options">
                        <td colspan="2">
                            {{ Form::label('stockin_remarks', 'Stockin Remarks') }}
                            {{ Form::textarea('stockInRemarks', null, ['rows' => 8, 'class' => 'form-control', 'placeholder' => 'Remarks', 'label' => 'Remarks']) }}
                        </td>
                    </tr>


                    <!-- Add other form fields as needed -->
                    <tr>
                        <td colspan="2">
                            {{ Form::hidden('id', null) }}
                            {{ Form::hidden('stockStatus', 1) }}
                            {{ Form::hidden('created_at', time()) }}
                            {{ Form::hidden('itemCode', $item->Item->Sku) }}
                            {{ Form::submit('Submit', ['class' => 'btn btn-success btn-block', 'name' => 'contact-button']) }}
                        </td>
                    </tr>
                </table>
                {!! Form::close() !!}

            </div>
        </div>
    </div>

@endsection

@push('javascript')
    <!-- SELECT2 JS -->
    <script src="{{ asset('assets/js/forms-select.js') }}"></script>
 <script>

    // (function()
   $(document).ready(function() {
      $('.select2').select2();
   });
 </script>


@endpush
