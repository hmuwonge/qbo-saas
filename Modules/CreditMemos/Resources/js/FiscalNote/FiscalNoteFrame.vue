<template>
  <div class="flex-grow-1 container-fluid">
    <div class="row invoice-preview">
      <div class="col-xl-10 col-md-8 col-12 mb-md-0 mb-4">
        <div class="card invoice-preview-card">
          <div class="rounded-md drop-shadow-md">
            <fiscalise-credit-note
              v-if="fiscalForm === true"
              :creditdata="creditmemo"
              :reasons="reasons"
            />
            <invoice-summary
              v-if="isSummary === true"
              :doc="creditmemo?.originalInvoice"
            />
          </div>
        </div>
      </div>

      <div class="col-xl-2 col-md-4 col-12 invoice-actions">
        <div class="card">
          <div class="card-body">
            <button
              class="btn btn-primary d-grid w-100 mb-3"
              :class="{
                'btn btn-secondary d-grid w-100 mb-3': isSummary === true,
              }"
              @click="fiscalNoteView"
            >
              <span
                class="d-flex align-items-center justify-content-center text-nowrap"
                ><i class="bx bx-paper-plane bx-xs me-1"></i>Invoice
                Summary</span
              >
            </button>

            <button
              class="btn btn-secondary d-grid w-100"
              :class="{
                'btn-primary rounded': fiscalForm === true,
              }"
              @click="fiscaliseInvoice"
            >
              Fiscalise Invoice
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import FiscaliseCreditNote from "./FiscaliseCreditNote.vue";
import InvoiceSummary from "./InvoiceSummary.vue";

const isSummary = ref(true);
const fiscalForm = ref(false);

const props = defineProps({
  creditmemo: {
    type: Object,
    default: () => {},
  },
  reasons: {
    type: Array,
    default: [],
  },
});

console.log("creditnote::", props.creditmemo);
const fiscalNoteView = () => {
  isSummary.value = true;
  fiscalForm.value = false;
};

const fiscaliseInvoice = () => {
  isSummary.value = false;
  fiscalForm.value = true;
};
</script>

<style scoped></style>
