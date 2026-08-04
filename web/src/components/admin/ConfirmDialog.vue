<script setup lang="ts">
import Modal from './Modal.vue'

defineProps<{
    title: string
    message: string
}>()
const emit = defineEmits<{ confirm: []; cancel: [] }>()
</script>

<template>
    <Modal :title="title" @close="emit('cancel')">
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ message }}</p>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button"
                class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                @click="emit('cancel')">
                Cancelar
            </button>
            <button type="button"
                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                @click="emit('confirm')">
                Eliminar
            </button>
        </div>
    </Modal>
</template>