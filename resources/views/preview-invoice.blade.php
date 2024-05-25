<html>
<head>
    <title>Invoice Preview</title>
</head>
<style>
    @font-face {
        font-family: SourceSansPro;
        src: url(SourceSansPro-Regular.ttf);
    }
    body{
        font-family: 'Roboto Condensed', sans-serif;
    }

    .company{
        display: flex;
        flex-direction: row;
        flex: fit-content;
    }

    .m-0{
        margin: 0px;
    }
    .p-0{
        padding: 0px;
    }
    .pt-5{
        padding-top:5px;
    }
    .mt-10{
        margin-top:10px;
    }
    .text-center{
        text-align:center !important;
    }
    .w-100{
        width: 100%;
    }
    .w-50{
        width:50%;
    }
    .w-85{
        width:85%;
    }
    .w-15{
        width:15%;
    }
    .logo img{
        width:200px;
        height:60px;
    }
    .gray-color{
        color:#5D5D5D;
    }
    .text-bold{
        font-weight: bold;
    }
    .border{
        border:1px solid black;
    }
    table tr,th,td{
        border: 1px solid #d2d2d2;
        border-collapse:collapse;
        padding:7px 8px;
    }
    table tr th{
        background: #F4F4F4;
        font-size:15px;
    }
    table tr td{
        font-size:13px;
    }
    table{
        border-collapse:collapse;
    }

    header {
        padding: 10px 0;
        margin-bottom: 5px;
        border-bottom: 2px solid #AAAAAA;
        display: flex;
        justify-content: space-between;
        flex-direction: row;
    }

    .table-title {
        margin-bottom: 20px;
        border-bottom: 1px solid #AAAAAA;
    }
    .box-text p{
        line-height:10px;
    }
    .float-left{
        float:left;
    }
    .total-part{
        font-size:16px;
        line-height:12px;
    }
    .total-right p{
        padding-right:20px;
    }
</style>
<body>
<div class="head-title">
    <h1 class="text-center m-0 p-0">Fiscal Invoice</h1>
</div>

<header>
{{--    <div class="add-detail mt-10">--}}
        <div class="w-50 float-left mt-10">

            <p>
                <b>{!! $doc->data->sellerDetails->legalName !!}</b><br>
                <b>Address</b>: <span class="text-xs font-thin"> {{ $doc->data->sellerDetails->address }}</span><br>
                <b>Tin</b>: <span class="text-xs font-thin"> {{ $doc->data->sellerDetails->tin }}</span><br>
                <b>Tel</b>: <span class="text-xs font-thin">{{@$doc->data->sellerDetails->linePhone}}</span><br>
                <b>Email</b>: <span class="text-xs font-thin">{{ $doc->data->sellerDetails->emailAddress }}</span><br>
                <b>Currency</b>: <span class="text-xs font-thin">{{ $doc->data->basicInformation->currency}}</span>
            </p>
        </div>
        <div class="w-50 float-left logo mt-10">
            <p>
                <b>FDN</b>: <span class="text-xs font-thin"> {{ $doc->data->basicInformation->invoiceNo }}</span><br>
                <b>Verification Code</b>: <span class="text-xs font-thin">{{$doc->data->basicInformation->antifakeCode}}</span><br>
                <b>Issue Date</b>: <span class="text-xs font-thin">{{ $doc->data->basicInformation->issuedDate }}</span><br>
                <b>Served by</b>: <span class="text-xs font-thin">{{ $doc->data->basicInformation->operator  }}</span>
            </p>
        </div>
        <div style="clear: both;"></div>
{{--    </div>--}}
</header>



<div class="company" style="">
    <div id="company">
        <p>
            @if (property_exists($doc->data->buyerDetails, 'buyerBusinessName'))
                <b>Billed To</b>: <span class="text-xs font-thin">
                    {{ $doc->data->buyerDetails->buyerBusinessName }}</span><br>
            @endif
            <b>Email</b>: <span class="text-xs font-thin">{{$doc->data->buyerDetails->buyerEmail ?? ''}}</span><br>

            @if(property_exists($doc->data->buyerDetails, 'buyerTin'))
                <b>Tin</b>: <span class="text-xs font-thin">{{ $doc->data->buyerDetails->buyerTin  }}</span>
            @endif
        </p>
    </div>


