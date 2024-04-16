<template>
  <!--    <template #header>-->
  <!--        <h2 class="font-bold text-2xl text-gray-800 leading-tight">-->
  <!--            Register Opening Stock-->
  <!--        </h2>-->
  <!--        <p class="text-sm">-->
  <!--            Map product with URA product categories-->
  <!--        </p>-->

  <!--    </template>-->
  <div class="bg-gray-200 shadow-sm p-5">
    <div class="bg-white rounded-sm shadow-sm">
      <div class="grid lg:grid-cols-2 p-4">
        <div class="w-full rounded border-2 px-2">
          <table
            class="w-full text-left text-black table table-responsive rounded-md table-striped"
          >
            <tr class="px-2">
              <th class="text-left py-2 text-base">Name</th>
              <td>{{ item?.Item?.FullyQualifiedName }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Product Code</th>
              <td>{{ item?.Item?.Sku ?? "null" }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Description</th>
              <td>{{ item?.Item?.Description }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Product Type</th>
              <td>{{ item?.Item?.Type }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Unit Price</th>
              <td>{{ item?.Item?.UnitPrice }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Purchase Cost</th>
              <td>{{ item?.Item?.PurchaseCost }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Preferred Vendor</th>
              <td>{{ item?.Item?.PrefVendorRef?.name }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Quantity in hand</th>
              <td>{{ item?.Item?.QtyOnHand }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Re-order point</th>
              <td>{{ item?.Item?.ReorderPoint }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Inventory Start Date</th>
              <td>{{ item?.Item?.InvStartDate }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Registered on</th>
              <td>{{ item?.Item?.MetaData?.CreateTime }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Last updated on</th>
              <td>{{ item?.Item?.MetaData?.LastUpdatedTime }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Expense Account</th>
              <td>{{ item?.Item?.ExpenseAccountRef?.name }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Assets Account</th>
              <td>{{ item?.Item?.AssetAccountRef?.name }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Income Account</th>
              <td>{{ item?.Item?.IncomeAccountRef?.name }}</td>
            </tr>
            <tr>
              <th class="text-left py-2">Is Taxable</th>
              <td>{{ item?.Item?.Taxable ? "Yes" : "No" }}</td>
            </tr>
          </table>
        </div>

        <div class="w-full ml-2 border-2 rounded">
          <div class="p-2 bg-sky-400 mb-2 font-bold">
            <h2>OPENING STOCK DETAILS</h2>
          </div>

          <alerts />
          <form @submit.prevent="submit">
            <div class="px-4">
              <!--                            {{itemId}}-->

              <div class="grid lg:grid-cols-2 gap-2">
                <div class="form-group my-3">
                  <input-label for="email" value="Opening Stock Quantity" />
                  <system-input
                    type="number"
                    @input="openingStockQtyInputHandler"
                    :value="itemQtyOnHand"
                    disabled
                  />
                </div>
                <div class="form-group my-3">
                  <input-label for="email" value="Stock in Date" />
                  <system-input type="date" @input="stockInDateInputHandler" />
                </div>
              </div>

              <div class="grid lg:grid-cols-2 gap-2 my-3">
                <div class="form-group">
                  <div class="relative">
                    <input-label for="email" value="Uint Of Measure" />
                    <system-input
                      type="text"
                      :placeholder="'search for unit of measure'"
                      @focus="showUnitofMeasure"
                      @input="unitOfMeasureInputHandler"
                      :value="selected_unit_of_measure.text"
                      :onBlur="hideUnitOfMeasure"
                    />

                    <div v-if="isUnitOfMeasure" class="options-container">
                      <div
                        class="mt-0 pt-0 d-flex justify-content-between flex-row pr-2 align-items-center"
                        v-for="unit in filteredUnitOfMeasure"
                        :key="unit.id"
                      >
                        <span
                          type="text"
                          class="options-item"
                          @click="selectedUnit(unit, unit.id)"
                          readonly
                          >{{ unit?.text }}</span
                        >
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <input-label
                    for="email"
                    value="Purchase Cost (Tax Inclusive)"
                  />
                  <system-input
                    type="number"
                    @input="purchaseCostInputHandler"
                  />
                </div>
              </div>

              <div class="grid lg:grid-cols-2 gap-2 my-3">
                <div class="form-group">
                  <input-label for="email" value="Name of Supplier" />
                  <system-input
                    type="text"
                    @input="nameOfSupplierInputHandler"
                    :placeholder="'Enter name of supplier'"
                  />
                </div>

                <div class="form-group">
                  <input-label for="tin" value="Supplier Tin" />
                  <system-input
                    type="number"
                    @input="supplierTinInputHandler"
                    :placeholder="'Enter supplier tin'"
                  />
                </div>
              </div>

              <div class="form-group">
                <label>Remarks</label>
                <textarea
                  v-model="form.stockin_remarks"
                  rows="4"
                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-100 rounded-md border border-gray-800 focus:ring-teal-500 focus:border-teal-500"
                  placeholder="Remarks"
                ></textarea>
              </div>
              <div class="my-3">
                <PrimaryButton
                  class="ml-4 float-right"
                  :class="{ loading: loading }"
                  :disabled="loading"
                >
                  Submit
                </PrimaryButton>
              </div>
            </div>

            <input type="hidden" v-model="form.Id" />
            <input type="hidden" v-model="form.stockStatus" />
            <input type="hidden" v-model="form.created_at" />
            <input type="hidden" v-model="form.itemCode" />
          </form>
          <!--                    {{ form }}-->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, toRaw } from "vue";
// import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
// import Alerts from "@/Components/Alerts.vue";
// import PrimaryButton from "@/Components/PrimaryButton.vue";
// import Button from "@/Components/Button.vue";
import Swal from "sweetalert2";

const props = defineProps({
  item: {
    type: Object,
    default: {},
  },
  measureunit: {
    type: Object,
  },
  itemId: {
    type: Number,
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

const itemQtyOnHand = computed(() => {
  return props.item?.Item?.QtyOnHand;
});

const itemPrice = computed(() => {
  return props.item?.Item?.UnitPrice;
});

const itemSku = computed(() => {
  return props.item?.Item?.Sku;
});

const loading = ref(false);

// console.log(props.item?.Item?.UnitPrice)
const form = ref({
  stockin_date: "",
  stockin_quantity: itemQtyOnHand.value,
  stockin_measureUnit: "",
  stockin_remarks: "",
  stockin_purchase_cost: "",
  stockin_supplier_tin: "",
  stockin_supplier: "",
  stockStatus: 1,
  stockin_price: itemPrice.value,
  created_at: props.item?.Item?.MetaData.CreateTime,
  itemCode: itemSku.value,
  Id: props.item?.Item?.Id,
});

const submit = async () => {
  loading.value = true;
  let formValues = toRaw(form.value);

  console.log(formValues);
  await axios
    .post(`/quickbooks/register-opening-stock/${formValues.Id}`, formValues)
    .then((response) => {
      if (response.status === 200) {
        let data = response.data;
        const { status, payload } = response.data;

        if (status === "SUCCESS") {
          Toast.fire({
            title: "Success",
            icon: "success",
            text: `${payload}`,
          });
        }

        if (status === "FAIL") {
          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: `${payload}`,
          });
        }

        if (data.status === 500) {
          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: `${payload}`,
          });
        }
      }

      if (response.status === 202) {
        const { status, payload } = response.data;

        if (status === "FAIL") {
          Toast.fire({
            title: "Error",
            icon: "error",
            text: `${payload}`,
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
    });
};

const openingStockFields = ref([]);

const openingStockQuantity = ref(0);
const measureUnits = ref([]);

const unitOptions = computed(() => {
  return measureUnits.value?.data?.rateUnit?.map((unit) => ({
    id: unit.value,
    text: unit.name,
  }));
});

const handleOpeningStock = () => {
  if (hasOpeningStock.value === "101") {
    openingStockFields.value.forEach((field) =>
      field.classList.add("required"),
    );
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
});

(async function unitsOfMeasure() {
  loading.value = true;
  await axios
    .get("/api/efris-ura/unit-of-measure")
    .then((response) => {
      measureUnits.value = response.data;
      // console.log(measureUnits.value?.data?.rateUnit)
    })
    .catch((e) => {
      // this.error = false
      console.log(e);
    })
    .finally(() => {
      loading.value = false;
      // this.efrisError = false
    });
})();

// unit of mesasure
const isUnitOfMeasure = ref(false);
const searched_unit_of_measure = ref("");
const selected_unit_of_measure = ref("");

const showUnitofMeasure = () => {
  isUnitOfMeasure.value = true;
};
const hideUnitOfMeasure = () => {
  setTimeout(() => {
    isUnitOfMeasure.value = false;
  }, 200);
};

const stockInDateInputHandler = (event) => {
  const value = event.target.value;
  form.value.stockin_date = value;
};

const openingStockQtyInputHandler = (event) => {
  const value = event.target.value;
  form.value.stockin_quantity = value;
};

const purchaseCostInputHandler = (event) => {
  const value = event.target.value;
  form.value.stockin_purchase_cost = value;
};
const supplierTinInputHandler = (event) => {
  const value = event.target.value;
  form.value.stockin_supplier_tin = value;
};

const nameOfSupplierInputHandler = (event) => {
  const value = event.target.value;
  form.value.stockin_supplier = value;
};

const remarksInputHandler = (event) => {
  const value = event.target.value;
  form.value.stockin_remarks = value;
};

//filter unit of measures
const filteredUnitOfMeasure = computed(() => {
  let unitList = measureUnits.value?.data?.rateUnit?.map((unit) => ({
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
  console.log(event);
  selected_unit_of_measure.value = event;
  form.value.stockin_measureUnit = id;
  hideUnitOfMeasure();
}

const unitOfMeasureInputHandler = (event) => {
  console.log(event.target.value);
  const value = event.target.value;
  searched_unit_of_measure.value = value;
};

//  =======================end currency fileter section===============
</script>

<style scoped>
.required {
  @apply bg-red-400;
}
</style>
