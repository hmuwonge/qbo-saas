<template>
  <div class="rounded-md">
    <div class="card invoice-preview-card">
      <div class="invoice-header card-body">
        <div
          class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column p-sm-3 p-0"
        >
          <div class="mb-xl-0 mb-4">
            <h6>{{ doc?.data?.sellerDetails.businessName }}</h6>
            <p>
              <b>Address</b>: {{ doc?.data?.sellerDetails.address }}<br />
              <b>TIN</b>: {{ doc?.data?.sellerDetails.tin }}<br />
              <b>Tel</b>: {{ doc?.data?.sellerDetails.linePhone }}<br />
              <b>Email</b>: {{ doc?.data?.sellerDetails.emailAddress }}
            </p>
          </div>
          <h1
            class="invoice-title"
            style="color: green; font-size: 200%; text-align: right"
          >
            INVOICE #{{ doc?.data?.basicInformation?.invoiceNo }}
          </h1>
        </div>
      </div>
      <hr class="my-0" />
      <div class="card-body">
        <div class="row p-sm-3 p-0">
          <div
            class="col-xl-6 col-md-12 col-sm-5 col-12 mb-xl-0 mb-md-4 mb-sm-0 mb-4"
          >
            <h6 class="pb-2">Billed To:</h6>
            <p class="mb-1">{{ doc?.data?.buyerDetails?.buyerBusinessName }}</p>
            <p v-if="doc?.data?.buyerDetails?.buyerTin">
              TIN: {{ doc?.data?.buyerDetails?.buyerTin }}
            </p>
          </div>

          <div class="col-xl-6 col-md-12 col-sm-7 col-12">
            <h6 class="pb-2">Invoice Information:</h6>
            <table>
              <tbody>
                <tr>
                  <td class="pe-3">Fiscal Document Number:</td>
                  <td>{{ doc?.data?.basicInformation?.invoiceNo }}</td>
                </tr>
                <tr>
                  <td class="pe-3">Verification Code:</td>
                  <td>{{ doc?.data?.basicInformation?.antifakeCode }}</td>
                </tr>
                <tr>
                  <td class="pe-3">Issue Date:</td>
                  <td>{{ doc?.data?.basicInformation?.issuedDate }}</td>
                </tr>
                <tr>
                  <td class="pe-3">Invoice Currency:</td>
                  <td>{{ doc?.data?.basicInformation?.currency }}</td>
                </tr>
                <tr>
                  <td class="pe-3">Served by:</td>
                  <td>{{ doc?.data?.basicInformation?.operator }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <hr class="my-0" />
      <div class="card-bod">

        <div class="table-responsive mt-10">
          <h4 class="text-2xl mb-2">Goods & Services</h4>
          <table class="table table-striped table-bordered border">
            <thead class="border bg-secondary text-base">
              <tr>
                <th scope="col" class="">Type</th>
                <th scope="col" class="">Description</th>
                <th scope="col" class="">QTY</th>
                <th scope="col" class="">Unit Price</th>
                <th scope="col" class="">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr
                class=""
                v-for="item in doc?.data?.goodsDetails"
                :key="item.item"
              >
                <td class="">{{ item.item }}</td>
                <td class="">{{ item.goodsCategoryName }}</td>
                <td class="">{{ item.qty }}</td>
                <td class="">
                  {{
                    formatCurrency(
                      item.unitPrice,
                      doc?.data?.basicInformation?.currency,
                    )
                  }}
                </td>
                <td class="px-6 py-4">
                  {{
                    formatCurrency(
                      item.total,
                      doc?.data?.basicInformation?.currency,
                    )
                  }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="table-responsive mt-10">
          <h4 class="text-2xl mb-2">Tax Details</h4>
          <table class="table table-striped table-bordered">
            <thead class="border bg-secondary mb-2 text-base">
              <tr>
                <th class="wd-20p">Tax Category</th>
                <th class="wd-20p">Tax Rate</th>
                <th class="wd-30p">Net Amount</th>
                <th class="tx-center">Tax Amount</th>
                <th class="tx-right">Gross Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="tax in doc?.data?.taxDetails" :key="tax.taxCategory">
                <td>{{ tax.taxCategory }}</td>
                <td>{{ tax.taxRateName }}</td>
                <td>
                  {{
                    formatCurrency(
                      tax.netAmount,
                      doc?.data?.basicInformation?.currency,
                    )
                  }}
                </td>
                <td class="text-center">
                  {{
                    formatCurrency(
                      parseFloat(tax.taxAmount),
                      doc?.data?.basicInformation.currency,
                    )
                  }}
                </td>
                <td class="text-right">
                  {{
                    formatCurrency(
                      parseFloat(tax.grossAmount),
                      doc?.data?.basicInformation.currency,
                    )
                  }}
                </td>
              </tr>
              <tr class="bg-secondary">
                <td colspan="5">
                  <h4 class="text-lg">Invoice Summary</h4>
                </td>
              </tr>
              <tr class="">
                <td colspan="2" rowspan="3" class="align-center">
                  <div class="invoice-notes">
                    <label class="az-content-label text-sm">Remarks</label>
                    <p>{{ doc?.data?.summary.remarks }}</p>
                  </div>
                </td>
                <td class="text-left">Net Amount</td>
                <td colspan="2" class="text-right">
                  {{
                    formatCurrency(
                      doc?.data?.summary.netAmount,
                      doc?.data?.basicInformation.currency,
                    )
                  }}
                </td>
              </tr>
              <tr>
                <td class="text-left">Tax Amount</td>
                <td colspan="2" class="text-right">
                  {{
                    formatCurrency(
                      doc?.data?.summary.taxAmount,
                      doc?.data?.basicInformation.currency,
                    )
                  }}
                </td>
              </tr>
              <tr>
                <td class="text-left uppercase font-bold tx-inverse">
                  Total Due
                </td>
                <td colspan="2" class="text-right">
                  <h4 class="font-bold text-2xl">
                    {{
                      formatCurrency(
                        doc?.data?.summary?.grossAmount,
                        doc?.data?.basicInformation.currency,
                      )
                    }}
                  </h4>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import "./style.css";
const props = defineProps({
  doc: {
    type: Object,
    // required: true,
  },
});

const formatCurrency = (number, currency) => {
  return new Intl.NumberFormat("ug", {
    style: "currency",
    currency: currency,
  }).format(number);
};
</script>

<style scoped>
.table-responsive {
  display: block;
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.card {
  position: relative;
  display: -ms-flexbox;
  display: -webkit-box;
  display: flex;
  -ms-flex-direction: column;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  flex-direction: column;
  min-width: 0;
  word-wrap: break-word;
  background-color: #fff;
  background-clip: border-box;
  border: 1px solid rgba(0, 0, 0, 0.125);
  border-radius: 0.25rem;
}

.card-body {
  -ms-flex: 1 1 auto;
  -webkit-box-flex: 1;
  flex: 1 1 auto;
  min-height: 1px;
  padding: 1.25rem;
}

.invoice-header {
  padding-bottom: 30px;
}
</style>
