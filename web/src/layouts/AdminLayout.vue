<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import {
    LayoutDashboard, Package, FolderTree, Tags, Image as ImageIcon, HelpCircle, Settings,
    Wrench, Clock, Calendar, LogOut, Menu, X, Store, ChevronDown,
} from '@lucide/vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useSettingsStore } from '@/stores/settings'
import ThemeToggle from '@/components/ThemeToggle.vue'
import ConfirmDialog from '@/components/admin/ConfirmDialog.vue'

const authStore = useAuthStore()
const settingsStore = useSettingsStore()
const router = useRouter()
const sidebarOpen = ref(false)
const confirmLogoutOpen = ref(false)

onMounted(() => settingsStore.fetch())

const navGroups = [
    {
        label: 'Catálogo',
        items: [
            { to: '/admin/products', label: 'Productos', icon: Package },
            { to: '/admin/categories', label: 'Categorías', icon: FolderTree },
            { to: '/admin/brands', label: 'Marcas', icon: Tags },
            { to: '/admin/banners', label: 'Banners', icon: ImageIcon },
        ],
    },
    {
        label: 'Mantenimiento',
        items: [
            { to: '/admin/maintenance-services', label: 'Servicios', icon: Wrench },
            { to: '/admin/maintenance-schedule', label: 'Horarios', icon: Clock },
            { to: '/admin/appointments', label: 'Citas', icon: Calendar },
        ],
    },
    {
        label: 'Contenido',
        items: [{ to: '/admin/faqs', label: 'Preguntas frecuentes', icon: HelpCircle }],
    },
    {
        label: 'Ajustes',
        items: [{ to: '/admin/settings', label: 'Configuración', icon: Settings }],
    },
]

const STORAGE_KEY = 'admin-sidebar-collapsed'

function loadCollapsedState(): Record<string, boolean> {
    try {
        const raw = localStorage.getItem(STORAGE_KEY)
        return raw ? JSON.parse(raw) : {}
    } catch {
        return {}
    }
}

const collapsedSections = ref<Record<string, boolean>>(loadCollapsedState())

function toggleSection(label: string) {
    collapsedSections.value[label] = !collapsedSections.value[label]
    localStorage.setItem(STORAGE_KEY, JSON.stringify(collapsedSections.value))
}

const userInitial = computed(() => (authStore.user?.name?.trim()?.[0] ?? '?').toUpperCase())

async function confirmLogout() {
    confirmLogoutOpen.value = false
    await authStore.logout()
    router.push({ name: 'admin-login' })
}
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">
        <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-black/40 lg:hidden" @click="sidebarOpen = false"></div>

        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-72 shrink-0 -translate-x-full flex-col border-r border-gray-200 bg-white transition-transform duration-200 dark:border-gray-800 dark:bg-gray-900 lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }">
            <div
                class="flex items-center justify-between gap-2 border-b border-gray-100 px-5 py-5 dark:border-gray-800">
                <div class="flex min-w-0 items-center gap-2.5">
                    <img v-if="settingsStore.settings?.logo" :src="settingsStore.settings.logo"
                        class="h-9 w-9 shrink-0 rounded-lg object-contain" />
                    <div class="min-w-0">
                        <p class="truncate font-display text-sm font-bold text-gray-900 dark:text-gray-100">{{
                            settingsStore.settings?.store_name ?? 'Admin' }}</p>
                        <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">
                            Panel de administración</p>
                    </div>
                </div>
                <button class="shrink-0 text-gray-400 dark:text-gray-500 lg:hidden" @click="sidebarOpen = false">
                    <X class="h-5 w-5" />
                </button>
            </div>

            <div class="border-b border-gray-100 px-3 py-3 dark:border-gray-800">
                <a href="https://dolmarbike.com" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition
                hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800
                dark:hover:text-gray-200">
                    <Store class="h-[18px] w-[18px] shrink-0" />
                    Ver tienda
                </a>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-3">
                <RouterLink to="/admin/dashboard"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    active-class="!bg-brand-primary/10 !text-brand-primary dark:!bg-brand-primary/15"
                    @click="sidebarOpen = false">
                    <LayoutDashboard class="h-[18px] w-[18px] shrink-0" />
                    Dashboard
                </RouterLink>

                <div v-for="group in navGroups" :key="group.label" class="pt-3">
                    <button
                        class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-400 transition hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                        @click="toggleSection(group.label)">
                        <span>{{ group.label }}</span>
                        <ChevronDown class="h-3.5 w-3.5 transition-transform duration-200"
                            :class="{ '-rotate-90': collapsedSections[group.label] }" />
                    </button>
                    <div v-show="!collapsedSections[group.label]" class="mt-1 space-y-0.5">
                        <RouterLink v-for="item in group.items" :key="item.to" :to="item.to"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                            active-class="!bg-brand-primary/10 !text-brand-primary dark:!bg-brand-primary/15"
                            @click="sidebarOpen = false">
                            <component :is="item.icon" class="h-[18px] w-[18px] shrink-0" />
                            <span class="truncate">{{ item.label }}</span>
                        </RouterLink>
                    </div>
                </div>
            </nav>

            <div class="border-t border-gray-100 p-3 dark:border-gray-800">
                <div class="flex items-center gap-3 rounded-lg px-2 py-2">
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-primary font-display text-sm font-bold text-white">
                        {{ userInitial }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{
                            authStore.user?.name }}</p>
                        <p class="truncate text-xs text-gray-400 dark:text-gray-500">{{ authStore.user?.email }}</p>
                    </div>
                    <button
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300"
                        aria-label="Cerrar sesión" title="Cerrar sesión" @click="confirmLogoutOpen = true">
                        <LogOut class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </aside>

        <div class="flex min-h-0 min-w-0 flex-1 flex-col lg:pl-72">
            <header
                class="flex shrink-0 items-center gap-3 border-b border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                <button class="text-gray-600 dark:text-gray-300 lg:hidden" @click="sidebarOpen = true">
                    <Menu class="h-6 w-6" />
                </button>
                <span class="truncate text-sm text-gray-600 dark:text-gray-300">
                    Hola, <span class="font-semibold text-gray-900 dark:text-gray-100">{{ authStore.user?.name }}</span>
                </span>
                <ThemeToggle class="ml-auto" />
            </header>

            <main class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
                <RouterView />
            </main>
        </div>

        <ConfirmDialog v-if="confirmLogoutOpen" title="Cerrar sesión" message="¿Seguro que quieres cerrar sesión?"
            confirm-label="Cerrar sesión" variant="primary" @confirm="confirmLogout"
            @cancel="confirmLogoutOpen = false" />
    </div>
</template>