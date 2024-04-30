<template>
  <div>
    <div class="rounded drop-shadow">
      <div class="row">
        <div class="col-xl-6 col-md-6 col-sm-12">
        <div class="card">
          <div class="card-body border rounded ">
              <div class="table-responsive">
                  <table class="table table-striped table-bordered">
                      <tr class="px-2">
                          <th class="text-left text-base md:text-sm md:font-bold">
                              Name
                          </th>
                          <td>{{ item?.Item?.FullyQualifiedName }}</td>
                      </tr>
                      <tr>
                          <th class="text-left md:text-sm md:font-bold">Product Code</th>
                          <td>{{ item?.Item?.Sku ?? "null" }}</td>
                      </tr>
                      <tr>
                          <th class="text-left sm:text-sm sm:font-bold">Description</th>
                          <td>{{ item?.Item?.Description }}</td>
                      </tr>
                      <tr>
                          <th class="text-left">Product Type</th>
                          <td>{{ item?.Item?.Type }}</td>
                      </tr>
                      <tr>
                          <th class="text-left">Unit Price</th>
                          <td>{{ item?.Item?.UnitPrice }}</td>
                      </tr>
                      <tr>
                          <th class="text-left">Purchase Cost</th>
                          <td>{{ item?.Item?.PurchaseCost }}</td>
                      </tr>
                      <tr>
                          <th class="text-left">Preferred Vendor</th>
                          <td>{{ item?.Item?.PrefVendorRef?.name }}</td>
                      </tr>
                      <tr>
                          <th class="text-left">Quantity in hand</th>
                          <td>{{ item?.Item?.QtyOnHand }}</td>
                      </tr>
                      <tr>
                          <th class="text-left">Re-order point</th>
                          <td>{{ item?.Item?.ReorderPoint }}</td>
                      </tr>
                      <tr>
                          <th class="text-left">Inventory Start Date</th>
                          <td>{{ item?.Item?.InvStartDate }}</td>
                      </tr>
                      <tr>
                          <th class="text-left">Registered on</th>
                          <td>{{ item?.Item?.MetaData?.CreateTime }}</td>
                      </tr>
                      <tr>
                          <th class="text-left">Last updated on</th>
                          <td>
                              {{ item?.Item?.MetaData?.LastUpdatedTime }}
                          </td>
                      </tr>
                      <tr>
                          <th class="text-left">Expense Account</th>
                          <td>
                              {{ item?.Item?.ExpenseAccountRef?.name }}
                          </td>
                      </tr>
                      <tr>
                          <th class="text-left">Assets Account</th>
                          <td>{{ item?.Item?.AssetAccountRef?.name }}</td>
                      </tr>
                      <tr>
                          <th class="text-left">Income Account</th>
                          <td>
                              {{ item?.Item?.IncomeAccountRef?.name }}
                          </td>
                      </tr>
                      <tr>
                          <th class="text-left">Is Taxable</th>
                          <td>
                              {{ item?.Item?.Taxable ? "Yes" : "No" }}
                          </td>
                      </tr>
                  </table>
              </div>

          </div>
          </div>
          </div>

          <div class="col-xl-6 col-md-6 col-sm-12">
              <div class="card">
                  <div class="card-body border rounded ">
          <div
            class="w-full ml-2 overflow-y-auto"
          >
            <div
              class="p-2 bg-info mb-2 border-box d-flex justify-content-between"
            >
              <div>
                <h4 class="text-white">
                  Fields marked with an asterisk (*) are Required
                </h4>
                <p class="text-sm mt-2">
                  Please fill in the form below to register
                  <span class="font-extrabold text-lg">{{
                    item?.Item?.FullyQualifiedName
                  }}</span>
                  with EFRIS
                </p>
              </div>
              <div>
                <secondary-button @click="back">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-6 h-6 mr-2"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M11.25 9l-3 3m0 0l3 3m-3-3h7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>

                  Back
                </secondary-button>
              </div>
            </div>
            <!-- {{ form }} -->
            <form @submit.prevent="handleSubmit">
              <div class="px-4">
                <div class="row my-6 text-lg">
                  <!-- ========================== start currecny filter ========================== -->
                  <div class="form-group col-6">
                    <div class="position-relative">
                      <input-label for="email" value="Currency" />
                      <system-input
                        type="text"
                        :placeholder="'search for currency'"
                        @focus="showCurrency"
                        @input="currencyInputHandler"
                        :value="selected_currency.text"
                        :onBlur="hideCurrency"
                      />

                      <div
                        v-show="isCurrency"
                        class="position-absolute shadow h-6 overflow-auto bg-white w-100 p-2 rounded border"
                        style="height: 300px"
                      >
                        <div
                          class="mt-0 pt-0 d-flex justify-content-between flex-row pr-2 align-items-center"
                          v-for="currency in filteredCurrencies"
                          :key="currency.id"
                        >
                          <span
                            type="text"
                            class="option-item text-base my-1 border-bottom w-100 cursor-pointer hoverable"
                            @click="selectedCurrency(currency, currency.id)"
                            readonly
                            >{{ currency?.text }}</span
                          >
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- ========================== end currecny filter ========================== -->

                  <!-- ========================== start unit of measure filter ========================== -->
                  <div class="form-group col-6">
                    <div class="form-group requiredu">
                      <div class="position-relative">
                        <input-label for="email" value="Unit Of Measure" />
                        <system-input
                          type="text"
                          :placeholder="'search for unit of measure'"
                          @focus="showmeasureUnit"
                          @input="measureUnitInputHandler"
                          :value="selected_unit_of_measure.text"
                          :onBlur="hidemeasureUnit"
                        />

                        <div
                          v-show="ismeasureUnit"
                          class="position-absolute shadow h-6 overflow-auto bg-white w-100 p-2 rounded border"
                          style="height: 300px"
                        >
                          <div
                            class="mt-0 pt-0 d-flex justify-content-between flex-row pr-2 align-items-center"
                            v-for="unit in filteredmeasureUnit"
                            :key="unit.id"
                          >
                            <span
                              class="option-item text-base my-1 border-bottom w-100 cursor-pointer hoverable"
                              @click="selectedUnit(unit, unit.id)"
                              readonly
                              >{{ unit?.text }}</span
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- ========================== start unit of measure filter ========================== -->
                </div>

                <!-- ======================= START HAVE ALTERNATIVE MEASURE UNIT ================= -->
                <div class="bg">
                  <div class="row my-6">
                    <!-- // commodity code handler section -->
                    <div class="form-group col-6">
                      <input-label for="email" value="UNSPSC" />
                      <system-input
                        type="text"
                        :placeholder="'enter unspsc code'"
                        @input="commodityCodeHandler"
                      />
                    </div>

                    <!--                    have alternative measure unit-->
                    <div class="form-group col-6">
                      <input-label
                        for="email"
                        value="Have Alternative Measure Unit"
                      />

                      <select
                        v-model="form.havePieceUnit"
                        @change="handleOtherPieceUnit"
                        class="form-control form-control-sm select2 text-gray-900 text-sm rounded-md block w-full p-2.5"
                        id="haveother"
                      >
                        <option value="101">Yes</option>
                        <option value="102">No</option>
                      </select>
                    </div>
                  </div>

                  <div
                    class="bg-light-secondary p-2 rounded"
                    v-if="form.havePieceUnit === '101'"
                  >
                    <div class="row my-65">
                      <!-- alternative measure unit -->
                      <div
                        class="form-group col-6"
                        v-show="form.havePieceUnit === '101'"
                      >
                        <div class="position-relative">
                          <input-label
                            for="email"
                            value="Alternative Unit of measure"
                          />
                          <system-input
                            type="text"
                            :placeholder="'Search for Other Unit of Measure'"
                            @focus="showPieceMeasureUnit"
                            @input="pieceMeasureUnitInputHandler"
                            :value="selected_piece_measure_unit.text"
                            :onBlur="hideisPieceMeasureUnit"
                          />

                          <div
                            v-if="ispieceMeasureUnit"
                            class="position-absolute shadow h-6 overflow-auto bg-white w-100 p-2 rounded border"
                            style="height: 300px"
                          >
                            <div
                              class="option-item text-base my-1 border-bottom w-100 cursor-pointer hoverable"
                              v-for="unit in filteredisPieceMeasureUnit"
                              :key="unit.id"
                            >
                              <span
                                type="text"
                                class="options-item"
                                @click="selectedPieceMeasureUnit(unit, unit.id)"
                                readonly
                                >{{ unit?.text }}</span
                              >
                            </div>
                          </div>
                        </div>
                      </div>

                      <div
                        class="form-group col-6 field-piece-scaled-value"
                        v-if="form.havePieceUnit === '101'"
                      >
                        <input-label
                          for="email"
                          value="Alternative measure Unit Price"
                        />
                        <system-input
                          type="number"
                          :placeholder="'Enter unit price'"
                          @input="pieceUnitPriceInputHandler"
                        />
                      </div>
                      <div class="invalid-feedback">
                        Please enter piece scaled value
                      </div>
                    </div>

                    <div class="row my-3">
                      <div
                        class="form-group col-6"
                        v-if="form.havePieceUnit === '101'"
                      >
                        <input-label for="email" value="Piece Scaled Value" />
                        <system-input
                          type="number"
                          :placeholder="'Enter piece scaled value'"
                          @input="pieceScaledValueInputHandler"
                        />
                      </div>

                      <div
                        class="form-group col-6"
                        v-if="form.havePieceUnit === '101'"
                      >
                        <input-label for="email" value="Package scaled value" />
                        <system-input
                          type="number"
                          :placeholder="'Enter package scaled value'"
                          @input="packageScaledValueInputHandler"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- ======================= END HAVE ALTERNATIVE MEASURE UNIT ================= -->

              <hr />

              <div class="px-4">
                <div class="row my-3">
                  <div class="form-group col-6">
                    <input-label for="email" value="Tax Rule" />

                    <select
                      v-model="form.itemTaxRule"
                      id="efrisitem-item_tax_rule"
                      class="form-control form-control-sm bg-gray-50 border mt-1 border-gray-300 text-gray-900 text-sm rounded-md focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5"
                    >
                      <option value="" selected>
                        Specific Tax Calculation if URA allows multiple options
                      </option>
                      <option value="URA">URA Tax Rules</option>
                      <option value="STANDARD">Standard Rated (18% VAT)</option>
                      <option value="ZERORATED">Zero Rated</option>
                      <option value="EXEMPT">Exempt</option>
                    </select>
                  </div>

                  <div class="form-group col-6">
                    <input-label
                      for="haveother"
                      value="Have Other Measure Unit?"
                    />
                    <select
                      v-model="form.haveOtherUnit"
                      @change="haveAlternativeMeasureUnit"
                      class="form-control form-control-sm bg-gray-50 border mt-1 border-gray-300 text-gray-900 text-sm rounded-md focus:ring-teal-500 focus:border-teal-500 w-full p-2.5"
                    >
                      <option value="" selected>
                        Does item have alternative unit of measure
                      </option>
                      <option value="101">Yes</option>
                      <option value="102">No</option>
                    </select>
                  </div>
                </div>

                <!-- <div class="border border-gray-300"></div> -->
                <!-- ======================= START HAVE OTHER MEASURE UNIT ================= -->
                <div
                  class="bg-light-secondary p-2 rounded"
                  v-if="form.haveOtherUnit === '101'"
                >
                  <div class="row my-3">
                    <div
                      class="form-group col-6"
                      v-show="form.haveOtherUnit === '101'"
                    >
                      <div class="position-relative">
                        <input-label
                          for="email"
                          value="Other Unit of Measure"
                        />
                        <system-input
                          type="text"
                          :placeholder="'search for other unit of measure'"
                          @focus="showOtherUnit"
                          @input="otherUnitInputHandler"
                          :value="selected_other_unit.text"
                          :onBlur="hideOtherUnit"
                        />

                        <div
                          v-if="isOtherUnit"
                          class="position-absolute shadow h-6 overflow-auto bg-white w-100 p-2 rounded border"
                          style="height: 300px"
                        >
                          <div
                            class="mt-0 pt-0 d-flex justify-content-between flex-row pr-2 align-items-center"
                            v-for="unit in filteredOtherUnit"
                            :key="unit.id"
                          >
                            <span
                              type="text"
                              class="option-item text-base my-1 border-bottom w-100 cursor-pointer hoverable"
                              @click="selectedOtherUnit(unit, unit.id)"
                              readonly
                              >{{ unit?.text }}</span
                            >
                          </div>
                        </div>
                      </div>
                    </div>

                    <div
                      class="form-group col-6"
                      v-if="form.haveOtherUnit === '101'"
                    >
                      <input-label for="email" value="Other Unit Price" />
                      <system-input
                        type="number"
                        :placeholder="'enter Alternative Measure Unit Price'"
                        @input="otherUnitPriceInputHandler"
                      />
                    </div>
                  </div>

                  <!-- <div class="grid lg:grid-cols-2 gap-2 my-3">
  <div class="form-group" v-if="form.havePieceUnit === '101'">

    <input-label for="email" value="Other Scaled Value" />
    <system-input type="number" :placeholder="'Enter piece scaled value'"
      @input="pieceScaledValueInputHandler" />
  </div>

  <div class="form-group" v-if="form.havePieceUnit === '101'">

    <input-label for="email" value="other package scaled value" />
    <system-input type="number" :placeholder="'Enter piece scaled value'"
      @input="packageScaledValueInputHandler" />
  </div>
