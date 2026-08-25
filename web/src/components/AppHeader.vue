<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Search, ShoppingBag, ShoppingCart, Menu, X } from '@lucide/vue'
import { useSettingsStore } from '@/stores/settings'
import { useCartStore } from '@/stores/cart'
import ThemeToggle from '@/components/ThemeToggle.vue'
import CartDrawer from '@/components/CartDrawer.vue'

const settingsStore = useSettingsStore()
const cartStore = useCartStore()
const router = useRouter()
const search = ref('')
const mobileMenuOpen = ref(false)
const mobileSearchOpen = ref(false)
const cartOpen = ref(false)
const bump = ref(false)

watch(
    () => cartStore.totalItems,
    (newVal, oldVal) => {
        if (newVal > oldVal) {
            bump.value = true
            setTimeout(() => (bump.value = false), 350)
        }
    }
)

function submitSearch() {
    if (!search.value.trim()) return
    router.push({ path: '/catalogo', query: { search: search.value } })
    mobileSearchOpen.value = false
}
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b border-gray-200 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-gray-950/90">
        <div class="mx-auto flex max-w-[1400px] items-center gap-4 px-4 py-3">
            <RouterLink to="/" class="flex min-w-0 items-center gap-2 font-bold text-brand-primary">
                <img v-if="settingsStore.settings?.logo" :src="settingsStore.settings.logo"
                    :alt="settingsStore.settings.store_name" class="h-9 w-9 shrink-0 rounded object-contain" />
                <ShoppingBag v-else class="h-7 w-7 shrink-0" />
                <span class="truncate">{{ settingsStore.settings?.store_name ?? 'Cargando...' }}</span>
            </RouterLink>

            <nav class="ml-4 hidden gap-6 text-sm font-medium text-gray-600 dark:text-gray-300 md:flex">
                <RouterLink to="/" class="hover:text-brand-primary">Inicio</RouterLink>
                <RouterLink to="/catalogo" class="hover:text-brand-primary">Catálogo</RouterLink>
                <RouterLink to="/nosotros" class="hover:text-brand-primary">Nosotros</RouterLink>
                <RouterLink to="/reservar-mantenimiento" class="hover:text-brand-primary">Mantenimiento</RouterLink>
            </nav>

            <form
                class="ml-auto hidden max-w-sm flex-1 items-center gap-2 rounded-full border border-gray-300 px-3 py-1.5 dark:border-gray-700 md:flex"
                @submit.prevent="submitSearch">
                <Search class="h-4 w-4 text-gray-400" />
                <input v-model="search" type="search" placeholder="Buscar productos..."
                    class="w-full bg-transparent text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-gray-100" />
            </form>

            <button
                class="relative hidden rounded-lg p-2 text-gray-600 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 md:block"
                aria-label="Ver carrito" @click="cartOpen = true">
                <ShoppingCart class="h-5 w-5 transition-transform duration-300" :class="{ 'scale-125': bump }" />
                <span v-if="cartStore.totalItems"
                    class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-brand-primary px-1 text-[10px] font-bold text-white transition-transform duration-300"
                    :class="{ 'scale-125': bump }">
                    {{ cartStore.totalItems }}
                </span>
            </button>

            <ThemeToggle class="hidden md:block" />

            <div class="ml-auto flex items-center gap-1 md:hidden">
                <button
                    class="relative rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    aria-label="Ver carrito" @click="cartOpen = true">
                    <ShoppingCart class="h-5 w-5 transition-transform duration-300" :class="{ 'scale-125': bump }" />
                    <span v-if="cartStore.totalItems"
                        class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-brand-primary px-1 text-[10px] font-bold text-white transition-transform duration-300"
                        :class="{ 'scale-125': bump }">
                        {{ cartStore.totalItems }}
                    </span>
                </button>
                <ThemeToggle />
                <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    @click="mobileSearchOpen = !mobileSearchOpen; mobileMenuOpen = false">
                    <Search class="h-5 w-5" />
                </button>
                <button class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    @click="mobileMenuOpen = !mobileMenuOpen; mobileSearchOpen = false">
                    <component :is="mobileMenuOpen ? X : Menu" class="h-5 w-5" />
                </button>
            </div>
        </div>

        <form v-if="mobileSearchOpen" class="border-t border-gray-100 px-4 py-3 dark:border-gray-800 md:hidden"
            @submit.prevent="submitSearch">
            <div class="flex items-center gap-2 rounded-full border border-gray-300 px-3 py-2 dark:border-gray-700">
                <Search class="h-4 w-4 text-gray-400" />
                <input v-model="search" type="search" placeholder="Buscar productos..." autofocus
                    class="w-full bg-transparent text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-gray-100" />
            </div>
        </form>

        <nav v-if="mobileMenuOpen"
            class="flex flex-col border-t border-gray-100 px-4 py-2 text-sm font-medium text-gray-600 dark:border-gray-800 dark:text-gray-300 md:hidden">
            <RouterLink to="/"
                class="rounded-lg px-3 py-2 hover:bg-gray-50 hover:text-brand-primary dark:hover:bg-gray-800"
                @click="mobileMenuOpen = false">Inicio</RouterLink>
            <RouterLink to="/catalogo"
                class="rounded-lg px-3 py-2 hover:bg-gray-50 hover:text-brand-primary dark:hover:bg-gray-800"
                @click="mobileMenuOpen = false">Catálogo</RouterLink>
            <RouterLink to="/nosotros"
                class="rounded-lg px-3 py-2 hover:bg-gray-50 hover:text-brand-primary dark:hover:bg-gray-800"
                @click="mobileMenuOpen = false">Nosotros</RouterLink>
            <RouterLink to="/reservar-mantenimiento"
                class="rounded-lg px-3 py-2 hover:bg-gray-50 hover:text-brand-primary dark:hover:bg-gray-800"
                @click="mobileMenuOpen = false">Mantenimiento</RouterLink>
        </nav>

        <CartDrawer v-if="cartOpen" @close="cartOpen = false" />
    </header>
</template>