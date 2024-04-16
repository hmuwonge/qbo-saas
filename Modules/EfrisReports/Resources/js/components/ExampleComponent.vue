<template>
    <div class="container">
        <div class="row ">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">Efris Invoices</div>

                    <div class="card-body">
                        <!-- I'm an example component. Efris -->
                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg p-4">
                            <div class="d-flex row justify-end py-2 ml-4">

                                <div class="d-flex row mx-auto">
                                    <div class="w-96 ">
                                        <VueDatePicker v-model="form.dateRange" position="center" multi-calendars
                                            multi-calendars-solo range :preset-ranges="presetRanges"
                                            :placeholder="'Select Date Range'">
                                            <template #yearly="{ label, range, presetDateRange }">
                                                <span @click="presetDateRange(range)">{{ label }}</span>
                                            </template>
                                        </VueDatePicker>
                                    </div>

                                    <div class="relative border-transparent">
                                        <system-input type="text" :class="'w-[20rem] mx-2'"
                                            :placeholder="'search for other unit of measure'" @input="searchInputHandler" />
                                    </div>
                                    <div class="mx-8">
                                        <primary-button type="button" class="text-white items-center w-40
                                                            font-medium rounded-md text-sm px-5
                                             py-3 mb-2 focus:outline-none" @click="getEfrisInvoices()">
                                            Fetch
                                        </primary-button>
                                    </div>

                                    <div>
                                        <SecondaryButton @click="downloadReport" class="text-white  py-3 bg-gray-800
                                            font-medium rounded-md text-lg px-5  ">
                                            Download Report
                                        </SecondaryButton>

                                    </div>

                                </div>

                            </div>

                            <div class="p-4 mb-4 text-white bg-green-500  w-auto mx-2 rounded-md
                                        " v-if="invoicesData?.length > 0">
                                Found
                                <span class="text-lg font-extrabold">{{
                                    invoicesData.length
                                }}</span>
                                Fiscal Invoices. Start
                                <b>{{ form.dateRange[0] }}</b> End Date:<b>{{
                                    form.dateRange[1]
                                }}</b>
                                Customer Name: <b>{{ form.buyerLegalName }}</b>
                            </div>

                            <div class="flex justify-between items-center pb-4">
                                <div class="p-2 d-none">
                                    <button id="dropdownRadioButton" data-dropdown-toggle="dropdownRadio"
                                        class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                                        type="button">
                                        <svg class="mr-2 w-4 h-4 text-gray-400" aria-hidden="true" fill="currentColor"
                                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Show Pages
                                        <svg class="ml-2 w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                </div>
                            </div>

                            <div class="bg-white rounded-md md:h-[calc(100vh-30rem)] overflow-x-auto">
                                <table class="w-full text-left text-black table table-responsive rounded-md table-striped"
                                    v-if="!loading">
                                    <thead class="text-base text-black uppercase bg-gray-300 rounded-md">
                                        <tr class="">
                                            <th scope="col" class="p-4">
                                                FDN
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Invoice No
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Date issued
                                            </th>

                                            <th scope="col" class="px-6 py-3">
                                                Customer
                                            </th>

                                            <th scope="col" class="px-6 py-3">
                                                Currency
                                            </th>

                                            <th scope="col" class="px-6 py-3">
                                                Gross Amount
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Tax Amountt
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Download
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class=" hover:bg-gray-50 " v-for="invoice in invoicesData" :key="invoice.id">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900  whitespace-nowrap">
                                                {{ invoice.invoiceNo }}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ invoice.referenceNo }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{
                                                    invoice.issuedDate
                                                }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{
                                                    invoice.buyerBusinessName
                                                }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ invoice.currency }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ invoice.grossAmount }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ invoice.taxAmount }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <span class="flex">
                                                    <a :href="`fiscal-invoice-download/${invoice.invoiceNo}`" class="text-white bg-gray-500 hover:bg-green-900
                                                            font-normal rounded-sm text-sm px-3 py-1 mr-2 mb-2
                                                            " target="_blank">
                                                        <DownloadIcon class="w-5 h-5 text-white" />
                                                    </a>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="flex justify-center items-center mt-[10vh]" v-else-if="loading">
                                    <div>
                                        <Loader></Loader>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center align-items-center flex-column mb-3" v-if="invoicesData?.length === 0 &&
                                    !loading
                                    ">
                                    <!-- <img src="../components/shared/nodata.svg" class="w-25 h-25 mt-5" /> -->
                                    <p class="mt-3 text-black">
                                        No Efris Invoices Data Available
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-row my-4">
                                <div>
                                    <!--                        <EfrisPagination :totalRecords="efrisData?.totalSize" :pageSize="efrisData?.pageSize"-->
                                    <!--                            :currentPage="efrisData?.pageNo" @getItems="getItems">-->
                                    <!--                        </EfrisPagination>-->
                                </div>
                                <div>
                                    <form @submit.prevent>
                                        <div class="relative">
                                            <input type="number" v-model="form.pageNo"
                                                class="bg-gray-50 font-sans border h-9 focus:border-2 transition ease-in-out rounded-md ml-10 p-2 border-teal-600"
                                                placeholder="Enter Go To page no">
                                            <span class="absolute inset-y-0 right-0 flex items-center pl-2">
                                                <button type="submit" class="p-1 focus:outline-none focus:shadow-outline"
                                                    @click="getPageData()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" aria-hidden="true"
                                                        class="w-5 h-5 hover:text-orange-6000">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </span>
                                        </div>
                                    </form>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { endOfMonth, endOfYear, startOfMonth, startOfYear, subDays, subMonths, endOfWeek, startOfWeek } from 'date-fns';
