<script setup>
defineProps({
  type: {
    type: String,
    default: "submit",
  },
});
</script>

<template>
  <button
    :type="type"
    class="d-flex items-center px-4 py-2 btn bg-primary border-transparent text-white uppercase hover:bg-green-700 active:bg-teal-900 focus:outline-none focus:border-teal-900 focus:shadow-outline-teal transition ease-in-out duration-150"
  >
    <slot />
  </button>
</template>
