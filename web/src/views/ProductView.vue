<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Share2, Check, ArrowLeft } from '@lucide/vue'
import { catalogService } from '@/services/catalog.service'
import { useSettingsStore } from '@/stores/settings'
import { buildWhatsAppUrl } from '@/utils/whatsapp'
import { canGoBack } from '@/router'
import ProductGallery from '@/components/ProductGallery.vue'
import PriceTag from '@/components/PriceTag.vue'
import AvailabilityBadge from '@/components/AvailabilityBadge.vue'
import type { Product } from '@/types/catalog'
import { useHead } from '@unhead/vue'

const props = defineProps<{ slug: string }>()
const settingsStore = useSettingsStore()
const router = useRouter()

const product = ref<Product | null>(null)
const notFound = ref(false)
const copied = ref(false)

useHead(() => ({
    title: product.value ? `${product.value.name} — ${settingsStore.settings?.store_name ?? ''}` : 'Producto',
    meta: [
        { name: 'description', content: product.value?.description?.slice(0, 160) ?? 'Consulta este producto por WhatsApp.' },
        { property: 'og:title', content: product.value?.name },
        { property: 'og:description', content: product.value?.description?.slice(0, 160) },
        { property: 'og:image', content: product.value?.images[0]?.large },
        { property: 'og:type', content: 'product' },
    ],
}))

async function loadProduct() {
    notFound.value = false
    try {
        product.value = await catalogService.getProductBySlug(props.slug)
    } catch {
        notFound.value = true
    }
}

function goBack() {
    if (canGoBack()) {
        router.back()
    } else {
        router.push('/catalogo')
    }
}

const whatsappUrl = computed(() => {
    if (!product.value || !settingsStore.settings?.whatsapp_number) return null
    return buildWhatsAppUrl(
        settingsStore.settings.whatsapp_number,
        `Hola, me interesa este producto: ${product.value.name}.\n${window.location.href}`
    )
})

async function share() {
    const url = window.location.href
    if (navigator.share) {
        await navigator.share({ title: product.value?.name, url })
    } else {
        await navigator.clipboard.writeText(url)
        copied.value = true
        setTimeout(() => (copied.value = false), 2000)
    }
}

onMounted(loadProduct)
watch(() => props.slug, loadProduct)
</script>

<template>
    <div class="mx-auto max-w-5xl px-4 py-6 sm:py-10">
        <button
            class="mb-4 flex items-center gap-1.5 text-sm font-medium  text-gray-600 hover:text-brand-primary dark:text-gray-300 sm:mb-6"
            @click="goBack">
            <ArrowLeft class="h-4 w-4" />
            Volver
        </button>

        <div v-if="notFound" class="py-24 text-center text-gray-400 dark:text-gray-500">Este producto ya no está
            disponible.</div>

        <div v-else-if="product" class="grid gap-8 md:grid-cols-2 md:gap-10">
            <ProductGallery :images="product.images" :alt="product.name" :price="product.price"
                :sale-price="product.sale_price" />

            <div>
                <span class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">
                    {{ product.category.name }}<template v-if="product.brand"> · {{ product.brand.name }}</template>
                </span>
                <h1 class="mt-1 font-display text-2xl font-bold text-gray-900 dark:text-gray-100 sm:text-3xl">{{
                    product.name }}</h1>

                <div class="mt-4">
                    <PriceTag :price="product.price" :sale-price="product.sale_price" />
                </div>

                <AvailabilityBadge :availability="product.availability" class="mt-4" />

                <p v-if="product.description" class="mt-6 leading-relaxed text-gray-600 dark:text-gray-400">{{
                    product.description }}
                </p>

                <ul v-if="product.features?.length" class="mt-6 space-y-2">
                    <li v-for="(feature, i) in product.features" :key="i"
                        class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-brand-primary"></span>
                        {{ feature }}
                    </li>
                </ul>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a v-if="whatsappUrl" :href="whatsappUrl" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 rounded-full bg-[#25D366] px-6 py-3 font-display
                    font-semibold
                    text-white transition hover:brightness-95">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor">
                            <path
                                d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.79.47 3.43 1.29 4.9L2 22l5.29-1.39c1.4.76 3 1.2 4.7 1.2h.01c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm0 18.05h-.01c-1.6 0-3.15-.43-4.5-1.24l-.32-.19-3.13.82.84-3.05-.21-.32a8.02 8.02 0 0 1-1.24-4.26c0-4.46 3.63-8.09 8.09-8.09 4.46 0 8.09 3.63 8.09 8.09 0 4.46-3.63 8.05-8.11 8.05zm4.44-6.02c-.24-.12-1.44-.71-1.66-.79-.22-.08-.38-.12-.55.12-.16.24-.62.79-.76.95-.14.16-.28.18-.52.06-.24-.12-1.01-.37-1.92-1.18-.71-.63-1.19-1.41-1.33-1.65-.14-.24-.02-.37.1-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.55-1.33-.76-1.82-.2-.48-.4-.42-.55-.42-.14 0-.3-.02-.46-.02s-.42.06-.64.3c-.22.24-.85.83-.85 2.02 0 1.19.87 2.34 1 2.5.12.16 1.71 2.61 4.15 3.66.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.44-.59 1.64-1.16.2-.57.2-1.06.14-1.16-.06-.1-.22-.16-.46-.28z" />
                        </svg>
                        Consultar por WhatsApp
                    </a>

                    <button
                        class="flex items-center justify-center gap-2 rounded-full border border-gray-200 px-6 py-3 font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                        @click="share">
                        <Check v-if="copied" class="h-5 w-5 text-green-600" />
                        <Share2 v-else class="h-5 w-5" />
                        {{ copied ? 'Enlace copiado' : 'Compartir' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>