</div>

<div class="table-section bill-tbl w-100 mt-10">
    <h3 class="table-title">Goods and Services</h3>
    <table class="table w-100 mt-10">
        <tr>
            <!-- <th class="no">#</th> -->
            <th class="desc">Type</th>
            <th class="unitj">Description</th>
            <th class="qty">Qty</th>
            <th class="qty">Unit Price</th>
            <th class="">TOTAL</th>
        </tr>
        @foreach ($doc->data->goodsDetails as $item)
            <tr align="center">
                <td>{{ $item->item }}</td>
                <td class="text-sm">{{ @$item->goodsCategoryName }}</td>
                <td class="text-center">{{ @$item->qty }}</td>
                <td class="text-right">
                    {{

                @$item->unitPrice,
                $doc->data->basicInformation->currency

        }}
                </td>
                <td class="text-right">
                    {{

                $item->total,
                $doc->data->basicInformation->currency

        }}
                </td>
            </tr>
        @endforeach

    </table>
</div>

<div class="table-section bill-tbl w-100 mt-10">
    <h3 class="table-title">Tax Details</h3>
    <table class="table w-100 mt-10">
        <tr>
            <!-- <th class="no">#</th> -->
            <th class="desc">Tax Category</th>
            <th class="unitj">Tax Rate</th>
            <th class="qty">Net Amount</th>
            <th class="qty">Tax Amount</th>
            <th class="">Gross Amount</th>
        </tr>
        @foreach($doc->data->taxDetails as $index => $tax)
            <tr>
                <td>{{ $tax->taxCategory }}</td>
                <td>{{ $tax->taxRateName }}</td>
                <td>{{ number_format($tax->netAmount,2) }}

                </td>
                <td class="text-center">{{ number_format($tax->taxAmount,2) }}</td>
                <td class="text-right">{{ number_format($tax->grossAmount,2) }}</td>
            </tr>
        @endforeach
    </table>
</div>


<div class="table-section bill-tbl w-100 mt-10">
    <h3 class="table-title">Invoice Summary</h3>
    <table class="table w-100 mt-10">

        <tr>
            <td colspan="2" rowspan="3" class="valign-middle">
                <div class="invoice-notes">
                    <label class="az-content-label text-base">Remarks</label>
                        @if (property_exists($doc->data->summary,'remarks'))
                             <p class="text-xs">
                        {!! $doc->data->summary->remarks !!}
                    </p>
                            @endif

                </div>

            </td>

            <td class="tx-right">Net Amount</td>

            <td colspan="2" class="tx-right">
                {{ number_format($doc->data->summary->netAmount,2) }}
            </td>
        </tr>

        <tr>
            <td class="tx-right">Tax Amount</td>
            <td colspan="2" class="tx-right">{{ number_format($doc->data->summary->taxAmount,2) }}</td>
        </tr>

        <tr>
            <td class="tx-right uppercase font-bold tx-inverse">Total Due</td>
            <td colspan="2" class="tx-right">
                <h4 class="text-black font-bold">{{ number_format($doc->data->summary->grossAmount,2) }}</h4>
              @php
                $currency =property_exists($doc->data->taxDetails[0],'exciseCurrency') ?$doc->data->taxDetails[0]->exciseCurrency:'UGX';
              @endphp
              <p>{{ ($doc->data->summary->grossAmount>0)? numberConvert($doc->data->summary->grossAmount,$currency):null }}</p>
            </td>
        </tr>

    </table>

    <div style="position: relative">
        <img src="{{$qrcode['base64']}}" alt="" srcset="" width="85">
    </div>
</div>
</body>
</html>
