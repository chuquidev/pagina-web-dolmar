<script setup lang="ts">
import { computed } from 'vue'
import { ChevronLeft, ChevronRight } from '@lucide/vue'

const props = defineProps<{ currentPage: number; lastPage: number; total?: number }>()
const emit = defineEmits<{ change: [page: number] }>()

// Ventana de páginas visibles: primera, última, actual ±1, con "…" en los huecos.
// Evita renderizar un botón por página cuando hay decenas de páginas.
const pages = computed<(number | '...')[]>(() => {
    const last = props.lastPage
    const current = props.currentPage
    if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1)

    const result: (number | '...')[] = [1]
    if (current > 3) result.push('...')

    const start = Math.max(2, current - 1)
    const end = Math.min(last - 1, current + 1)
    for (let p = start; p <= end; p++) result.push(p)

    if (current < last - 2) result.push('...')
    result.push(last)
    return result
})

function go(page: number) {
    if (page < 1 || page > props.lastPage || page === props.currentPage) return
    emit('change', page)
}
</script>

<template>
    <div v-if="lastPage > 1" class="mt-6 flex flex-wrap items-center justify-between gap-3">
        <p v-if="total !== undefined" class="text-xs text-gray-400 dark:text-gray-500">
            Página {{ currentPage }} de {{ lastPage }} · {{ total }} resultado{{ total === 1 ? '' : 's' }}
        </p>
        <div class="flex flex-wrap items-center gap-1.5">
            <button type="button" :disabled="currentPage === 1"
                class="flex h-9 w-9 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 disabled:pointer-events-none disabled:opacity-30 dark:text-gray-400 dark:hover:bg-gray-800"
                aria-label="Página anterior" @click="go(currentPage - 1)">
                <ChevronLeft class="h-4 w-4" />
            </button>

            <template v-for="(page, i) in pages" :key="i">
                <span v-if="page === '...'" class="w-9 text-center text-sm text-gray-400 dark:text-gray-600">…</span>
                <button v-else type="button" class="h-9 w-9 rounded-full text-sm transition" :class="page === currentPage
                    ? 'bg-brand-primary font-semibold text-white'
                    : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'" @click="go(page)">
                    {{ page }}
                </button>
            </template>

            <button type="button" :disabled="currentPage === lastPage"
                class="flex h-9 w-9 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 disabled:pointer-events-none disabled:opacity-30 dark:text-gray-400 dark:hover:bg-gray-800"
                aria-label="Página siguiente" @click="go(currentPage + 1)">
                <ChevronRight class="h-4 w-4" />
            </button>
        </div>
    </div>
</template>