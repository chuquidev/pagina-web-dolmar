<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { Package, FolderTree, Star, ImageOff, Percent, Power, Clock } from '@lucide/vue'
import { adminDashboardService, type DashboardStats } from '@/services/admin/dashboard.service'
import { useAuthStore } from '@/stores/auth'
import StatCard from '@/components/admin/StatCard.vue'
import DonutChart from '@/components/admin/DonutChart.vue'
import BarChart from '@/components/admin/BarChart.vue'

const authStore = useAuthStore()
const stats = ref<DashboardStats | null>(null)
const loading = ref(true)

onMounted(async () => {
    stats.value = await adminDashboardService.stats()
    loading.value = false
})
</script>

<template>
    <div>
        <h1 class="font-display text-xl font-bold text-gray-900 dark:text-gray-100 sm:text-2xl">Hola, {{
            authStore.user?.name }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Resumen general de tu catálogo.</p>

        <div v-if="loading" class="mt-6 text-gray-400 dark:text-gray-500">Cargando...</div>

        <template v-else-if="stats">
            <!-- Todas las métricas en un solo grid continuo, 2 columnas ya desde el celular -->
            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
                <StatCard label="Productos" :value="stats.total_products" :icon="Package" />
                <StatCard label="Categorías" :value="stats.total_categories" :icon="FolderTree" />
                <StatCard label="Marcas" :value="stats.total_brands" :icon="FolderTree" />
                <StatCard label="Destacados" :value="stats.featured_products" :icon="Star"
                    icon-bg="bg-amber-50 dark:bg-amber-950/40" icon-color="text-amber-600 dark:text-amber-400" />
                <StatCard label="Con oferta" :value="stats.products_with_offers" :icon="Percent"
                    icon-bg="bg-red-50 dark:bg-red-950/40" icon-color="text-red-600 dark:text-red-400" />
                <StatCard label="Activos" :value="stats.active_products" :icon="Power"
                    icon-bg="bg-green-50 dark:bg-green-950/40" icon-color="text-green-600 dark:text-green-400" />
                <StatCard label="Inactivos" :value="stats.inactive_products" :icon="Power"
                    icon-bg="bg-gray-100 dark:bg-gray-800" icon-color="text-gray-500 dark:text-gray-400" />
                <StatCard label="Sin imágenes" :value="stats.products_without_images" :icon="ImageOff"
                    :icon-bg="stats.products_without_images > 0 ? 'bg-amber-50 dark:bg-amber-950/40' : 'bg-gray-100 dark:bg-gray-800'"
                    :icon-color="stats.products_without_images > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500'" />
            </div>

            <p v-if="stats.products_without_images > 0"
                class="mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 rounded-lg bg-amber-50 px-4 py-2.5 text-sm text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">
                <ImageOff class="h-4 w-4 shrink-0" />
                <span>{{ stats.products_without_images }} producto{{ stats.products_without_images === 1 ? '' : 's' }}
                    sin fotos.</span>
                <RouterLink to="/admin/products" class="font-semibold underline underline-offset-2">Revisar</RouterLink>
            </p>

            <!-- Gráficas -->
            <div class="mt-6 grid gap-4 lg:grid-cols-2">
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 sm:p-6">
                    <h2 class="font-display text-base font-semibold text-gray-900 dark:text-gray-100">Disponibilidad
                    </h2>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Cómo está distribuido tu catálogo por
                        stock.</p>
                    <div class="mt-5">
                        <DonutChart :data="[
                            { label: 'Disponible', value: stats.availability_breakdown.in_stock, color: '#22c55e' },
                            { label: 'Por encargo', value: stats.availability_breakdown.on_request, color: '#f59e0b' },
                            { label: 'Agotado', value: stats.availability_breakdown.out_of_stock, color: '#9ca3af' },
                        ]" />
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 sm:p-6">
                    <h2 class="font-display text-base font-semibold text-gray-900 dark:text-gray-100">Productos por
                        categoría</h2>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Dónde está concentrado tu catálogo.</p>
                    <div class="mt-5">
                        <BarChart :data="stats.products_by_category.map((c) => ({ label: c.name, value: c.count }))" />
                    </div>
                </div>
            </div>

            <!-- Últimos productos agregados -->
            <div
                class="mt-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 sm:p-6">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-base font-semibold text-gray-900 dark:text-gray-100">Últimos productos
                    </h2>
                    <RouterLink to="/admin/products" class="text-sm font-medium text-brand-primary hover:underline">Ver
                        todos</RouterLink>
                </div>

                <ul v-if="stats.recent_products.length" class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
                    <li v-for="product in stats.recent_products" :key="product.id"
                        class="flex items-center gap-2.5 py-3 sm:gap-3">
                        <div
                            class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 sm:h-11 sm:w-11">
                            <img v-if="product.thumb" :src="product.thumb" class="h-full w-full object-cover" />
                            <div v-else
                                class="flex h-full w-full items-center justify-center text-gray-300 dark:text-gray-600">
                                <Package class="h-5 w-5" />
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ product.name }}
                            </p>
                            <p class="truncate text-xs text-gray-400 dark:text-gray-500">{{ product.category }} · S/ {{
                                product.price }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium"
                            :class="product.is_active ? 'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'">
                            {{ product.is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                        <span
                            class="hidden shrink-0 items-center gap-1 text-xs text-gray-400 dark:text-gray-500 sm:flex">
                            <Clock class="h-3.5 w-3.5" />
                            {{ product.created_at }}
                        </span>
                    </li>
                </ul>
                <p v-else class="mt-4 text-sm text-gray-400 dark:text-gray-500">Aún no has agregado productos.</p>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <RouterLink to="/admin/products"
                    class="rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:border-brand-primary hover:text-brand-primary dark:border-gray-700 dark:text-gray-300">
                    Ir a Productos
                </RouterLink>
                <RouterLink to="/admin/categories"
                    class="rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:border-brand-primary hover:text-brand-primary dark:border-gray-700 dark:text-gray-300">
                    Ir a Categorías
                </RouterLink>
            </div>
        </template>
    </div>
</template>