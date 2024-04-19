<template>
    <div class="modal" :class="{ 'is-active': isActive }">
        <div class="modal-background" @click="closeModal"></div>
        <div class="modal-content">
            <slot></slot>
        </div>
        <button class="bg-blue-400" aria-label="close" @click="closeModal"></button>
    </div>
</template>

<script>
import { ref } from 'vue';

export default {
    name: 'Modal',
    props: {
        isOpen: {
            type: Boolean,
            default: false,
        },
    },
    setup(props, { emit }) {
        const isActive = ref(props.isOpen);

        const closeModal = () => {
            isActive.value = false;
            emit('update:isOpen', false);
        };

        const openModal = () => {
            isActive.value = true;
            emit('update:isOpen', true);
        };

        return {
            isActive,
            closeModal,
            openModal,
        };
    },
};
</script>

<style scoped>
.modal {
    display: none;
}

.modal.is-active {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.5);
    padding: 20px;
    max-width: 500px;
    width: 100%;
}

.modal-close {
    position: absolute;
    top: 10px;
    right: 10px;
}
</style>
