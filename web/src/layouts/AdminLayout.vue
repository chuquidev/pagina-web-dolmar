<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { LayoutDashboard, Package, FolderTree, Tags, Image as ImageIcon, HelpCircle, Settings, LogOut, Menu, X } from '@lucide/vue'
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

const navItems = [
    { to: '/admin/dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { to: '/admin/products', label: 'Productos', icon: Package },
    { to: '/admin/categories', label: 'Categorías', icon: FolderTree },
    { to: '/admin/brands', label: 'Marcas', icon: Tags },
    { to: '/admin/banners', label: 'Banners', icon: ImageIcon },
    { to: '/admin/faqs', label: 'Preguntas frecuentes', icon: HelpCircle },
    { to: '/admin/settings', label: 'Configuración', icon: Settings },
]

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
            class="fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 -translate-x-full flex-col border-r border-gray-200 bg-white transition-transform duration-200 dark:border-gray-800 dark:bg-gray-900 lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }">
            <div
                class="flex items-center justify-between gap-2 border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                <div class="flex min-w-0 items-center gap-2">
                    <img v-if="settingsStore.settings?.logo" :src="settingsStore.settings.logo"
                        class="h-8 w-8 shrink-0 object-contain" />
                    <span class="truncate font-display font-bold text-gray-900 dark:text-gray-100">{{
                        settingsStore.settings?.store_name ?? 'Admin' }}</span>
                </div>
                <button class="shrink-0 text-gray-400 dark:text-gray-500 lg:hidden" @click="sidebarOpen = false">
                    <X class="h-5 w-5" />
                </button>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-4">
                <RouterLink v-for="item in navItems" :key="item.to" :to="item.to"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    active-class="bg-brand-primary/10 text-brand-primary dark:bg-brand-primary/20"
                    @click="sidebarOpen = false">
                    <component :is="item.icon" class="h-5 w-5" />
                    {{ item.label }}
                </RouterLink>
            </nav>

            <div class="border-t border-gray-200 p-3 dark:border-gray-800">
                <button
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    @click="confirmLogoutOpen = true">
                    <LogOut class="h-5 w-5" />
                    Cerrar sesión
                </button>
            </div>
        </aside>

        <div class="flex min-h-0 min-w-0 flex-1 flex-col lg:pl-64">
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
            @confirm="confirmLogout" @cancel="confirmLogoutOpen = false" />
    </div>
</template>