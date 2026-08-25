<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    data: { label: string; value: number; color: string }[]
}>()

const total = computed(() => props.data.reduce((sum, d) => sum + d.value, 0))

const segments = computed(() => {
    const radius = 40
    const circumference = 2 * Math.PI * radius
    let offset = 0
    return props.data.map((d) => {
        const fraction = total.value > 0 ? d.value / total.value : 0
        const dash = fraction * circumference
        const segment = {
            ...d,
            dasharray: `${dash} ${circumference - dash}`,
            dashoffset: -offset,
            percent: total.value > 0 ? Math.round(fraction * 100) : 0,
        }
        offset += dash
        return segment
    })
})
</script>

<template>
    <div v-if="total > 0" class="flex flex-col items-center gap-4 sm:flex-row sm:gap-6">
        <svg viewBox="0 0 100 100" class="h-28 w-28 shrink-0 -rotate-90 sm:h-32 sm:w-32">
            <circle cx="50" cy="50" r="40" fill="none" stroke-width="14" class="stroke-gray-100 dark:stroke-gray-800" />
            <circle v-for="(segment, i) in segments" :key="i" cx="50" cy="50" r="40" fill="none" :stroke="segment.color"
                stroke-width="14" :stroke-dasharray="segment.dasharray" :stroke-dashoffset="segment.dashoffset"
                class="transition-all duration-500" />
        </svg>
        <ul class="w-full space-y-2 text-sm sm:w-auto">
            <li v-for="(segment, i) in segments" :key="i" class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 shrink-0 rounded-full" :style="{ backgroundColor: segment.color }"></span>
                <span class="text-gray-600 dark:text-gray-300">{{ segment.label }}</span>
                <span class="font-medium text-gray-900 dark:text-gray-100">{{ segment.value }}</span>
                <span class="text-gray-400 dark:text-gray-500">({{ segment.percent }}%)</span>
            </li>
        </ul>
    </div>
    <p v-else class="text-sm text-gray-400 dark:text-gray-500">Aún no hay productos para mostrar.</p>
</template>