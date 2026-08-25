<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { adminMaintenanceSettingsService } from '@/services/admin/maintenance.service'
import { useToastStore } from '@/stores/toast'
import { useFormErrors } from '@/composables/useFormErrors'
import type { MaintenanceSettings, DayKey } from '@/types/catalog'

const toastStore = useToastStore()
const { getError, parseErrors, clearErrors } = useFormErrors()

const days: { key: DayKey; label: string }[] = [
    { key: 'monday', label: 'Lunes' },
    { key: 'tuesday', label: 'Martes' },
    { key: 'wednesday', label: 'Miércoles' },
    { key: 'thursday', label: 'Jueves' },
    { key: 'friday', label: 'Viernes' },
    { key: 'saturday', label: 'Sábado' },
    { key: 'sunday', label: 'Domingo' },
]

const hours = ref<Record<DayKey, { open: string; close: string } | null>>({
    monday: null,
    tuesday: null,
    wednesday: null,
    thursday: null,
    friday: null,
    saturday: null,
    sunday: null,
})
const capacity = ref(1)
const slotIntervalMinutes = ref(30)
const advanceBookingDays = ref(14)
const minNoticeHours = ref(2)
const loading = ref(true)
const saving = ref(false)

function toggleDay(day: DayKey, open: boolean) {
    hours.value[day] = open ? { open: '09:00', close: '19:00' } : null
}

async function load() {
    loading.value = true
    const settings = await adminMaintenanceSettingsService.get()
    hours.value = settings.business_hours
    capacity.value = settings.capacity
    slotIntervalMinutes.value = settings.slot_interval_minutes
    advanceBookingDays.value = settings.advance_booking_days
    minNoticeHours.value = settings.min_notice_hours
    loading.value = false
}

async function submit() {
    saving.value = true
    clearErrors()
    try {
        const payload: MaintenanceSettings = {
            business_hours: hours.value,
            capacity: capacity.value,
            slot_interval_minutes: slotIntervalMinutes.value,
            advance_booking_days: advanceBookingDays.value,
            min_notice_hours: minNoticeHours.value,
        }
        await adminMaintenanceSettingsService.update(payload)
        toastStore.success('Horarios guardados.')
    } catch (err) {
        toastStore.error(parseErrors(err))
    } finally {
        saving.value = false
    }
}

onMounted(load)
</script>

<template>
    <div>
        <h1 class="font-display text-xl font-bold text-gray-900 dark:text-gray-100 sm:text-2xl">Horarios de atención
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define cuándo se pueden reservar citas de mantenimiento
            y cuántas al mismo tiempo.</p>

        <div v-if="loading" class="mt-6 text-gray-400 dark:text-gray-500">Cargando...</div>

        <form v-else
            class="mt-6 max-w-3xl space-y-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 sm:p-6"
            @submit.prevent="submit">
            <div>
                <h2 class="font-display text-base font-semibold text-gray-900 dark:text-gray-100">Días y horas</h2>
                <div class="mt-3 space-y-2">
                    <div v-for="day in days" :key="day.key"
                        class="flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <label class="flex w-28 items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" :checked="hours[day.key] !== null"
                                class="rounded border-gray-300 text-brand-primary dark:border-gray-600 dark:bg-gray-800"
                                @change="toggleDay(day.key, ($event.target as HTMLInputElement).checked)" />
                            {{ day.label }}
                        </label>

                        <template v-if="hours[day.key]">
                            <input v-model="hours[day.key]!.open" type="time"
                                class="rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                            <span class="text-sm text-gray-400">a</span>
                            <input v-model="hours[day.key]!.close" type="time"
                                class="rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                        </template>
                        <span v-else class="text-sm text-gray-400 dark:text-gray-500">Cerrado</span>
                    </div>
                </div>
                <p v-if="getError('business_hours')" class="mt-2 text-xs text-red-600 dark:text-red-400">{{
                    getError('business_hours')
                    }}</p>
            </div>

            <div class="grid gap-4 border-t border-gray-200 pt-6 dark:border-gray-800 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Capacidad simultánea</label>
                    <input v-model.number="capacity" type="number" min="1" required
                        class="mt-1 w-full rounded-lg border px-3 py-2 text-sm text-gray-900 focus:outline-none dark:bg-gray-800 dark:text-gray-100"
                        :class="getError('capacity') ? 'border-red-400 dark:border-red-700' : 'border-gray-300 focus:border-brand-primary dark:border-gray-700'" />
                    <p v-if="getError('capacity')" class="mt-1 text-xs text-red-600 dark:text-red-400">{{
                        getError('capacity') }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Cuántas bicicletas puedes atender al mismo
                        tiempo
                        (número de mecánicos/espacios).</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Intervalo entre horarios
                        (minutos)</label>
                    <input v-model.number="slotIntervalMinutes" type="number" min="5" step="5" required
                        class="mt-1 w-full rounded-lg border px-3 py-2 text-sm text-gray-900 focus:outline-none dark:bg-gray-800 dark:text-gray-100"
                        :class="getError('slot_interval_minutes') ? 'border-red-400 dark:border-red-700' : 'border-gray-300 focus:border-brand-primary dark:border-gray-700'" />
                    <p v-if="getError('slot_interval_minutes')" class="mt-1 text-xs text-red-600 dark:text-red-400">{{
                        getError('slot_interval_minutes') }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Ej: cada 30 minutos aparece un horario
                        nuevo para
                        elegir.</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Días de anticipación
                        permitidos</label>
                    <input v-model.number="advanceBookingDays" type="number" min="1" required
                        class="mt-1 w-full rounded-lg border px-3 py-2 text-sm text-gray-900 focus:outline-none dark:bg-gray-800 dark:text-gray-100"
                        :class="getError('advance_booking_days') ? 'border-red-400 dark:border-red-700' : 'border-gray-300 focus:border-brand-primary dark:border-gray-700'" />
                    <p v-if="getError('advance_booking_days')" class="mt-1 text-xs text-red-600 dark:text-red-400">{{
                        getError('advance_booking_days') }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Hasta cuántos días a futuro puede reservar
                        un cliente.
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Aviso mínimo (horas)</label>
                    <input v-model.number="minNoticeHours" type="number" min="0" required
                        class="mt-1 w-full rounded-lg border px-3 py-2 text-sm text-gray-900 focus:outline-none dark:bg-gray-800 dark:text-gray-100"
                        :class="getError('min_notice_hours') ? 'border-red-400 dark:border-red-700' : 'border-gray-300 focus:border-brand-primary dark:border-gray-700'" />
                    <p v-if="getError('min_notice_hours')" class="mt-1 text-xs text-red-600 dark:text-red-400">{{
                        getError('min_notice_hours') }}</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">No se pueden reservar horarios más cercanos
                        a esto
                        desde ahora.</p>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" :disabled="saving"
                    class="w-full rounded-lg bg-brand-primary px-6 py-2.5 text-sm font-semibold text-white hover:brightness-110 disabled:opacity-60 sm:w-auto">
                    {{ saving ? 'Guardando...' : 'Guardar cambios' }}
                </button>
            </div>
        </form>
    </div>
</template>