</div> -->

                  <div class="row my-3">
                    <div
                      class="form-group col-6"
                      v-if="form.haveOtherUnit === '101'"
                    >
                      <input-label for="email" value="Other Scaled Value" />
                      <system-input
                        type="text"
                        :placeholder="'search for other unit of measure'"
                        @input="otherScaledInputHandler"
                      />
                    </div>

                    <div
                      class="form-group col-6"
                      v-if="form.haveOtherUnit === '101'"
                    >
                      <input-label
                        for="packageScaledValue"
                        value="Package Scaled Value"
                      />
                      <system-input
                        type="number"
                        :placeholder="'Enter package scaled value'"
                        id="packageScaledValue"
                        @input="packageScaledInputHandler"
                      />
                    </div>
                  </div>
                </div>

                <!--                            check if product has excise tax-->
                <div class="row">
                  <div class="form-group col-6">
                    <label class="text-gray-500">Have Excise Tax</label>
                    <select
                      v-model="form.haveExciseTax"
                      @change="haveExciseDuty"
                      class="form-control form-control-sm bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    >
                      <option value="" selected>
                        Does item attract excise duty?
                      </option>
                      <option value="101">Yes</option>
                      <option value="102">No</option>
                    </select>
                  </div>

                  <div class="form-group col-6">
                    <input-label for="email" value="Register opening Stock" />

                    <select
                      v-model="form.hasOpeningStock"
                      @change="handleOpeningStock"
                      class="form-control form-control-sm text-gray-900 text-sm rounded-md w-full p-2.5"
                    >
                      <option value="101">Yes</option>
                      <option value="102">No</option>
                    </select>
                  </div>
                </div>
                <div class="form-group col" v-if="form.haveExciseTax === '101'">
                  <input-label for="email" value="Excise Duty Code" />
                  <system-input
                    type="number"
                    :placeholder="'Enter Excise Duty Code'"
                    @input="exciseDutyCodeInputHandler"
                  />
                </div>
                <!-- ======================= START HAVE ALTERNATIVE MEASURE UNIT ================= -->

                <div
                  class="bg-light-secondary p-2 rounded"
                  v-if="form.hasOpeningStock === '101'"
                >
                  <hr />
                  <!--                  <div class="row my-6">-->
                  <!--                    &lt;!&ndash;                  does item have opening stock&ndash;&gt;-->
                  <!--                    <div class="form-group col-6">-->
                  <!--                      <input-label for="email" value="Register opening Stock" />-->

                  <!--                      <select-->
                  <!--                        v-model="form.hasOpeningStock"-->
                  <!--                        @change="handleOpeningStock"-->
                  <!--                        class="form-control form-control-sm text-gray-900 text-sm rounded-md w-full p-2.5"-->
                  <!--                      >-->
                  <!--                        <option value="101">Yes</option>-->
                  <!--                        <option value="102">No</option>-->
                  <!--                      </select>-->
                  <!--                    </div>-->
                  <!--                  </div>-->

                  <div class="row my-65">
                    <!-- alternative measure unit -->
                    <div
                      class="form-group col-6"
                      v-show="form.hasOpeningStock === '101'"
                    >
                      <div class="">
                        <input-label for="email" value="Supplier Tin" />
                        <system-input
                          type="text"
                          :placeholder="'enter supplier tin'"
                          @input="supplierTinHandler"
                        />
                      </div>
                    </div>

                    <div
                      class="form-group col-6 field-piece-scaled-value"
                      v-show="form.hasOpeningStock === '101'"
                    >
                      <input-label for="email" value="Supplier Name" />
                      <system-input
                        type="text"
                        :placeholder="'Enter supplier name'"
                        @input="supplierNameHandler"
                      />
                    </div>
                    <div class="invalid-feedback">
                      Please enter piece scaled value
                    </div>
                  </div>

                  <div class="row my-3">
                    <div
                      class="form-group col-6"
                      v-show="form.hasOpeningStock === '101'"
                    >
                      <input-label for="email" value="Stock In Date" />
                      <system-input type="date" @input="stockInDateHandler" />
                    </div>

                    <div
                      class="col col-6"
                      v-show="form.hasOpeningStock === '101'"
                    >
                      <div class="row">
                        <div class="col-6">
                          <input-label for="email" value="Stock In Quantity" />
                          <system-input
                            type="number"
                            @input="stockInQtyHandler"
                          />
                        </div>

                        <div class="col-6">
                          <input-label for="email" value="Stock In Price" />
                          <system-input
                            type="number"
                            @input="stockInPriceHandler"
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!--                  <div class="row my-3">-->
                  <!--                    -->
                  <!--                  </div>-->

                  <div
                    class="form-group col"
                    v-if="form.hasOpeningStock === '101'"
                  >
                    <input-label for="email" value="StockInRemarks" />
                    <textarea
                      @change="handleStockInRemarks"
                      rows="10"
                      cols="5"
                      class="form-control text-gray-900 text-sm"
                    >
                    </textarea>
                  </div>
                </div>

                <input type="hidden" v-model="form.Id" />
                <input type="hidden" v-model="form.stockStatus" />
                <input type="hidden" v-model="form.created_at" />
                <input type="hidden" v-model="form.itemCode" />

                <div class="my-3">
                  <!--                  <PrimaryButton class="ml-4">-->
                  <!--                    <span v-if="loading === true" class="">Please wait...</span>-->
                  <!--                    <span v-else>Submit</span>-->
                  <!--                  </PrimaryButton>-->
                  <primary-button
                    class="btn-secondary cursor-pointer py-0.5 px-1.5"
                    :class="{ 'opacity-25 loading': loading }"
                    :disabled="loading"
                    type="submit"
                  >
                    <span v-if="loading">Please wait...</span>
                    <span v-else>Register</span>
                  </primary-button>
                </div>
              </div>
            </form>
          </div>
        </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, toRaw, watchEffect } from "vue";