import { ref, computed, onMounted } from "vue";
import moment from "moment";
import Swal from "sweetalert2";
const props = defineProps({
    invoices: {
        type: Object,
        default: {},
    },
});

const startDate = new Date();
const toDate = new Date(new Date().setDate(startDate.getDate() - 30));

const form = ref({
    dateRange: [toDate, startDate],
    invoiceNo: "",
    buyerLegalName: "",
    invoiceType: "",
    invoiceKind: "",
    endDate: "",
    pageNo: "",
    pageSize: "",
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

const loading = ref(false);
let invoicesData = ref([]);
const formatDate = (dateString) => {
    let newDate = moment(dateString);
    return newDate.format("MMM Do YYYY");
};

const searchInputHandler = (event) => {
    console.log(event.target.value);
    const value = event.target.value;
    form.value.buyerLegalName = value;
};

async function getEfrisInvoices(page) {

loading.value = true;
const formValues = form.value;
console.log('date range default', form.value.dateRange)

const fromDate = moment(formValues.dateRange[0]).format("YYYY-MM-DD");
const toDate = moment(formValues.dateRange[1]).format("YYYY-MM-DD");

let requestData = {
    customer_name: formValues.buyerLegalName,
    invoiceType: "",
    invoiceKind: "",
    invoice_period: fromDate + " " + "to" + " " + toDate,
    pageNo: page,
    pageSize: 99,
};

await axios
    .post("/api/efrisreports/fiscalised-invoices", requestData)
    .then((response) => {
        if (response.status === 200) {
            loading.value = false;
            let data = response.data.data;
            console.log(data.records)
            const { returnCode, returnMessage } = response.data?.status;
            if (returnCode === '00') {
                invoicesData.value = data.records
            }

            if (returnCode !== '00') {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: `${returnMessage}`,
                });
            }
        }
    })
    .catch((e) => {
        // this.error = false
        console.log(e);
    })
    .finally(() => {
        loading.value = false;
        // this.efrisError = false
    });
}

onMounted(() => {
getEfrisInvoices()

})
</script>