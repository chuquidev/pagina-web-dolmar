<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { X } from '@lucide/vue'
import { catalogService, type ProductFilters } from '@/services/catalog.service'
import { useCatalogStore } from '@/stores/catalog'
import ProductCard from '@/components/ProductCard.vue'
import type { Product } from '@/types/catalog'

const route = useRoute()
const router = useRouter()
const catalogStore = useCatalogStore()

const products = ref<Product[]>([])
const currentPage = ref(1)
const lastPage = ref(1)
const loading = ref(false)

const sortOptions = [
    { value: 'newest', label: 'Más recientes' },
    { value: 'price_asc', label: 'Precio: menor a mayor' },
    { value: 'price_desc', label: 'Precio: mayor a menor' },
    { value: 'name', label: 'Nombre A-Z' },
]

async function loadProducts() {
    loading.value = true
    const response = await catalogService.getProducts({
        category: (route.query.category as string) || undefined,
        brand: (route.query.brand as string) || undefined,
        search: (route.query.search as string) || undefined,
        sort: (route.query.sort as ProductFilters['sort']) || 'newest',
        page: Number(route.query.page) || 1,
    })
    products.value = response.data
    currentPage.value = response.meta.current_page
    lastPage.value = response.meta.last_page
    loading.value = false
}

function updateFilter(partial: Record<string, string | undefined>) {
    router.push({ query: { ...route.query, ...partial, page: undefined } })
}

function updatePage(page: number) {
    router.push({ query: { ...route.query, page } })
}

watch(() => route.query, loadProducts, { immediate: true, deep: true })
</script>

<template>
    <div class="mx-auto max-w-[1400px] px-4 py-6 sm:py-8">
        <h1 class="font-display text-xl font-bold text-gray-900 dark:text-gray-100 sm:text-2xl">Catálogo</h1>

        <div class="mt-5 flex flex-wrap items-center gap-2 sm:mt-6 sm:gap-3">
            <select :value="route.query.category ?? ''"
                class="rounded-full border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:px-4"
                @change="updateFilter({ category: ($event.target as HTMLSelectElement).value || undefined })">
                <option value="">Todas las categorías</option>
                <option v-for="c in catalogStore.categories" :key="c.id" :value="c.slug">{{ c.name }}</option>
            </select>

            <select :value="route.query.brand ?? ''"
                class="rounded-full border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:px-4"
                @change="updateFilter({ brand: ($event.target as HTMLSelectElement).value || undefined })">
                <option value="">Todas las marcas</option>
                <option v-for="b in catalogStore.brands" :key="b.id" :value="b.slug">{{ b.name }}</option>
            </select>

            <select :value="route.query.sort ?? 'newest'"
                class="rounded-full border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:px-4"
                @change="updateFilter({ sort: ($event.target as HTMLSelectElement).value })">
                <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>

            <span v-if="route.query.search"
                class="flex items-center gap-2 rounded-full bg-gray-100 px-4 py-2 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                "{{ route.query.search }}"
                <button class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                    @click="updateFilter({ search: undefined })">
                    <X class="h-3.5 w-3.5" />
                </button>
            </span>
        </div>

        <div v-if="loading" class="py-24 text-center text-gray-400 dark:text-gray-500">Cargando productos...</div>
        <div v-else-if="!products.length" class="py-24 text-center text-gray-400 dark:text-gray-500">
            No encontramos productos con esos filtros.
        </div>

        <div v-else class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 xl:grid-cols-5">
            <ProductCard v-for="product in products" :key="product.id" :product="product" />
        </div>

        <div v-if="lastPage > 1" class="mt-8 flex flex-wrap justify-center gap-2">
            <button v-for="page in lastPage" :key="page" class="h-9 w-9 rounded-full text-sm"
                :class="page === currentPage ? 'bg-brand-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'"
                @click="updatePage(page)">
                {{ page }}
            </button>
        </div>
    </div>
</template>