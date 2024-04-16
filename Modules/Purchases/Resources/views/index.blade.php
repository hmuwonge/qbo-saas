@extends('layouts.main')

@section('title', 'Quickbooks Purchases')

@section('styles')
  <style>
    .loader {
      display: none; /* Initially hide the loader */
    }
  </style>
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Purchases From QuickBooks</h3>
                </div>
                {{-- <div id="responseMessage"></div> --}}
{{--                <div class="alert alert-success" role="alert" id="responseMessage">--}}
{{--                </div>--}}
                <div class="card-body">
                    <div class="d-flex row form-group my-2">
                        <div class="col-lg-3">
                            {{ Form::select('stock_in_type', $stockInTypes, null, [
                                'class' => 'form-control-sm form-control',
                                'id' => 'stockin_type',
                                'style' => 'width:250px;',
                                'onChange' => 'updatePurchaseStockInType()',
                                'placeholder' => 'Update Stock-in type...',
                            ]) }}
                        </div>


                        <div class="col-lg-2">
                            <a type="button" id="validateButton" class="btn btn-sm btn-primary"
                                href="{{ route('validate.bill') }}">Sync Purchase From QBO</a>
                        </div>

                    </div>


                    {{-- <p>Found <code class="highlighter-rouge">{{ $data['total'] }}</code>tbetween <code
                            class="highlighter-rouge">{{ $data['startdate'] }} and {{ $data['enddate'] }}</code>.</p> --}}
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap text-md-nowrap  table-striped mb-0">
                            <thead class="bg-secondary">
                                <tr>
                                    <th class="">#</th>
                                    <th scope="col" class="">
                                        Transaction Date
                                    </th>
                                    <th scope="col" class=" ">
                                        Stock-In Type
                                    </th>
                                    <th scope="col" class="">
                                        Ref. Number
                                    </th>
                                    <th scope="col" class="">
                                        Vendor
                                    </th>
                                    <th scope="col" class="">
                                        Currency
                                    </th>

                                    <th scope="col" class="">
                                        Amount
                                    </th>

                                    <th scope="col" class="">
                                        Options
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-black text-sm py-0.5 overflow-y-auto">
                                @foreach ($purchases as $index => $purchase)
                                  @if (array_key_exists($purchase['Id'], $dbPurchases->toArray()))
                                  <tr class="hover:bg-gray-100 hover:cursor-pointer">
                                    <td class="px-6 py-2">
                                      <input type="checkbox" id="row{{ $purchase['Id'] }}" class="itemCheckbox"
                                             data-id="{{ $purchase['Id'] }}">
                                    </td>
                                    <td class="px-6 py-2">{{ $purchase['TxnDate'] }}</td>
                                    <td class="px-6 py-2">
                                      @if (isset($dbPurchases[$purchase['Id']]['stockInType']))
                                        {{ $stockInTypes[$dbPurchases[$purchase['Id']]['stockInType']] }}
                                      @endif
                                    </td>
                                    <td class="px-6 py-2">{{ $purchase['DocNumber'] ?? '(not set)' }}</td>
                                    <td class="px-6 py-2">{{ $purchase['VendorRef']['name'] }}</td>
                                    <td class="px-6 py-2">{{ $purchase['CurrencyRef']['value'] }}</td>
                                    <td class="px-6 py-2">{{ number_format($purchase['TotalAmt'], 2) }}</td>
                                    <td class="px-6 py-2">

                                      @if ($dbPurchases[$purchase['Id']]['uraSyncStatus'] === 1)
                                        <button class="btn btn-sm btn-success text-white w-40 rounded py-1.5 px-2">
                                          Stocked</button>
                                      @else
{{--                                        <button type="button" onclick="handleAddStock({{ $dbPurchases[$purchase['Id']]['id']}})"--}}
{{--                                                class="btn btn-sm btn-primary bg-violet-950">--}}
{{--                                          Add Stock--}}
{{--                                          <i class="fa fa-spinner fa-spin ms-2"></i>--}}
{{--                                        </button>--}}

                                        <button type="button" onclick="handleAddStock({{ $index }},{{ $dbPurchases[$purchase['Id']]['id']}})"
                                                class="btn btn-sm btn-primary bg-violet-950">
                                          <span class="button-text">Add Stock</span>
{{--                                          <span class="loader">Loading...</span>--}}
                                          <i class="fa fa-spinner fa-spin ms-2 loader"></i>
                                        </button>
                                      @endif
                                    </td>
                                  </tr>

                                  @else
                                 @php
                                 $item_id = $purchase['Id'];
                                   @endphp
                                      <div class="alert alert-danger alert-dismissible " role="alert">
                                      Found Unmatched purchase ID that is not synced with your database, please click the sync button to remove this warning: {{$item_id}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                      </button>
                                      </div>

                                  @endif

                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    @if ($purchases->hasPages())
                        <div class="pagination-wrapper my-1">
                            <nav aria-label="Page navigation">
                                {{ $purchases->links() }}
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{--    </div> --}}
    <!-- END ROW -->
