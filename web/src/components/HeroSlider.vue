<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { ChevronLeft, ChevronRight } from '@lucide/vue'
import type { Banner } from '@/types/catalog'

const props = defineProps<{ banners: Banner[] }>()

const currentIndex = ref(0)
let autoplayTimer: ReturnType<typeof setInterval> | null = null
let touchStartX = 0

function isExternal(url: string) {
    return url.startsWith('http')
}

function goTo(index: number) {
    currentIndex.value = (index + props.banners.length) % props.banners.length
}
function next() {
    goTo(currentIndex.value + 1)
}
function prev() {
    goTo(currentIndex.value - 1)
}

function startAutoplay() {
    stopAutoplay()
    if (props.banners.length <= 1) return
    autoplayTimer = setInterval(next, 5500)
}
function stopAutoplay() {
    if (autoplayTimer) clearInterval(autoplayTimer)
}

function onTouchStart(e: TouchEvent) {
    touchStartX = e.touches[0].clientX
}
function onTouchEnd(e: TouchEvent) {
    const delta = e.changedTouches[0].clientX - touchStartX
    if (Math.abs(delta) < 50) return
    delta > 0 ? prev() : next()
}

const hasMultiple = computed(() => props.banners.length > 1)

onMounted(startAutoplay)
onUnmounted(stopAutoplay)
</script>

<template>
    <section class="group relative w-full overflow-hidden aspect-[16/7]" @mouseenter="stopAutoplay"
        @mouseleave="startAutoplay" @touchstart="onTouchStart" @touchend="onTouchEnd">
        <div v-for="(banner, index) in banners" :key="banner.id"
            class="absolute inset-0 transition-opacity duration-700"
            :class="index === currentIndex ? 'z-10 opacity-100' : 'z-0 opacity-0'">
            <img v-if="banner.image" :src="banner.image" :alt="banner.title ?? ''" class="h-full w-full object-cover" />
            <div v-else class="h-full w-full"
                style="background: linear-gradient(135deg, var(--color-brand-primary), var(--color-brand-secondary))">
            </div>

            <div v-if="banner.title || banner.subtitle" class="absolute inset-0 bg-black/30"></div>

            <div class="absolute inset-0 flex items-center justify-center px-4 text-center text-white">
                <div class="max-w-2xl">
                    <h1 v-if="banner.title"
                        class="font-display text-base font-bold leading-tight sm:text-2xl lg:text-4xl">{{ banner.title
                        }}</h1>
                    <p v-if="banner.subtitle"
                        class="mx-auto mt-1 hidden max-w-xl text-xs text-white/85 sm:mt-2 sm:block sm:text-sm lg:mt-3 lg:text-base">
                        {{ banner.subtitle }}</p>

                    <template v-if="banner.button_text && banner.button_url">

                        <a v-if="isExternal(banner.button_url)" :href="banner.button_url" target="_blank" rel="noopener"
                            class="mt-2 inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 font-display text-xs
                        font-semibold text-gray-900 transition hover:bg-gray-100 sm:mt-4 sm:px-5 sm:py-2 sm:text-sm
                        lg:mt-6 lg:px-6 lg:py-3 lg:text-base">
                            {{ banner.button_text }}
                        </a>
                        <RouterLink v-else :to="banner.button_url"
                            class="mt-2 inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 font-display text-xs font-semibold text-gray-900 transition hover:bg-gray-100 sm:mt-4 sm:px-5 sm:py-2 sm:text-sm lg:mt-6 lg:px-6 lg:py-3 lg:text-base">
                            {{ banner.button_text }}
                        </RouterLink>
                    </template>
                </div>
            </div>
        </div>

        <template v-if="hasMultiple">
            <button
                class="absolute left-1 top-1/2 z-20 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full bg-black/30 text-white opacity-0 transition hover:bg-black/50 group-hover:opacity-100 sm:left-3 sm:h-9 sm:w-9 lg:left-6 lg:h-10 lg:w-10"
                aria-label="Anterior" @click="prev">
                <ChevronLeft class="h-4 w-4 sm:h-5 sm:w-5" />
            </button>
            <button
                class="absolute right-1 top-1/2 z-20 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full bg-black/30 text-white opacity-0 transition hover:bg-black/50 group-hover:opacity-100 sm:right-3 sm:h-9 sm:w-9 lg:right-6 lg:h-10 lg:w-10"
                aria-label="Siguiente" @click="next">
                <ChevronRight class="h-4 w-4 sm:h-5 sm:w-5" />
            </button>

            <div
                class="absolute bottom-1.5 left-1/2 z-20 flex -translate-x-1/2 gap-1.5 sm:bottom-3 sm:gap-2 lg:bottom-5">
                <button v-for="(banner, index) in banners" :key="banner.id"
                    class="h-1.5 rounded-full transition-all sm:h-2"
                    :class="index === currentIndex ? 'w-5 bg-white sm:w-6' : 'w-1.5 bg-white/50 hover:bg-white/75 sm:w-2'"
                    :aria-label="`Ir al banner ${index + 1}`" @click="goTo(index)"></button>
            </div>
        </template>
    </section>
</template>