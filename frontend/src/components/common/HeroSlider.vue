<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({ slides: { type: Array, default: () => [] }, title: String, subtitle: String })
const index = ref(0)
let timer = null

const hasSlides = computed(() => props.slides.length > 0)
const go = (i) => { index.value = (i + props.slides.length) % props.slides.length }

onMounted(() => { timer = setInterval(() => hasSlides.value && props.slides.length > 1 && go(index.value + 1), 5500) })
onUnmounted(() => clearInterval(timer))
</script>

<template>
  <section class="relative overflow-hidden rounded-xl bg-brand-dark text-white">
    <template v-if="hasSlides">
      <div class="relative h-64 sm:h-96">
        <img v-for="(s, i) in slides" :key="s.id" :src="s.image_url" alt="" :class="[i === index ? 'opacity-100' : 'opacity-0', 'absolute inset-0 h-full w-full object-cover transition-opacity duration-700']" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />
        <div class="absolute inset-x-0 bottom-0 p-6 sm:p-10">
          <h2 v-if="slides[index].title" class="max-w-2xl font-serif text-2xl font-bold sm:text-4xl">{{ slides[index].title }}</h2>
          <p v-if="slides[index].subtitle" class="mt-1 max-w-xl text-white/85">{{ slides[index].subtitle }}</p>
          <a v-if="slides[index].link_url" :href="slides[index].link_url" class="btn mt-4 bg-accent text-black">আরও জানুন</a>
        </div>
        <template v-if="slides.length > 1">
          <button class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-black/40 px-3 py-2 hover:bg-black/60" aria-label="পূর্ববর্তী" @click="go(index - 1)">‹</button>
          <button class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-black/40 px-3 py-2 hover:bg-black/60" aria-label="পরবর্তী" @click="go(index + 1)">›</button>
          <div class="absolute bottom-3 right-5 flex gap-1.5">
            <button v-for="(s, i) in slides" :key="s.id" :class="[i === index ? 'bg-accent' : 'bg-white/50', 'h-2 w-2 rounded-full']" :aria-label="`স্লাইড ${i + 1}`" @click="go(i)" />
          </div>
        </template>
      </div>
    </template>
    <div v-else class="px-8 py-16 sm:py-20">
      <h1 class="max-w-2xl font-serif text-3xl font-bold sm:text-5xl">{{ title || 'একসাথে, আমরা গড়ে তুলি শক্তিশালী একটি সমাজ' }}</h1>
      <p v-if="subtitle" class="mt-3 max-w-xl text-lg text-white/80">{{ subtitle }}</p>
      <div class="mt-6 flex flex-wrap gap-3">
        <RouterLink to="/notices" class="btn bg-accent text-black">সর্বশেষ নোটিশ</RouterLink>
        <RouterLink to="/join" class="btn border border-white/40 text-white hover:bg-white/10">সদস্য হন</RouterLink>
      </div>
    </div>
  </section>
</template>