import Swal from "sweetalert2";
import axios from "axios";

const props = defineProps({
  item: {
    type: Object,
    default: {},
  },
  redo: {
    type: String,
    default: "no",
  },
  masterData: {
    type: Array,
    default: [],
  },
});

console.log(props.masterData);
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

const isSuccess = ref(false);

const hasAlternativerUnitsOfMeasure = ref("102");
const openingStockFields = ref([]);

const loading = ref(false);
const measureUnits = ref([]);

// currency consta
const isCurrency = ref(false);
const searched_currency = ref("");
const selected_currency = ref("");

const itemSku = computed(() => {
  return props.item?.Item?.Sku;
});

const form = ref({
  unitOfMeasure: "",
  created_at: props.item?.Item?.MetaData?.CreateTime,
  Id: props.item?.Item?.Id,
  stockStatus: 1,
  itemCode: itemSku.value,
  commodityCategoryId: "",

  // 101 == yes
  // 102 === no
  havePieceUnit: "102", //no

  // 101 == yes
  // 102 === no
  haveExciseTax: "102", // no

  pieceUnitPrice: "",
  pieceScaledValue: "",
  pieceMeasureUnit: "",
  packageScaledValue: "",

  itemTaxRule: "",

  hasOpeningStock: "101",
  haveExciseDuty: "101",

  // 101 == yes
  // 102 === no
  haveOtherUnit: "102", //no
  stockinSupplierTin: "",
  stockInsupplier: "",
  stockInRemarks: "",
  stockInDate: "",
  stockinQuantity: "",
  stockinPrice: "",
  currency: "",
  otherUnit: "",
  otherPrice: "",
  otherScaled: "",
  packageScaled: "",
  exciseDutyCode: "",
  isRegisteredInEfris: props.redo,
});

