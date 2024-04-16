<template>
  <div class="card">
    <div class="d-flex row card-body">
      <div class="col-9 bg-white rounded-md">
        <form id="fiscalise-credit-memo-form" @submit.prevent="submitForm">
          <h3 class="p-2 font-extrabold text-2xl">
            Quickbooks credit memo items
          </h3>
          <table
            class="table table-striped table-bordered text-sm text-left text-black"
          >
            <thead class="text-xs text-gray-700 uppercase">
              <tr class="bg-success">
                <th scope="col" class="">Line Ref</th>
                <th scope="col" class="">Item</th>
                <th scope="col" class="">Quantity</th>
                <th scope="col" class="">Unit Price</th>
                <th scope="col" class="">Total Price</th>
              </tr>
            </thead>
            <tbody>
              <tr
                class="bg-white border-b"
                v-for="item in creditdata.creditMemoItems"
                :key="item.LineNum"
              >
                <th class="">
                  LINE{{ item.LineNum }}
                  <input
                    type="hidden"
                    :disabled="true"
                    :id="`qb_qty_${item.LineNum}`"
                    :name="`qb_qty_${item.LineNum}`"
                    :value="item.SalesItemLineDetail.Qty"
                  />
                  <input
                    type="hidden"
                    :disabled="true"
                    :id="`qb_unitprice_${item.LineNum}`"
                    :name="`qb_unitprice_${item.LineNum}`"
                    :value="item.SalesItemLineDetail.UnitPrice"
                  />
                  <input
                    type="hidden"
                    :disabled="true"
                    :id="`qb_total_${item.LineNum}`"
                    :name="`qb_total_${item.LineNum}`"
                    :value="item.SalesItemLineDetail.TaxInclusiveAmt"
                  />
                </th>
                <td class="">
                  {{ item.SalesItemLineDetail.ItemRef.name }}
                </td>
                <td class="">{{ item.SalesItemLineDetail.Qty }}</td>
                <td class="">
                  {{ item.SalesItemLineDetail.UnitPrice }}
                </td>
                <td class="">
                  {{ item.SalesItemLineDetail.TaxInclusiveAmt }}
                </td>
              </tr>
            </tbody>
          </table>

          <div class="my-10 table-responsive">
            <h3 class="p-2 font-extrabold text-2xl">Invoice details</h3>
            <table
              class="table table-bordered table-striped text-sm text-left text-black"
            >
              <thead class="text-xs text-gray-700 uppercase bg-secondary">
                <tr class="bg-sky-400 text-lg border">
                  <th colspan="4" class="py-2">FISCAL INVOICE ITEMS</th>
                  <th colspan="5" class="py-2">QUICKBOOKS CREDIT MEMO ITEMS</th>
                  <th>OPTIONS</th>
                </tr>

                <tr>
                  <th>#</th>
                  <th scope="col" class="px-6 py-3">Item Code</th>
                  <th scope="col" class="px-6 py-3">Item</th>
                  <th scope="col" class="px-6 py-3">Quantity</th>
                  <th scope="col" class="px-6 py-3">Unit Price</th>
                  <td>Quickbooks Item Line Ref.</td>
                  <th scope="col" class="px-6 py-3">Quantity</th>
                  <th scope="col" class="px-6 py-3">Unit Price</th>
                  <th scope="col" class="px-6 py-3">Total Price</th>
                  <th scope="col" class="px-6 py-3">&nbsp;</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  class="bg-white border"
                  v-for="(item, index) in newInvoiceData"
                  :key="item.orderNumber"
                  :id="`efris${item.orderNumber}`"
                >
                  <th>{{ `efris${item.orderNumber}` }}</th>
                  <th class="px-6 py-4">{{ item.itemCode }}</th>
                  <td class="px-6 py-4">{{ item.item }}</td>
                  <td class="tx-center px-6 py-4">{{ item.qty }}</td>
                  <td class="text-right px-6 py-4">
                    {{ item.unitPrice }}
                  </td>
                  <td class="px-6 py-4">
                    <select
                      :id="`line${item.orderNumber}`"
                      @change="
                        setInvoiceCreditValues(
                          $event.target.value,
                          item.orderNumber,
                        )
                      "
                      class="form-control bg-gray-300 rounded-md"
                    >
                      <option value="" selected>Select Item</option>
                      <option
                        v-for="line in lines"
                        :key="line.id"
                        :value="line.id"
                      >
                        {{ line.name }}
                      </option>
                    </select>
                  </td>

                  <td>
                    <input
                      :name="'itemCode[' + item.orderNumber + ']'"
                      v-model="item.itemCode"
                      type="hidden"
                    />
                    <input
                      :name="'taxCode[' + item.orderNumber + ']'"
                      v-model="itemTaxCodes[item.orderNumber]"
                      type="hidden"
                    />
                    <input
                      :name="'orderNumber[' + item.orderNumber + ']'"
                      v-model="item.orderNumber"
                      type="hidden"
                    />

                    <input
                      :id="`efris_qty_${item.orderNumber}`"
                      :name="`quantity[${item.orderNumber}]`"
                      class="form-control text-center disabled rounded-md bg-gray-300"
                      v-model="item.qty"
                    />
                  </td>

                  <td class="px-6 py-4">
                    <input
                      :id="`efris_price_${item.orderNumber}`"
                      :name="`unitprice[${item.orderNumber}]`"
                      v-model="item.unitPrice"
                      class="form-control text-center rounded-md bg-gray-300"
                    />
                  </td>
                  <td class="px-6 py-4">
                    <input
                      :id="`efris_total_${item.orderNumber}`"
                      :name="`totalprice[${item.orderNumber}]`"
                      v-model="item.total"
                      class="form-control disabled text-center rounded-md bg-gray-300"
                    />
                  </td>
                  <td>
                    <secondary-button
                      class="text-sm btn-sm bg-danger rounded"
                      @click="removeRow(index)"
                    >
                      <!--                      <x-heroicon-o-arrow-left class="w-6 h-6 text-gray-500" />-->
                      <!--                      <XCircleIcon class="w-6 h-6" />-->
                      Remove
                    </secondary-button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <table
            class="table table-striped table-borderless text-sm w-full text-left text-black mt-10"
          >
            <tr width="100" class="">
              <td class="text-lg flex flex-col">
                <label for="creditmemo-reasoncode">Reason</label>
                <select
                  id="creditmemo-reasoncode"
                  v-model="creditmemo.reasonCode"
                  @change="checkSelectedReason"
                  class="form-control bg-gray-300 rounded"
                >
                  <option value="" disabled selected>Select Reason</option>
                  <option
                    v-for="(value, key) in reasons"
                    :key="key"
                    :value="key"
                  >
                    {{ value }}
                  </option>
                </select>
              </td>
            </tr>
            <tr v-if="creditmemo.reasonCode === '105'" id="specific_reason">
              <td class="d-flex flex-col text-lg">
                <label for="creditmemo-reason">Please specify the reason</label>
                <textarea
                  class="bg-gray-200 rounded-sm form-control"
                  id="creditmemo-reason"
                  v-model="creditmemo.reason"
                  rows="1"
                  style="height: 80px"
                ></textarea>
              </td>
            </tr>
            <tr class="">
              <td class="text-lg flex flex-col">
                <label for="creditmemo-remarks">Remarks</label>
                <textarea
                  class="bg-gray-200 rounded-md form-control"
                  id="creditmemo-remarks"
                  v-model="creditmemo.remarks"
                  rows="2"
                ></textarea>
              </td>
            </tr>
            <tr>
              <td>
                <input type="hidden" v-model="creditmemo.id" />
                <input type="hidden" v-model="creditmemo.oriInvoiceNo" />
                <input type="hidden" v-model="creditmemo.sellersReferenceNo" />
                <input
                  type="hidden"
                  v-model="creditmemo.invoiceApplyCategoryCode"
                />

                <primary-button
                  :class="'bg-primary btn-block rounded shadow-sm mt-2'"
                >
                  <i class="ri-arrow-turn-back-fill"></i>
                  <span v-if="loading"> Loading...</span>
                  <span v-else> Submit</span>
                </primary-button>
              </td>
            </tr>
          </table>
        </form>
      </div>
      <div class="col-3 md:text-sm card rounded-md">
        <div class="row bg-info p-2 text-2xl">
          <h2 class="card-header text-lg">
            Fields marked with an asterisk (*) are Required
          </h2>
          <div class="card-body">
            <p class="text-lg">
              1. Then first table shows the list of items in the credit memo as
              entered in your Quickbooks system
            </p>
            <p class="text-lg">
              2. Remove the line from the second table which are not included in
              the credit note
            </p>
            <p class="text-lg">
              3. Please select the corresponding <b>Line Ref</b> in the second
              table to match the invoice item line and the items specified in
              the credit memo
            </p>
            <p class="text-lg">
              4. Specify the reason why you are creating this credit note
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, toRaw } from "vue";
import { XCircleIcon } from "@heroicons/vue/outline";
// import { XCircleIcon } from "@heroicons/vue/solid";
import Swal from "sweetalert2";
// import Spinner from "@/Components/Spinner.vue";

