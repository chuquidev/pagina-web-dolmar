<script setup lang="ts">
import { ref, watch } from 'vue'
import Modal from './Modal.vue'
import { adminFaqsService } from '@/services/admin/faqs.service'
import type { Faq } from '@/types/catalog'

const props = defineProps<{ faq: Faq | null }>()
const emit = defineEmits<{ close: []; saved: [] }>()

const question = ref('')
const answer = ref('')
const order = ref(0)
const isActive = ref(true)
const saving = ref(false)
const error = ref('')

watch(
    () => props.faq,
    (faq) => {
        question.value = faq?.question ?? ''
        answer.value = faq?.answer ?? ''
        order.value = faq?.order ?? 0
        isActive.value = faq?.is_active ?? true
    },
    { immediate: true }
)

async function submit() {
    saving.value = true
    error.value = ''
    try {
        const payload = { question: question.value, answer: answer.value, order: order.value, is_active: isActive.value }
        if (props.faq) {
            await adminFaqsService.update(props.faq.id, payload)
        } else {
            await adminFaqsService.create(payload)
        }
        emit('saved')
    } catch {
        error.value = 'No se pudo guardar la pregunta.'
    } finally {
        saving.value = false
    }
}
</script>

<template>
    <Modal :title="faq ? 'Editar pregunta' : 'Nueva pregunta'" size="lg" @close="emit('close')">
        <form class="space-y-4" @submit.prevent="submit">
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pregunta</label>
                <input v-model="question" type="text" required
                    class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Respuesta</label>
                <textarea v-model="answer" rows="4" required
                    class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Orden</label>
                <input v-model.number="order" type="number" min="0"
                    class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input v-model="isActive" type="checkbox"
                    class="rounded border-gray-300 text-brand-primary dark:border-gray-600 dark:bg-gray-800" />
                Pregunta activa
            </label>

            <p v-if="error" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    @click="emit('close')">
                    Cancelar
                </button>
                <button type="submit" :disabled="saving"
                    class="rounded-lg bg-brand-primary px-4 py-2 text-sm font-semibold text-white hover:brightness-110 disabled:opacity-60">
                    {{ saving ? 'Guardando...' : 'Guardar' }}
                </button>
            </div>
        </form>
    </Modal>
</template>