<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ImagePlus } from '@lucide/vue'
import { adminSettingsService } from '@/services/admin/settings.service'
import { useSettingsStore } from '@/stores/settings'
import QrCodeGenerator from '@/components/admin/QrCodeGenerator.vue'

const siteUrl = window.location.origin
const settingsStore = useSettingsStore()
const storeName = ref('')
const whatsappNumber = ref('')
const email = ref('')
const facebookUrl = ref('')
const instagramUrl = ref('')
const tiktokUrl = ref('')
const address = ref('')
const schedule = ref('')
const primaryColor = ref('#1e3a8a')
const secondaryColor = ref('#3b82f6')
const privacyPolicy = ref('')
const termsConditions = ref('')
const newLogo = ref<File | null>(null)
const newLogoPreview = ref<string | null>(null)
const saving = ref(false)
const saved = ref(false)
const error = ref('')

function loadFromStore() {
    const s = settingsStore.settings
    if (!s) return
    storeName.value = s.store_name
    whatsappNumber.value = s.whatsapp_number
    email.value = s.email ?? ''
    facebookUrl.value = s.facebook_url ?? ''
    instagramUrl.value = s.instagram_url ?? ''
    tiktokUrl.value = s.tiktok_url ?? ''
    address.value = s.address ?? ''
    schedule.value = s.schedule ?? ''
    primaryColor.value = s.primary_color
    secondaryColor.value = s.secondary_color
    privacyPolicy.value = s.privacy_policy ?? ''
    termsConditions.value = s.terms_conditions ?? ''
}

function onLogoSelected(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0]
    if (!file) return
    newLogo.value = file
    newLogoPreview.value = URL.createObjectURL(file)
}

async function submit() {
    saving.value = true
    saved.value = false
    error.value = ''
    try {
        const formData = new FormData()
        formData.append('store_name', storeName.value)
        formData.append('whatsapp_number', whatsappNumber.value)
        if (email.value) formData.append('email', email.value)
        if (facebookUrl.value) formData.append('facebook_url', facebookUrl.value)
        if (instagramUrl.value) formData.append('instagram_url', instagramUrl.value)
        if (tiktokUrl.value) formData.append('tiktok_url', tiktokUrl.value)
        if (address.value) formData.append('address', address.value)
        if (schedule.value) formData.append('schedule', schedule.value)
        if (privacyPolicy.value) formData.append('privacy_policy', privacyPolicy.value)
        if (termsConditions.value) formData.append('terms_conditions', termsConditions.value)
        formData.append('primary_color', primaryColor.value)
        formData.append('secondary_color', secondaryColor.value)
        if (newLogo.value) formData.append('logo', newLogo.value)

        const updated = await adminSettingsService.update(formData)
        settingsStore.settings = updated
        settingsStore.applyTheme()
        newLogo.value = null
        newLogoPreview.value = null
        saved.value = true
        setTimeout(() => (saved.value = false), 2500)
    } catch {
        error.value = 'No se pudo guardar la configuración.'
    } finally {
        saving.value = false
    }
}

onMounted(async () => {
    await settingsStore.fetch()
    loadFromStore()
})
</script>

<template>
    <div>
        <h1 class="font-display text-xl font-bold text-gray-900 dark:text-gray-100 sm:text-2xl">Configuración de tienda
        </h1>

        <form
            class="mt-6 max-w-8xl space-y-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 sm:p-6"
            @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logo</label>
                <div class="mt-2 flex flex-wrap items-center gap-4">
                    <div
                        class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                        <img v-if="newLogoPreview ?? settingsStore.settings?.logo"
                            :src="newLogoPreview ?? settingsStore.settings?.logo ?? ''"
                            class="h-full w-full object-contain" />
                    </div>
                    <label for="logo-input"
                        class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        <ImagePlus class="h-4 w-4" />
                        Cambiar logo
                    </label>
                    <input id="logo-input" type="file" accept="image/*" class="hidden" @change="onLogoSelected" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de la tienda</label>
                    <input v-model="storeName" type="text" required
                        class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">WhatsApp</label>
                    <input v-model="whatsappNumber" type="text" required placeholder="51987654321"
                        class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Correo de contacto</label>
                    <input v-model="email" type="email" placeholder="contacto@dolmarbikes.com"
                        class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Facebook (URL)</label>
                    <input v-model="facebookUrl" type="url" placeholder="https://facebook.com/tutienda"
                        class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Instagram (URL)</label>
                    <input v-model="instagramUrl" type="url" placeholder="https://instagram.com/tutienda"
                        class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">TikTok (URL)</label>
                    <input v-model="tiktokUrl" type="url" placeholder="https://tiktok.com/@tutienda"
                        class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Dirección</label>
                    <input v-model="address" type="text"
                        class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Horario</label>
                    <input v-model="schedule" type="text" placeholder="Lun a Sáb 9am - 7pm"
                        class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Color primario</label>
                    <div class="mt-1 flex items-center gap-2">
                        <input v-model="primaryColor" type="color"
                            class="h-10 w-14 cursor-pointer rounded border border-gray-300 dark:border-gray-700" />
                        <input v-model="primaryColor" type="text"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Color secundario</label>
                    <div class="mt-1 flex items-center gap-2">
                        <input v-model="secondaryColor" type="color"
                            class="h-10 w-14 cursor-pointer rounded border border-gray-300 dark:border-gray-700" />
                        <input v-model="secondaryColor" type="text"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6 dark:border-gray-800">
                <h2 class="font-display text-base font-semibold text-gray-900 dark:text-gray-100">Contenido legal</h2>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Se muestra en las páginas públicas de política
                    de privacidad y términos y condiciones.</p>

                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Política de
                            privacidad</label>
                        <textarea v-model="privacyPolicy" rows="8"
                            class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Separa los párrafos con una línea en
                            blanco entre ellos.</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Términos y
                            condiciones</label>
                        <textarea v-model="termsConditions" rows="8"
                            class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-primary focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                    </div>
                </div>
            </div>

            <p v-if="error" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
            <p v-if="saved" class="text-sm text-green-600 dark:text-green-400">Cambios guardados.</p>

            <QrCodeGenerator :default-value="siteUrl" />

            <div class="flex justify-end">
                <button type="submit" :disabled="saving"
                    class="w-full rounded-lg bg-brand-primary px-6 py-2.5 text-sm font-semibold text-white hover:brightness-110 disabled:opacity-60 sm:w-auto">
                    {{ saving ? 'Guardando...' : 'Guardar cambios' }}
                </button>
            </div>
        </form>
    </div>
</template>