@endsection

@section('scripts')

    <script>
        function getCsrfToken() {
            return document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        const csrfToken = getCsrfToken();
        // Initialize an array to hold selected IDs
        let selectedIds = [];

        // Function to add selected ID to the array
        function getSelectedRows() {
            const checkboxes = document.querySelectorAll('input.itemCheckbox:checked');
            return Array.from(checkboxes).map(function(checkbox) {
                // Extract the ID from the checkbox ID
                const checkboxId = checkbox.id;
                 // Remove 'row' prefix
              return checkboxId.replace('row', '');
            });
        }

        function updatePurchaseStockInType() {
            const keys = getSelectedRows();
            {{--const tin = {{ $tin }};--}}

            if (keys.length > 0) {
                const purchase_mod = {
                    purchases: {
                        id: keys,
                        stockIn: $('#stockin_type').val()
                    }
                };
                // Send Request
                fetch(route('purchase.stockUpdate'), {
                    method: 'POST',
                    headers: {
                        Accept: '*/*',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        // 'tin': tin,
                        'Connection': 'keep-alive'
                    },
                    body: JSON.stringify(purchase_mod)
                }).then(response => {
                    return response.json()
                }).then(response => {

                    const responseMessage = document.getElementById('responseMessage');

                    if (response.status === true) {
                      Swal.fire({
                        // title: 'Are you sure?',
                        text: response.payload,
                        icon: 'success',
                      })
                        // You can also choose to hide the message after a few seconds if needed
                        setTimeout(function() {
                            window.location.reload();
                        }, 3000);
                    } else {
                      Swal.fire({
                        title: 'Something Wrong occured',
                        text: response.payload,
                        icon: 'warning',
                      })
                    }
                });
            }
        }

        // Selected Purchases
        function getSelectedPurchases(_keys) {
            let selected_values = [];
            for (let i = 0; i < _keys.length; i++) {
                const _value = _keys[i];
                selected_values.push(_value);
                return selected_values;
            }
        }

        function handleAddStock(index,id){
          const button = document.getElementsByClassName('btn')[index+1];
          const loader = button.querySelector('.loader');
          const buttonText = button.querySelector('.button-text');

          // Show loader and hide button text
          loader.style.display = 'inline-block';
          buttonText.style.display = 'none';
          // alert('add')
          fetch(route('quickbooks.fiscalise-increase-stock',id), {
            method: 'GET',
            headers: {
              Accept: '*/*',
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Connection': 'keep-alive'
            },
          }).then(response => {
            return response.json()
          }).then(response => {

            // Hide loader and show button text
            loader.style.display = 'none';
            buttonText.style.display = 'inline-block';

            if (response.status === true) {
              Swal.fire({
                // title: 'Are you sure?',
                text: response.payload,
                icon: 'success',
              })
              // You can also choose to hide the message after a few seconds if needed
              setTimeout(function() {
                window.location.reload();
              }, 2000);
            } else {
              Swal.fire({
                title: 'Something Wrong occured',
                text: response.payload,
                icon: 'warning',
              })
            }
          });
        }

    </script>
@endsection