watchEffect(() => {
  measureUnits.value = props.masterData;
});

const goBackWithRoute = (route) => {
  const previousUrl = document.referrer;
  window.location.href = previousUrl + `?route=${route}`;
};

const closeAlert = () => {
  isSuccess.value = false;
};

//  =======================start currency fileter section===============

const showCurrency = () => {
  isCurrency.value = true;
};
const hideCurrency = () => {
  setTimeout(() => {
    isCurrency.value = false;
  }, 200);
};

const back = () => {
  window.history.back();
};
//filter patients
const filteredCurrencies = computed(() => {
  let currencyList = measureUnits.value?.data?.currencyType?.map((unit) => ({
    id: unit.value,
    text: unit.name,
  }));

  if (searched_currency.value) {
    currencyList = currencyList.filter(
      (currency) =>
        currency?.text
          .toUpperCase()
          .includes(searched_currency.value?.toUpperCase()) ||
        currency?.text
          .toUpperCase()
          .includes(searched_currency.value?.toUpperCase()),
    );
  }
  return currencyList;
});

function selectedCurrency(event, id) {
  selected_currency.value = event;
  form.value.currency = id;
  hideCurrency();
}

const currencyInputHandler = (event) => {
  console.log(event.target.value);
  searched_currency.value = event.target.value;
};