const props = defineProps({
  creditdata: {
    type: Object,
    required: true,
  },

  reasons: {
    type: Array,
    required: true,
  },
});
const Toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener("mouseenter", Swal.stopTimer);
    toast.addEventListener("mouseleave", Swal.resumeTimer);
  },
});
const lines = ref([]);
const newInvoiceData = ref([]);
const invoiceData = ref({
  goods: props.creditdata?.originalInvoice.data.goodsDetails,
});
const itemTaxCodes = ref([]);
const items = ref({
  unitPrice: "",
  quantity: "",
  totalPrice: "",
});
const creditmemo = ref({
  reasonCode: "",
  reason: "",
  remarks: "",
  id: props.creditdata.id,
  oriInvoiceNo: props.creditdata.oriInvoiceNo,
  sellersReferenceNo: props.creditdata.sellersReferenceNo,
  invoiceApplyCategoryCode: "101",
});

function setInvoiceCreditValues(selectedvalue, ura_ref) {
  const qtyField = document.getElementById(`efris_qty_${ura_ref}`);
  const priceField = document.getElementById(`efris_price_${ura_ref}`);
  const totalField = document.getElementById(`efris_total_${ura_ref}`);

  qtyField.value = document.getElementById(`qb_qty_${selectedvalue}`).value;
  priceField.value = document.getElementById(
    `qb_unitprice_${selectedvalue}`,
  ).value;
  totalField.value = document.getElementById(`qb_total_${selectedvalue}`).value;
}

