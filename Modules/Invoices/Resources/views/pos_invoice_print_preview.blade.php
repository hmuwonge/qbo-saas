<!DOCTYPE html>
<html lang="en">
<head>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: "PT Sans", sans-serif;
    }

    @page {
      size: 2.8in 11in;
      margin-top: 0cm;
      margin-left: 0cm;
      margin-right: 0cm;
    }

    table {
      width: 100%;
    }

    tr {
      width: 100%;
    }

    table tr td{
      font-weight: 400;
      color: black;
    }

    table tr{
      font-weight: 500;
      color: black;
    }

    h1 {
      text-align: center;
      vertical-align: middle;
    }

    #logo {
      width: 60%;
      text-align: center;
      -webkit-align-content: center;
      align-content: center;
      padding: 5px;
      margin: 2px;
      display: block;
      margin: 0 auto;
    }

    header {
      width: 100%;
      text-align: center;
      -webkit-align-content: center;
      align-content: center;
      vertical-align: middle;
    }

    .items thead {
      text-align: center;
    }

    .center-align {
      text-align: center;
    }

    .bill-details td {
      font-size: 15px;
      font-weight: 500;
    }

    .receipt {
      font-size: medium;
    }

    .items .heading {
      font-size: 12.5px;
      text-transform: uppercase;
      border-top: 1px solid black;
      margin-bottom: 4px;
      border-bottom: 1px solid black;
      vertical-align: middle;
    }

    .items thead tr th:first-child,
    .items tbody tr td:first-child {
      width: 47%;
      min-width: 47%;
      max-width: 47%;
      word-break: break-all;
      text-align: left;
    }

    .items td {
      font-size: 13px;
      text-align: right;
      vertical-align: bottom;
    }

    .price::before {
      content: "\20B9";
      font-family: Arial;
      text-align: right;
    }

    .sum-up {
      text-align: right !important;
    }
    .total {
      font-size: 13px;
      border-top: 1px dashed black !important;
      border-bottom: 1px dashed black !important;
    }
    .total.text,
    .total.price {
      text-align: right;
    }
    .total.price::before {
      content: "\20B9";
    }
    .line {
      border-top: 1px solid black !important;
    }
    .heading.rate {
      width: 20%;
    }
    .heading.amount {
      width: 25%;
    }
    .heading.qty {
      width: 5%;
    }
    p {
      padding: 1px;
      margin: 0;
    }
    section,
    footer {
      font-size: 12px;
    }
  </style>
</head>

<body>
<header>
  <div id="logo" class="media" data-src="logo.png" src="./logo.png"></div>
</header>
<header style="text-align: center; font-weight: 600;">
  <p>{!! $doc->data->sellerDetails->legalName !!}</p>
  <p>Tel: {{ $doc->data->sellerDetails->linePhone }} TIN:  {{ $doc->data->sellerDetails->tin }}</p>
</header>
<hr/>
<table class="bill-details">
  <tbody style="font-weight: 500;">
  <tr >
    <td style="font-weight: 500;">FDN : <span>15837699643969543</span></td>
  </tr>
  <tr>
    <td>V.Code : <span>{{ $doc->data->basicInformation->antifakeCode }}</span></td>
  </tr>
  <tr>
    <td>Date : <span>{{ $doc->data->basicInformation->issuedDate }}</span></td>
  </tr>
{{--  <tr>--}}
{{--    <td>Customer : <span>Muwonge Hassan Saava</span></td>--}}
{{--  </tr>--}}
  <tr>
    @if (property_exists($doc->data->buyerDetails, 'buyerTin'))
    <td>
      Tin: <span class="text-xs font-thin">{{ $doc->data->buyerDetails->buyerTin }}</span>
    </td>
    @endif
  </tr>
  <tr>
    <th class="center-align" colspan="2">
      <span class="receipt">Item Details</span>
    </th>
  </tr>
  </tbody>
</table>

<table class="items">
  <thead>
  <tr>
    <th class="heading name">Item</th>
    <th class="heading qty">Qty</th>
    <th class="heading rate">Rate</th>
    <th class="heading amount">Amount</th>
  </tr>
  </thead>

  <tbody>

  @foreach ($doc->data->goodsDetails as $item)
    <tr align="center" >
      <td>{{ $item->item }}</td>
{{--      <td class="text-sm">{{ $item->goodsCategoryName }}</td>--}}
      <td class="text-center">{{ $item->qty }}</td>
      <td class="text-right priceg">
        {{ number_format($item->unitPrice, 2), $doc->data->basicInformation->currency }}
      </td>
      <td class="text-right priche">
        {{ number_format($item->total, 2), $doc->data->basicInformation->currency }}
      </td>
    </tr>
  @endforeach

  <tr style="margin-top: 10px">
    <td colspan="3" class="sum-up line">Tax Amount</td>
    <td class="line prkice">{{ number_format($doc->data->summary->taxAmount, 2) }}</td>
  </tr>
  <tr>
    <td colspan="3" class="sum-up">Net Amount</td>
    <td class="prkice">{{ number_format($doc->data->summary->netAmount, 2) }}</td>
  </tr>
  <!-- <tr>
    <td colspan="3" class="sum-up">SGST</td>
    <td class="price">10.00</td>
  </tr> -->
  <tr>
    <th colspan="3" class="total text">Total</th>
    <th class="total ">{{ number_format($doc->data->summary->grossAmount, 2) }}</th>
  </tr>
  </tbody>
</table>
{{--<section>--}}
{{--  <p>Paid by : <span>CASH</span></p>--}}

{{--  <p style="text-align: center">Thank you for your visit!</p>--}}
{{--</section>--}}
<footer style="text-align: center;">
  <img src="https://chart.apis.google.com/chart?cht=qr&chl=Hello&chs=248" alt="" srcset="" width="150">
  <p>All Prices are VAT inclusive where applicable</p>
  <p>Thank you for shopping with us!!!</p>
  <p>EFRIS solution provided by Weaf Company Uganda Ltd +256-756-508-361</p>
</footer>
</body>
</html>