const commodityCodeHandler = (event) => {
  form.value.commodityCategoryId = event.target.value;
};

// for has stock in
const supplierTinHandler = (event) => {
  form.value.stockinSupplierTin = event.target.value;
};

const supplierNameHandler = (event) => {
  form.value.stockInsupplier = event.target.value;
};

const stockInDateHandler = (event) => {
  form.value.stockInDate = event.target.value;
};

const handleStockInRemarks = (event) => {
  form.value.stockInRemarks = event.target.value;
};

const stockInQtyHandler = (event) => {
  form.value.stockinQuantity = event.target.value;
};

const stockInPriceHandler = (event) => {
  form.value.stockinPrice = event.target.value;
};

const otherUnitPriceInputHandler = (event) => {
  form.value.otherPrice = event.target.value;
};

const otherScaledInputHandler = (event) => {
  form.value.otherScaled = event.target.value;
};

// =============================// values that need "havePieceUnit"==================
const packageScaledValueInputHandler = (event) => {
  form.value.packageScaledValue = event.target.value;
};

const pieceUnitPriceInputHandler = (event) => {
  form.value.pieceUnitPrice = event.target.value;
};

const pieceScaledValueInputHandler = (event) => {
  form.value.pieceScaledValue = event.target.value;
};
// =============================// values that need "havePieceUnit"==================

