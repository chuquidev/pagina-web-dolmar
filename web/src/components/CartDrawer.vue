<script setup lang="ts">
import { computed } from 'vue'
import { X, Plus, Minus, Trash2, Package } from '@lucide/vue'
import { useCartStore } from '@/stores/cart'
import { useSettingsStore } from '@/stores/settings'
import { buildWhatsAppUrl } from '@/utils/whatsapp'
import { formatCurrency } from '@/utils/currency'

const emit = defineEmits<{ close: [] }>()
const cartStore = useCartStore()
const settingsStore = useSettingsStore()

const whatsappUrl = computed(() => {
    const phone = settingsStore.settings?.whatsapp_number
    if (!phone || !cartStore.items.length) return null

    const lines = cartStore.items.map((item, i) => {
        const price = formatCurrency(item.product.sale_price ?? item.product.price)
        const url = `${window.location.origin}/producto/${item.product.slug}`
        return `${i + 1}. ${item.product.name} (x${item.quantity}) — ${price}\n${url}`
    })

    const message = [
        'Hola, me interesan estos productos:',
        '',
        ...lines,
        '',
        `Total aproximado: ${formatCurrency(cartStore.totalPrice)}`,
    ].join('\n')

    return buildWhatsAppUrl(phone, message)
})
</script>

<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-[90] bg-black/40" @click="emit('close')"></div>

        <aside class="fixed inset-y-0 right-0 z-[91] flex w-full max-w-sm flex-col bg-white shadow-xl dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-4 dark:border-gray-800">
                <h2 class="font-display text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Tu carrito {{ cartStore.totalItems ? `(${cartStore.totalItems})` : '' }}
                </h2>
                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" aria-label="Cerrar"
                    @click="emit('close')">
                    <X class="h-5 w-5" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-4 py-4">
                <div v-if="!cartStore.items.length"
                    class="flex h-full flex-col items-center justify-center text-center text-gray-400 dark:text-gray-500">
                    <Package class="h-10 w-10" />
                    <p class="mt-3 text-sm">Tu carrito está vacío.</p>
                </div>

                <div v-else class="space-y-4">
                    <div v-for="item in cartStore.items" :key="item.product.id" class="flex gap-3">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800">
                            <img v-if="item.product.images[0]" :src="item.product.images[0].thumb"
                                class="h-full w-full object-cover" />
                            <div v-else
                                class="flex h-full w-full items-center justify-center text-gray-300 dark:text-gray-600">
                                <Package class="h-6 w-6" />
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{
                                item.product.name }}</p>
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                {{ formatCurrency(item.product.sale_price ?? item.product.price) }}
                            </p>

                            <div class="mt-2 flex items-center gap-2">
                                <button
                                    class="flex h-7 w-7 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800"
                                    @click="cartStore.updateQuantity(item.product.id, item.quantity - 1)">
                                    <Minus class="h-3.5 w-3.5" />
                                </button>
                                <span class="w-6 text-center text-sm text-gray-900 dark:text-gray-100">{{ item.quantity
                                }}</span>
                                <button
                                    class="flex h-7 w-7 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800"
                                    @click="cartStore.updateQuantity(item.product.id, item.quantity + 1)">
                                    <Plus class="h-3.5 w-3.5" />
                                </button>
                                <button class="ml-auto text-gray-400 hover:text-red-500" aria-label="Quitar"
                                    @click="cartStore.removeItem(item.product.id)">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="cartStore.items.length" class="border-t border-gray-200 px-4 py-4 dark:border-gray-800">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Total aproximado</span>
                    <span class="font-display text-lg font-semibold text-gray-900 dark:text-gray-100">{{
                        formatCurrency(cartStore.totalPrice) }}</span>
                </div>

                <a v-if="whatsappUrl" :href="whatsappUrl" target="_blank" rel="noopener" class="mt-3 flex items-center justify-center gap-2 rounded-full bg-[#25D366] px-6 py-3 font-display
                font-semibold text-white transition hover:brightness-95">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor">
                        <path
                            d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.79.47 3.43 1.29 4.9L2 22l5.29-1.39c1.4.76 3 1.2 4.7 1.2h.01c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm0 18.05h-.01c-1.6 0-3.15-.43-4.5-1.24l-.32-.19-3.13.82.84-3.05-.21-.32a8.02 8.02 0 0 1-1.24-4.26c0-4.46 3.63-8.09 8.09-8.09 4.46 0 8.09 3.63 8.09 8.09 0 4.46-3.63 8.05-8.11 8.05zm4.44-6.02c-.24-.12-1.44-.71-1.66-.79-.22-.08-.38-.12-.55.12-.16.24-.62.79-.76.95-.14.16-.28.18-.52.06-.24-.12-1.01-.37-1.92-1.18-.71-.63-1.19-1.41-1.33-1.65-.14-.24-.02-.37.1-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.55-1.33-.76-1.82-.2-.48-.4-.42-.55-.42-.14 0-.3-.02-.46-.02s-.42.06-.64.3c-.22.24-.85.83-.85 2.02 0 1.19.87 2.34 1 2.5.12.16 1.71 2.61 4.15 3.66.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.44-.59 1.64-1.16.2-.57.2-1.06.14-1.16-.06-.1-.22-.16-.46-.28z" />
                    </svg>
                    Consultar por WhatsApp
                </a>

                <button class="mt-2 w-full text-center text-xs text-gray-400 hover:text-red-500 dark:text-gray-500"
                    @click="cartStore.clear()">
                    Vaciar carrito
                </button>
            </div>
        </aside>
    </Teleport>
</template>