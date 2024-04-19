<script setup>
import { defineProps, defineEmits, ref, reactive, computed, inject } from "vue";

const props = defineProps({
  type: {
    type: String,
    required: false,
    default: "text",
  },

  name: {
    type: String,
    required: false,
    default: null,
  },

  required: {
    type: Boolean,
    required: false,
    default: false,
  },

  value: {
    type: String,
    required: false,
    default: null,
  },

  min: {
    type: String,
    required: false,
    default: null,
  },

  max: {
    type: String,
    required: false,
    default: null,
  },

  placeholder: {
    type: String,
    required: false,
    default: "",
  },

  status: {
    type: String,
    required: false,
    default: "normal",
  },

  messages: {
    type: Array,
    required: false,
    default: null,
  },

  prefix: {
    type: String,
    required: false,
    default: null,
  },

  suffix: {
    type: String,
    required: false,
    default: null,
  },

  validation: {
    type: Function,
    required: false,
    default: null,
  },
  onFocus: { type: Function, required: false, default: null },
  onBlur: { type: Function, required: false, default: null },
});

const compValue = ref(props.value);
const focused = ref(false);
// const filterCodes = inject("filterCodes");

const emit = defineEmits({
  input: (event) => event,
  focus: (event) => event,
});

const inputHandler = (event) => {
  emit("input", event);
  compValue.value = event.target.value;
};

const updateFocus = (value) => (focused.value = value);

const blurHandler = (event) => {
  event.preventDefault();
  setTimeout(() => {
    updateFocus(false);
    props.validation ? props.validation(event.target.value) : null;
    props.onBlur ? props.onBlur() : null;
    event.target.blur();
  }, 200);
};

const focusHandler = (event) => {
  updateFocus(true);
  emit("focus", event);
};

// const keyDownHandler = (event) => {
//   if (compValue.value !== null) filterCodes(event, compValue.value);
// };

const normal = computed(
  () => focused.value === true && props.status === "normal",
);
const success = computed(
  () =>
    (focused.value === true && props.status === "success") ||
    props.status === "success",
);
const warning = computed(
  () =>
    (focused.value === true && props.status === "warning") ||
    props.status === "warning",
);
const error = computed(
  () =>
    (focused.value === true && props.status === "error") ||
    props.status === "error",
);

const classObj = reactive({
  "border-yellow-650/80": normal,
  "border-green-400/80": success,
  "border-yellow-400/80": warning,
  "border-red-400/80": error,
  "transform transition duration-500": true,
  "w-full overflow-hidden rounded-md border-[0.125rem]": true,
  "flex items-center": true,
});

const messageClassObj = reactive({
  "px-1 capitalize flex flex-col pt-2": true,
  "text-green-400/80": success,
  "text-yellow-400/80": warning,
  "text-red-400/80": error,
});

const fixObj =
  "w-[1.7rem] h-full flex justify-center items-center p-1 text-[20px] group";

const iconColor = computed(() =>
  focused.value ? " text-yellow-650/80" : " text-[#9d9c9c]",
);
</script>

<template>
  <div class="py-1 w-full">
    <div :class="classObj">
      <!-- Prefix -->
      <div v-if="prefix" :class="fixObj">
        <i :class="prefix + iconColor"></i>
      </div>

      <!-- the input field -->
      <input
        class="number-input form-control-sm form-control"
        @focus="focusHandler"
        @blur="blurHandler"
        :type="type"
        :name="name"
        :placeholder="placeholder"
        :required="required"
        :value="value ?? compValue"
        :min="min"
        :max="max"
        @input="inputHandler"
        @keydown="keyDownHandler"
      />

      <!-- Suffix -->
      <div v-if="suffix" :class="fixObj">
        <i :class="suffix + iconColor"></i>
      </div>
    </div>

    <!-- Status message -->
    <div v-if="messages?.length !== 0" :class="messageClassObj">
      <small v-for="(message, index) in messages" :key="index">{{
        message
      }}</small>
    </div>
  </div>
</template>

<style scoped>
.number-input::-webkit-outer-spin-button,
.number-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.number-input[type="number"] {
  -moz-appearance: textfield;
}

.number-input {
  @apply w-full p-2 border-gray-200  bg-gray-200 outline-none rounded-md text-gray-500 placeholder-gray-400 sm:text-base;
}

i {
  color: inherit;
  @apply transform transition duration-500;
}
</style>