const packageScaledInputHandler = (event) => {
  form.value.packageScaled = event.target.value;
};

const exciseDutyCodeInputHandler = (event) => {
  form.value.exciseDutyCode = event.target.value;
};

//  =======================end currency fileter section===============

//  =======================start unit of measures section===============
// unit of mesasure
const ismeasureUnit = ref(false);
const searched_unit_of_measure = ref("");
const selected_unit_of_measure = ref("");

const showmeasureUnit = () => {
  ismeasureUnit.value = true;
};
const hidemeasureUnit = () => {
  setTimeout(() => {
    ismeasureUnit.value = false;
  }, 200);
};

//filter unit of measures
const filteredmeasureUnit = computed(() => {
  let unitList = measureUnits.value?.data.rateUnit.map((unit) => ({
    id: unit.value,
    text: unit.name,
  }));

  if (searched_unit_of_measure.value) {
    unitList = unitList.filter(
      (unit) =>
        unit?.text
          .toUpperCase()
          .includes(searched_unit_of_measure.value?.toUpperCase()) ||
        unit?.text
          .toUpperCase()
          .includes(searched_unit_of_measure.value?.toUpperCase()),
    );
  }
  return unitList;
});

function selectedUnit(event, id) {
  selected_unit_of_measure.value = event;
  form.value.unitOfMeasure = id;
  hidemeasureUnit();
}

