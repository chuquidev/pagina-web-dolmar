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
    <section class="group relative min-h-[420px] overflow-hidden sm:min-h-[520px] lg:min-h-[640px]"
        @mouseenter="stopAutoplay" @mouseleave="startAutoplay" @touchstart="onTouchStart" @touchend="onTouchEnd">
        <div v-for="(banner, index) in banners" :key="banner.id"
            class="absolute inset-0 transition-opacity duration-700"
            :class="index === currentIndex ? 'z-10 opacity-100' : 'z-0 opacity-0'">
            <img v-if="banner.image" :src="banner.image" :alt="banner.title ?? ''" class="h-full w-full object-cover" />
            <div v-else class="h-full w-full"
                style="background: linear-gradient(135deg, var(--color-brand-primary), var(--color-brand-secondary))">
            </div>

            <div class="absolute inset-0 bg-black/40"></div>

            <div class="absolute inset-0 flex items-center justify-center px-4 text-center text-white">
                <div class="max-w-2xl">
                    <h1 v-if="banner.title" class="font-display text-3xl font-bold sm:text-4xl lg:text-5xl">{{
                        banner.title }}</h1>
                    <p v-if="banner.subtitle" class="mx-auto mt-4 max-w-xl text-sm text-white/85 sm:text-base">{{
                        banner.subtitle }}</p>

                    <template v-if="banner.button_text && banner.button_url">

                        <a v-if="isExternal(banner.button_url)" :href="banner.button_url" target="_blank" rel="noopener"
                            class="mt-8 inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 font-display
                        font-semibold text-gray-900 transition hover:bg-gray-100">
                            {{ banner.button_text }}
                        </a>
                        <RouterLink v-else :to="banner.button_url"
                            class="mt-8 inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 font-display font-semibold text-gray-900 transition hover:bg-gray-100">
                            {{ banner.button_text }}
                        </RouterLink>
                    </template>
                </div>
            </div>
        </div>

        <template v-if="hasMultiple">
            <button
                class="absolute left-3 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/30 text-white opacity-0 transition hover:bg-black/50 group-hover:opacity-100 sm:left-6"
                aria-label="Anterior" @click="prev">
                <ChevronLeft class="h-5 w-5" />
            </button>
            <button
                class="absolute right-3 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/30 text-white opacity-0 transition hover:bg-black/50 group-hover:opacity-100 sm:right-6"
                aria-label="Siguiente" @click="next">
                <ChevronRight class="h-5 w-5" />
            </button>

            <div class="absolute bottom-5 left-1/2 z-20 flex -translate-x-1/2 gap-2">
                <button v-for="(banner, index) in banners" :key="banner.id" class="h-2 rounded-full transition-all"
                    :class="index === currentIndex ? 'w-6 bg-white' : 'w-2 bg-white/50 hover:bg-white/75'"
                    :aria-label="`Ir al banner ${index + 1}`" @click="goTo(index)"></button>
            </div>
        </template>
    </section>
</template>