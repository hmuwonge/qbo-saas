<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FISCAL INVOICE</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
        @font-face {
            font-family: SourceSansPro;
            src: url(SourceSansPro-Regular.ttf);
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-family: SourceSansPro;
        }

        header {
            padding: 10px 0;
            margin-bottom: 10px;
            border-bottom: 1px solid #AAAAAA;
            display: flex;
            justify-content: space-between;
            flex-direction: row;
        }

        .table-title {
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        table tr .no-border {
            border: none;
        }

        #logo {
            float: left;
            margin-top: 8px;
        }

        #seller-logo {
            float: right;
            margin-top: 8px;
        }

        #seller-logo img {
            height: 70px;
        }

        #logo img {
            height: 70px;
        }

        #company {
            float: right;
            text-align: left;
        }


        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {
            float: right;
            text-align: left;
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 1.4em;
            line-height: 1em;
            font-weight: normal;
            margin: 0 0 10px 0;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 10px;
            /* background: #EEEEEE; */
            border: 2px solid #e2dddd;
            text-align: center;
            /* border-bottom: 1px solid #FFFFFF; */
        }

        thead th {
            padding: 10px;
            background: #EEEEEE;
            text-align: center;
            /* border-bottom: 1px solid #FFFFFF; */
        }

        table th {
            white-space: nowrap;
            font-weight: normal;
        }

        table td {
            text-align: right;
        }

        table td h3 {
            color: #57B223;
            font-size: 1.2em;
            font-weight: normal;
            margin: 0 0 0.2em 0;
        }

        table .no {
            /* color: #FFFFFF; */
            font-size: 1.6em;
            background: #57B223;
        }

        table .desc {
            text-align: left;
        }

        table .unit {
            background: #DDDDDD;
        }

        table .qty {}

        table .total {
            background: #57B223;
            color: #FFFFFF;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }

        /* table tbody tr:last-child td {
  border: none;
} */

        table tfoot td {
            padding: 10px 20px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-top: 1px solid #AAAAAA;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr:last-child td {
            color: #57B223;
            font-size: 1.4em;
            border-top: 1px solid #57B223;

        }

        table tfoot tr td:first-child {
            border: none;
        }

        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }



        html {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
            line-height: 1.5
        }
    </style>

    <!-- <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style> -->
</head>

<body>
    <header class="clearfix">
        <div id="logo">
            <img src="logo.png">
        </div>

        <div id="logo">
            <h3 style="font-weight:700px; font-size:2rem">FISCAL INVOICE</h3>
        </div>

        <div id="seller-logo">
            <img src="logo.png">
        </div>
        </div>
    </header>

    <header class="clearfix">

        <div id="company">
            <h2 class="name">{{
          $doc->data->sellerDetails->businessName
      }}
            </h2>
            <p>
                <b>Address</b>: <span class="text-xs font-thin"> {{ $doc->data->sellerDetails->address }}</span><br>
                <b>Tel</b>: <span class="text-xs font-thin">{{$doc->data->sellerDetails->linePhone}}</span><br>
                <b>Email</b>: <span class="text-xs font-thin">{{ $doc->data->sellerDetails->emailAddress }}</span>
            </p>
        </div>
        <div></div>
        <div id="invoice">
            <h1 class="name">Invoice Information</h1>

            <div class="invoice-info-row">
                <span>Fiscal Document Number:</span>
                <span class="text-xs font-thin ml-1">{{ $doc->data->basicInformation->invoiceNo }}</span>
            </div>
            <div class="invoice-info-row">
                <span>Verification Code</span>
                <span class="text-xs font-thin ml-1">{{ $doc->data->basicInformation->antifakeCode }}</span>
            </div>
            <div class="invoice-info-row">
                <span>Issue Date:</span>
                <span class="text-xs font-thin ml-1">{{ $doc->data->basicInformation->issuedDate}}</span>
            </div>
            <div class="invoice-info-row">
                <span>Invoice Currency:</span>
                <span class="text-xs font-thin ml-1">{{ $doc->data->basicInformation->currency }}</span>
            </div>
            <div class="invoice-info-row">
                <span>Served by:</span>
                <span class="text-xs font-thin ml-1">{{ $doc->data->basicInformation->operator }}</span>
            </div>
        </div>
        <!-- </div> -->
    </header>
    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <div class="to">Bill TO:</div>
                <div class="billed-to">
                    <h4>{{ $doc->data->buyerDetails->buyerBusinessName }}</h4>
                    @if($doc->data->buyerDetails->buyerTin )
                    <p v-if="doc.buyerDetails?.buyerTin">TIN:<span class="text-xs font-thin">
                            {{ $doc->data->buyerDetails->buyerTin }}</span></p>
                    @endIf
                </div>
            </div>

        </div>


        <!-- goods and services section -->
        <div>
            <h3 class="table-title">Goods and Services</h3>
            <table border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <!-- <th class="no">#</th> -->
                        <th class="desc">Type</th>
                        <th class="unitj">Description</th>
                        <th class="qty">Qty</th>
                        <th class="qty">Unit Price</th>
                        <th class="">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- <tr>
              <td class="desc">Creating a recognizable design solution based on the company's existing visual identity</td>
              <td class="unith">$40.00</td>
              <td class="qty">30</td>
              <td class="">$1,200.00</td>
              <td class="">$3,200.00</td>
  
            </tr> -->
                    @foreach ($doc->data->goodsDetails as $item)
                    <tr>
                        <td>{{ $item->item }}</td>
                        <td class="text-sm">{{ $item->goodsCategoryName }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-right">
                            {{

                        $item->unitPrice,
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
                </tbody>

            </table>
        </div>

        <div>
            <h3 class="table-title">Tax Details</h3>
            <table border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <!-- <th class="no">#</th> -->
                        <th class="desc">Tax Category</th>
                        <th class="unitj">Tax Rate</th>
                        <th class="qty">Net Amount</th>
                        <th class="qty">Tax Amount</th>
                        <th class="">Gross Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- <tr>
              <td class="desc">Creating a recognizable</td>
              <td class="unith">$40.00</td>
              <td class="qty">30</td>
              <td class="">$1,200.00</td>
              <td class="">$3,200.00</td>
  
            </tr> -->
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
                    <tr class="no-border mt-3 font-light" style="border:none;">
                        <td colspan="" class="border mt-3 font-light">
                            <h4>Invoice Summary</h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" rowspan="3" class="valign-middle">
                            <div class="invoice-notes">
                                <label class="az-content-label text-base">Remarks</label>
                                <p class="text-xs">REmarks will go here</p>
                            </div>
                        </td>
                        <td class="tx-right">Net Amount</td>
                        <td colspan="2" class="tx-right">{{ number_format($doc->data->summary->netAmount,2) }}</td>
                    </tr>

                    <tr>
                        <td class="tx-right">Tax Amount</td>
                        <td colspan="2" class="tx-right">{{ number_format($doc->data->summary->taxAmount,2) }}</td>
                    </tr>

                    <tr>
                        <td class="tx-right uppercase font-bold tx-inverse">Total Due</td>
                        <td colspan="2" class="tx-right">
                            <h4 class="text-black font-bold">{{ number_format($doc->data->summary->grossAmount,2) }}</h4>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div id="notices">
            <!-- qrcode -->
            <hr />
            <p style="text-align: center">
                <img src="{{ $qrcode }}" />
            </p>
        </div>
        <footer>
        Invoice was created on a computer and is valid without the signature and seal.
        </footer>
</body>

</html>