const measureUnitInputHandler = (event) => {
  // console.log(event.target.value);
  const value = event.target.value;
  searched_unit_of_measure.value = value;
};

//  =======================end currency fileter section===============

//  =======================start other unit fileter section===============
// other unit
const isOtherUnit = ref(false);
const searched_other_unit = ref("");
const selected_other_unit = ref("");

const showOtherUnit = () => {
  isOtherUnit.value = true;
};
const hideOtherUnit = () => {
  setTimeout(() => {
    isOtherUnit.value = false;
  }, 200);
};

//filter patients
const filteredOtherUnit = computed(() => {
  let unitList = measureUnits.value?.data?.rateUnit?.map((unit) => ({
    id: unit.value,
    text: unit.name,
  }));

  if (searched_other_unit.value) {
    unitList = unitList.filter(
      (unit) =>
        unit?.text
          .toUpperCase()
          .includes(searched_other_unit.value?.toUpperCase()) ||
        unit?.text
          .toUpperCase()
          .includes(searched_other_unit.value?.toUpperCase()),
    );
  }
  return unitList;
});

function selectedOtherUnit(event, id) {
  selected_other_unit.value = event;
  form.value.otherUnit = id;
  hidemeasureUnit();
}

const otherUnitInputHandler = (event) => {
  searched_other_unit.value = event.target.value;
};

//  =======================end currency fileter section===============

//  =======================start other unit fileter section===============dkjskg
// other unit
const ispieceMeasureUnit = ref(false);
const searched_piece_measure_unit = ref("");
const selected_piece_measure_unit = ref("");
const search_piece_measure_unit = ref("");

const showPieceMeasureUnit = () => {
  ispieceMeasureUnit.value = true;
};
const hideisPieceMeasureUnit = () => {
  setTimeout(() => {
    ispieceMeasureUnit.value = false;
  }, 200);
};

//filter patients
const filteredisPieceMeasureUnit = computed(() => {
  let unitList = measureUnits.value?.data?.rateUnit?.map((unit) => ({
    id: unit.value,
    text: unit.name,
  }));

  if (search_piece_measure_unit.value) {
    unitList = unitList.filter(
      (unit) =>
        unit?.text
          .toUpperCase()
          .includes(search_piece_measure_unit.value?.toUpperCase()) ||
        unit?.text
          .toUpperCase()
          .includes(search_piece_measure_unit.value?.toUpperCase()),
    );
  }
  return unitList;
});

function selectedPieceMeasureUnit(event, id) {
  selected_piece_measure_unit.value = event;
  form.value.pieceMeasureUnit = id;
  hidemeasureUnit();
}

const pieceMeasureUnitInputHandler = (event) => {
  search_piece_measure_unit.value = event.target.value;
};

//  =======================end currency fileter section===============

const currencyTypes = computed(() => {
  return measureUnits.value?.data?.currencyType?.map((unit) => ({
    id: unit.value,
    text: unit.name,
  }));
});

const taxRules = computed(() => {
  return measureUnits.value?.data?.currencyType?.map((unit) => ({
    id: unit.value,
    text: unit.name,
  }));
});

const unitOptions = computed(() => {
  return measureUnits.value?.data?.rateUnit?.map((unit) => ({
    id: unit.value,
    text: unit.name,
  }));
});

const handleOpeningStock = () => {
  if (form.value.hasOpeningStock === "102") {
    this.$refs.openingStockFields.forEach((field) =>
      field.classList.add("required"),
    );

    form.value.stockInRemarks = "";
    form.value.stockInsupplier = "";
    form.value.stockInDate = "";
    form.value.stockinSupplierTin = "";
  } else {
    this.$refs.openingStockFields.forEach((field) =>
      field.classList.remove("required"),
    );
  }
};

