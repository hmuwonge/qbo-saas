@extends('layouts.main')

@section('styles')
  <style>
    .hide {
      display: none;
    }
  </style>
@endsection

@section('title', 'Credit Memos')

@section('content')
  <!-- PAGE HEADER -->
  <div class="page-header">
    <div class="page-block">
      <div class="row align-items-center">
        <div class="col-md-12">

          <ul class="breadcrumb">
            <li class="breadcrumb-item "><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('Quickbooks') }}</li>
            <li class="breadcrumb-item active">{{ __('Credit Memos') }}</li>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">All Quickbooks Credit Notes</h3>
                    <a href="{{ route('sync.creditnotes') }}" class="btn btn-sm btn-primary">Validate Credit
                        memos</a>
                </div>

                <div class="d-inline-flex gap-1">
                    {!! Form::open([
                        'route' => 'quickbooks.link-credit-note',
                        'method' => 'Post',
                        'class' => 'form-horizontal  row g-3  d-none',
                        'id' => 'credit_memo_form',
                    ]) !!}
                    {{ Form::hidden('crednoteType', 'CN') }}
                    {{ Form::hidden('unlink', null, ['id' => 'isUnlink']) }}
                    <div class="col-auto">
                        <label>ID</label><br />
                        {{ Form::number('creditnoteId', null, ['class' => 'form-control form-control-sm', 'id' => 'creditnote_id', 'style' => 'width:250px;']) }}
                    </div>
                    <div class="col-auto">
                        <label>Credit Note Ref.</label><br />
                        {{ Form::text('creditnoteRef', null, ['class' => 'form-control form-control-sm', 'id' => 'creditnote_ref', 'style' => 'width:250px;']) }}
                    </div>
                    <div class="col-auto ">
                        <label>Original Invoice FDN</label><br />
                        <div class="input-group">
                            {{ Form::text('invoiceFdn', null, ['class' => 'form-control form-control-sm', 'id' => 'invoice_fdn', 'style' => 'width:250px;']) }}
                            <button type="submit" class="btn btn-primary btn-sm">Link Credit
                                Note</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="card-body">




          <div class="table-responsive">
            <table class="table table-bordered table-striped  mb-0">
              <thead class="bg-secondary text-white">
              <tr>
                <th scope="col" class="">Ref. Number</th>
                <th scope="col" class="">
                  Registered on
                </th>
                <th scope="col" class="">
                  Original Invoice
                </th>
                <th scope="col" class=>
                  Customer Details
                </th>
                <th scope="col" class="">Total Amount</th>
                <th scope="col" class="">Fiscal Status</th>
                <th scope="col" class="">Actions</th>
              </tr>
              </thead>
              <tbody>
              @forelse($credits as $creditMemo)
                <tr class="hover:bg-gray-100 text-base text-black ">
                  <td class="">
                    <span class="font-weight-bold">{{ $creditMemo['DocNumber'] }}</span>
                  </td>
                  <td class="">
                    {{ $creditMemo['MetaData']['CreateTime'] }}
                  </td>
                  <td class="">
                    @if (isset($invoiceStatus[$creditMemo['Id']]))

                      @if ($invoiceStatus[$creditMemo['Id']]['fiscalStatus'] == 0 && isset($invoiceStatus[$creditMemo['Id']]['invoice_fdn']))
                        {{ $invoiceStatus[$creditMemo['Id']]['invoice_fdn'] }}
                        <br>
                        <button class="btn btn-xs btn-secondary font-bold"
                                onclick="handleUnLinkInvoice({{ $creditMemo['Id'] }},'{{ $creditMemo['DocNumber'] }}',{{ $invoiceStatus[$creditMemo['Id']]['invoice_fdn'] }})">
                          Unlink Invoice
                        </button>
                      @elseif($invoiceStatus[$creditMemo['Id']]['fiscalStatus'] == 1)
                        <span class="text-dark font-weight-600 fs-4 fw-bold"> {{ $invoiceStatus[$creditMemo['Id']]['invoice_fdn'] }}</span>
                      @else
                        <span class="btn btn-xs btn-primary"
                              onclick="assignFormFields({{ $creditMemo['Id'] }},'{{ $creditMemo['DocNumber'] }}')">
                                                        Link Invoice
                                                    </span>
                      @endif
                    @else
                      <span class="badge bg-warning">No yet Synced</span>
                    @endif
                  </td>
                  <td class="">
                    {{ $creditMemo['CustomerRef']['name'] }}
                  </td>
                  <td class="">
                    {{ number_format($creditMemo['TotalAmt']), $creditMemo['CurrencyRef']['value'] }}
                  </td>
                  <td class=" whitespace-nowrap">
                    @if (isset($invoiceStatus[$creditMemo['Id']]))
                      @if ($invoiceStatus[$creditMemo['Id']]['fiscalStatus'] == 1)
                        <span class="badge bg-success">Fiscalised</span>
                      @else
                        <span class="badge bg-danger">Not Fiscalised</span>
                      @endif
                    @else
                      {{--                                                <span class="badge bg-danger">Not linked to --}}
                      {{--                                                    invoice</span> --}}
                    @endif
                  </td>
                  <td class=" whitespace-nowrap">
                    @if (isset($invoiceStatus[$creditMemo['Id']]))
                      @if ($invoiceStatus[$creditMemo['Id']]['fiscalStatus'] == 0 && isset($invoiceStatus[$creditMemo['Id']]['invoice_fdn']))
                        <a href="{{ route('creditnote.fiscalise', ['id' => $invoiceStatus[$creditMemo['Id']]]) }}"
                           class="btn btn-sm btn-primary text-white rounded-md text-base">
                          Fiscalise CreditNote
                        </a>
                      @elseif($invoiceStatus[$creditMemo['Id']]['fiscalStatus'] == 1)
                        <span class="badge bg-success">Fiscalised</span>
                      @elseif(!isset($invoiceStatus[$creditMemo['Id']]['invoice_fdn']))
                        <span class="badge bg-warning">Not yet Linked</span>
                      @endif
                    @endif
                    {{--                                              <span --}}
                    {{--                                                class="badge bg-warning">Not Yet Synced</span> --}}
                  </td>
                </tr>

              @empty
                <tr>
                  <p class="mt-3 text-black font-bold text-lg my-10">
                    No Credit Notes Data Available
                  </p>
                </tr>
              @endforelse

              </tbody>
            </table>
          </div>

          @if ($credits->hasPages())
            <div class="pagination-wrapper my-1">
              <nav aria-label="Page navigation">
                {{ $credits->links() }}
              </nav>
            </div>
          @endif
        </div>
      </div>
    </div>
    <!-- END ROW -->
    @endsection

    @section('scripts')
      <!-- INDEX JS -->
      <script>
        function assignFormFields(id, ref) {
          $("#credit_memo_form").removeClass("d-none");
          $("#creditnote_id").val(id);
          $("#creditnote_ref").val(ref.toString());
          $("#invoice_fdn").val('');
          return true;
        }

        function handleUnLinkInvoice(id, ref, fdn) {
          $("#credit_memo_form").removeClass("d-none");
          $("#creditnote_id").val(id);
          $("#creditnote_ref").val(ref.toString());
          $("#invoice_fdn").val(fdn.toString());
          $("#isUnlink").val('yes');
          return true;
        }
      </script>
@endsection
