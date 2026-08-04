<script setup lang="ts">
withDefaults(
    defineProps<{
        label: string
        value: string | number
        icon: object
        iconBg?: string
        iconColor?: string
    }>(),
    { iconBg: 'bg-brand-primary/10', iconColor: 'text-brand-primary' }
)
</script>

<template>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-3">
            <div class="rounded-lg p-2.5" :class="[iconBg, iconColor]">
                <component :is="icon" class="h-5 w-5" />
            </div>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ label }}</span>
        </div>
        <p class="mt-3 font-display text-2xl font-bold text-gray-900 dark:text-gray-100">{{ value }}</p>
    </div>
</template>