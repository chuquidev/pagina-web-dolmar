<script setup lang="ts">
import { X } from '@lucide/vue'

withDefaults(defineProps<{ title: string; size?: 'md' | 'lg' }>(), { size: 'md' })
const emit = defineEmits<{ close: [] }>()
</script>

<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click.self="emit('close')">
            <div class="max-h-[90vh] w-full overflow-y-auto rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900"
                :class="size === 'lg' ? 'max-w-2xl' : 'max-w-md'">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-lg font-semibold text-gray-900 dark:text-gray-100">{{ title }}</h2>
                    <button class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                        @click="emit('close')">
                        <X class="h-5 w-5" />
                    </button>
                </div>
                <div class="mt-4">
                    <slot />
                </div>
            </div>
        </div>
    </Teleport>
</template>