function getSelectedInvoiceLine(ref) {
  return document.getElementById(`line${ref}`).value;
}

function checkSelectedReason() {
  const selectedvalue = document.getElementById("creditmemo-reasoncode").value;
  const specificReason = document.getElementById("specific_reason");

  if (selectedvalue === "105") {
    specificReason.style.display = "block";
  } else {
    specificReason.style.display = "none";
  }
}

function removeRow(index) {
  newInvoiceData.value.splice(index, 1);
}

const loading = ref(false);
async function submitForm() {
  const formData = {
    CreditMemo: toRaw(creditmemo.value),
    orderNumber: toRaw(newInvoiceData.value),
  };

  loading.value = true;

  await axios
    .post("/quickbooks/creditmemos/send-fiscalise-credit-note", formData)
    .then((response) => {
      let data = response.data;
      console.log(data);
      if (response.status === 200) {
        if (data.status === "SUCCESS") {
          Toast.fire({
            title: "Success",
            icon: "success",
            text: `${data.payload}`,
          });
        }

        if (data.status === "FAIL") {
          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: `${data.payload}`,
          });
        }
      }
    })
    .catch((error) => {
      // handle error
      console.log(error);
    })
    .finally(() => {
      // isError.value = false
      loading.value = false;
      window.location.reload();
    });
}

const initializeData = () => {
  props.creditdata.creditMemoItems.forEach((item) => {
    const num = item.LineNum;
    lines.value.push({
      id: num,
      name: `LINE${num}`,
      unitPrice: item.SalesItemLineDetail.UnitPrice,
      qty: item.SalesItemLineDetail.Qty,
      totalPrice: item.Amount,
    });
    itemTaxCodes.value.push(item.SalesItemLineDetail.TaxCodeRef.value);
  });

  const formatCurrency = (number, currency) => {
    return new Intl.NumberFormat("ug", {
      style: "currency",
      currency: currency,
    }).format(number);
  };
};

onMounted(() => {
  initializeData();
  invoiceData.value.goods.forEach((item) => {
    let newInvoice = {};
    newInvoice.item = item.item;
    newInvoice.itemCode = item.itemCode;
    newInvoice.orderNumber = item.orderNumber;
    newInvoice.unitPrice = item.unitPrice;
    newInvoice.qty = item.qty;
    newInvoice.total = item.total;
    newInvoiceData.value.push(newInvoice);
  });
});
</script>

<style>
.disabled {
  pointer-events: none;
  background: #eee;
  border: none;
  font-weight: 700;
  border-radius: 0px;
}
</style>
