{{-- @extends('stockadjustments::layouts.master') --}}

@extends('layouts.main')

@section('title', 'Stock Adjustments')
@section('styles')
  <style>
    .hidden {
      display: none;
    }
  </style>
@endsection
@section('content')
    <!-- PAGE HEADER -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title">
              <h4 class="m-b-10">{{ __('Quickbooks Stock Adjustments') }}</h4>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
              <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Quickbooks') }}</a></li>
              <li class="breadcrumb-item active">{{ __(' Stock Adjustments') }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- END PAGE HEADER -->

    <!-- ROW -->
    <div class="row">

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Stock Adjustments</h3>
                </div>
              <div class="alert alert-success hidden" role="alert" id="responseMessage">
              </div>

              <div class="alert alert-warning hidden" role="alert" id="responseErrorMessage">
              </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between form-group my-2">
                        <div class="col-lg-3">
                            {{ Form::select('reason', $reasons, null, [
                                'class' => 'form-control',
                                'id' => 'stockin_type',
                                'style' => 'width:250px;',
                                'onchange' => 'updateStockInType()',
                                'prompt' => 'Update Stock In Type...',
                            ]) }}
                        </div>
                        <div class="col-lg-2">
                            <a type="button" id="validateButton" class="btn btn-primary"
                                href="{{ route('qbo.stockadjustments.sync') }}">Sync Stock Adjustments</a>
                        </div>

                    </div>

                    <tr class="table-responsive">
                        <table class="table table-bordered table-striped text-nowrap text-md-nowrap rounded  mb-0">
                            <thead class="bg-gray-500 bg-secondary">
                                <tr>
                                    <th class="">#</th>
                                    <th scope="col" class="py-2 px-2">Rer. Number</th>
                                    <th scope="col" class="py-2 px-6">
                                        Transaction Date
                                    </th>
                                    <th scope="col" class="py-2 px-6">Item Name</th>
                                    <th scope="col" class="py-2 px-4">Reason</th>
                                    <th scope="col" class="py-2 px-2">Quantity</th>

                                    <th scope="col" class="py-2 px-6">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $item)
                                    <tr class="px-2">
                                        <td>
                                            <input type="checkbox" id="row{{ $item->transact_id }}" class="itemCheckbox"
                                                data-id="{{ $item->transact_id }}">
                                        </td>
                                        <th scope="row" class="">
                                            {{ $item->transact_id }}
                                        </th>
                                        <td class="">
                                            {{ $item->transact_date }}
                                        </td>
                                        <td class="">
                                            {{ $item->item_name }}
                                        </td>
                                        <td class="">
                                            {{ $item->reason }}
                                        </td>
                                        <td class="text-center">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class=" text-center">
                                            @if ($item->ura_sync_status == 1)
                                                <span class="bg-green-600 rounded text-sm text-white px-2 py-2">Synced
                                                    with  URA</span>
                                            @endif

                                            @if ($item->ura_sync_status == 0 && !is_null($item->adjust_reason))
                                                <a type="button" href="{{ route('stockAdjust.reduce-stock', ['id'=>$item->transact_id,'stock'=>'stock']) }}"
                                                    class="btn btn-sm btn-primary"> Reduce Stock</a>
                                            @endif
                                            @if (!$item->item)
                                                <span class="badge bg-danger me-1 my-1 fw-semibold">Item not
                                                    Registered</span>
                                            @endif

                                            @if (is_null($item->adjust_reason))
                                                <span class="badge bg-danger me-1 my-1 fw-semibold">Reason not
                                                specified</span>
                                            @endif

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <p class="mt-3 text-black font-bold text-lg my-10">
                                            No Data Available
                                        </p>
                                    </tr>
                                @endforelse

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
        </div>
    </div>
    {{--    </div> --}}
    <!-- END ROW -->
@endsection

@push('javascript')
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
            const itemId = checkboxId.replace('row', ''); // Remove 'row' prefix
            return itemId;
          });
        }

        const id = $(this).attr('data-id');

        function updateStockInType() {
            const keys = getSelectedRows();

            if (keys.length > 0) {

                // Get the CSRF token
              if (keys.length > 0) {
                const stockins=
                   {
                    type: $('#stockin_type').val(),
                     transaction_ids: keys,
                     reason: 'test'
                };

                const responseMessage = document.getElementById('responseMessage');
                const responseErrorMessage = document.getElementById('responseErrorMessage');
                try {
                  axios.post("{{route('update.stockInType')}}", stockins, {
                    headers: {
                      'Accept': '*/*',
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': csrfToken,
                      'tin': 7868,
                      'Connection': 'keep-alive'
                    }
                  }).then(response => {

                    const {status, payload}=response.data
                    if (status === 'success') {
                      // Display success message
                      $("#responseMessage").show();

                      responseMessage.innerHTML = 'Success: ' + payload;
                      // You can also choose to hide the message after a few seconds if needed
                      setTimeout(function() {
                        $("#responseMessage").hide();
                        responseMessage.innerHTML = '';
                      }, 3000);
                    } else{
                      // alert(payload)
                      // Display error message
                      $("#responseErrorMessage").show();
                      responseErrorMessage.innerHTML = 'Error: ' + payload;
                    }

                  });
                }catch (e) {

                }finally {
                  setTimeout(function() {
                    $("#responseMessage").hide();
                    $("#responseErrorMessage").hide();
                    responseMessage.innerHTML = '';
                    window.location.reload()
                  }, 3000);
                }

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
        }

        (function () {
          $("#responseMessage").hide();
          $("#responseErrorMessage").hide();
        })()

    </script>
@endpush