const handleOtherPieceUnit = () => {
  if (form.value.havePieceUnit === "102") {
    // openingStockFields.value.forEach((field) =>
    //   field.classList.add("required")
    // );
    $(".field-piece-scaled-value").addClass("is-invalid");
    $(".peice_unit_options").show();
    form.value.pieceUnitPrice = "";
    form.value.pieceScaledValue = "";
    form.value.pieceMeasureUnit = "";
    form.value.packageScaledValue = "";
  }
  // else {
  //   openingStockFields.value.forEach((field) =>
  //     field.classList.remove("required")
  //   );
  // }
};

const haveAlternativeMeasureUnit = (event) => {
  if (form.value.haveOtherUnit === "102") {
    form.value.otherPrice = "";
    form.value.otherScaled = "";
    form.value.otherUnit = "";
    form.value.packageScaled = "";
  }
};

const haveExciseDuty = () => {
  if (form.value.haveExciseTax === "102") {
    // openingStockFields.value.forEach((field) =>
    //   field.classList.add("required")
    // );
    form.value.exciseDutyCode = "";
  } else {
    openingStockFields.value.forEach((field) =>
      field.classList.remove("required"),
    );
  }
};

onMounted(() => {
  openingStockFields.value = Array.from(
    document.querySelectorAll(".opening-stock-field"),
  );

  // console.log(data);
  measureUnits.value = toRaw(props.masterData);
});

const handleSubmit = async () => {
  loading.value = true;
  const formValues = toRaw(form.value);

  console.log(formValues);

  if (formValues.Id === undefined) {
    alert("Item not defined or your quickbooks auth is expired");
  }

  await axios
    .post(`/quickbooks/goods/register-product/${formValues.Id}/no`, formValues)
    .then((response) => {
      if (response.status === 200) {
        loading.value = false;
        let data = response.data;
        console.log(data);
        const { status, msg, payload } = response.data;

        console.log(data);

        if (status === "SUCCESS") {
          Toast.fire({
            title: "Success",
            icon: "success",
            text: `${msg}`,
          });

          setTimeout(() => {
            window.history.back();
          }, 800);
        }
        if (status === "FAIL") {
          // Toast.fire({
          //   title: 'Error',
          //   icon: "error",
          //   text: `${msg}`
          // });

          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: msg,
          });
        }
      }

      if (response.status === 202) {
        const { status, msg } = response.data;

        if (status === "FAIL") {
          // Toast.fire({
          //   title: 'Error',
          //   icon: "error",
          //   text: `${msg}`
          // });

          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: `${msg}`,
          });
        }
      }
    })
    .catch((error) => {
      if (error.response.status === 401) {
      } else if (error.response.status === 404) {
        // Handle not found error
        console.error("Not found");
      } else {
        // Handle other errors
        console.error(error.response);
      }
    })

    .finally(() => {
      loading.value = false;
      isSuccess.value = false;
      // this.efrisError = false
    });
};

// (async function masterData() {
//   loading.value = true;
//   await axios
//     .get("/api/efris-ura/unit-of-measure")
//     .then((response) => {
//       measureUnits.value = response.data;
//       // console.log(measureUnits.value?.data?.rateUnit)
//     })
//     .catch((e) => {
//       // this.error = false
//       console.log(e);
//     })
//     .finally(() => {
//       loading.value = false;
//       // this.efrisError = false
//     });
// })();

// (async function unspcCodes() {
//   loading.value = true;
//   await axios
//     .get("/api/efris-ura/unspsc-codes")
//     .then((response) => {
//       unspcCodes.value = response.data;
//       // console.log(measureUnits.value?.data?.rateUnit)
//     })
//     .catch((e) => {
//       // this.error = false
//       console.log(e);
//     })
//     .finally(() => {
//       loading.value = false;
//       // this.efrisError = false
//     });
// })();
</script>

<style scoped>
.required {
  background: red;
}

form div.required label.control-label:after {
  content: " * ";
  color: red;
}
</style>
