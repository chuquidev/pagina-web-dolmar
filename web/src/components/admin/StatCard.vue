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
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 sm:p-5">
        <div class="flex items-center gap-2.5 sm:gap-3">
            <div class="shrink-0 rounded-lg p-2" :class="[iconBg, iconColor]">
                <component :is="icon" class="h-4 w-4 sm:h-5 sm:w-5" />
            </div>
            <span class="text-xs font-medium leading-tight text-gray-500 dark:text-gray-400 sm:text-sm">{{ label
                }}</span>
        </div>
        <p class="mt-2.5 font-display text-xl font-bold text-gray-900 dark:text-gray-100 sm:mt-3 sm:text-2xl">{{ value
            }}</p>
    </div>
</template>