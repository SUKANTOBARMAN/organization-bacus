
<script setup>
import { useFetch } from '../../../composables/useFetch'
import { useSettings } from '../../../composables/useSettings'
import HeroSlider from '../../../components/common/HeroSlider.vue'
import Avatar from '../../../components/common/Avatar.vue'
import StateBlock from '../../../components/common/StateBlock.vue'

const settings = useSettings()
const { data, loading, error } = useFetch('/home')

const fmt = (d) =>
  d
    ? new Date(d).toLocaleDateString('bn-BD', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
      })
    : ''

const statLabels = {
  members: 'সদস্য',
  notices: 'বিজ্ঞপ্তি',
  events: 'অনুষ্ঠান',
  albums: 'ছবির অ্যালবাম',
}
</script>

<template>
  <!-- হিরো স্লাইডার -->
  <HeroSlider
    :slides="data?.sliders || []"
    :title="settings.hero_title"
    :subtitle="settings.hero_subtitle"
  />

  <StateBlock :loading="loading" :error="error">

    <!-- পরিসংখ্যান -->
    <div
      v-if="data"
      class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4"
    >
      <div
        v-for="(label, key) in statLabels"
        :key="key"
        class="card p-4 text-center"
      >
        <p class="font-serif text-3xl font-bold text-brand">
          {{ data.stats[key] }}
        </p>
        <p class="text-sm text-stone-500">{{ label }}</p>
      </div>
    </div>

    <!-- সংবাদ, বিজ্ঞপ্তি ও অনুষ্ঠান -->
    <div
      v-if="data"
      class="mt-12 grid gap-10 lg:grid-cols-3"
    >

      <!-- সর্বশেষ সংবাদ -->
      <section class="lg:col-span-2">
        <div class="mb-4 flex items-end justify-between border-b pb-2">
          <h2 class="font-serif text-2xl font-bold text-brand-dark">
            সর্বশেষ সংবাদ
          </h2>

          <RouterLink
            to="/news"
            class="text-sm text-brand underline"
          >
            সকল সংবাদ →
          </RouterLink>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
          <RouterLink
            v-for="p in [...data.featured, ...data.news].filter((p, i, a) => a.findIndex((x) => x.id === p.id) === i).slice(0, 4)"
            :key="p.id"
            :to="`/news/${p.id}`"
            class="card overflow-hidden transition hover:shadow-md"
          >
            <img
              v-if="p.cover_image_url"
              :src="p.cover_image_url"
              :alt="p.title"
              class="aspect-video w-full object-cover"
              loading="lazy"
            />

            <div
              v-else
              class="grid aspect-video place-items-center bg-brand/10 font-serif text-3xl text-brand/40"
            >
              {{ p.title.charAt(0) }}
            </div>

            <div class="p-4">
              <p class="text-xs text-accent">
                {{ p.type }} · {{ fmt(p.published_at) }}
              </p>

              <h3 class="font-semibold leading-snug">
                {{ p.title }}
              </h3>
            </div>
          </RouterLink>
        </div>
      </section>

      <!-- ডান পাশের বিভাগ -->
      <aside class="space-y-8">

        <!-- বিজ্ঞপ্তি -->
        <section>
          <div class="mb-3 flex items-end justify-between border-b pb-2">
            <h2 class="font-serif text-xl font-bold text-brand-dark">
              বিজ্ঞপ্তি
            </h2>

            <RouterLink
              to="/notices"
              class="text-sm text-brand underline"
            >
              সকল বিজ্ঞপ্তি →
            </RouterLink>
          </div>

          <ul class="card divide-y">
            <li
              v-for="n in data.notices"
              :key="n.id"
            >
              <RouterLink
                :to="`/notices/${n.id}`"
                class="block px-4 py-3 hover:bg-stone-50"
              >
                <p class="text-xs text-accent">
                  {{ fmt(n.published_at) }}
                  <span v-if="n.is_pinned">📌</span>
                </p>

                <p class="text-sm">{{ n.title }}</p>
              </RouterLink>
            </li>
          </ul>
        </section>

        <!-- আসন্ন অনুষ্ঠান -->
        <section v-if="data.events.length">
          <h2 class="mb-3 border-b pb-2 font-serif text-xl font-bold text-brand-dark">
            আসন্ন অনুষ্ঠান
          </h2>

          <ul class="space-y-3">
            <li
              v-for="e in data.events"
              :key="e.id"
              class="flex gap-3"
            >
              <div class="w-14 shrink-0 rounded bg-brand p-1.5 text-center text-white">
                <p class="text-lg font-bold leading-none">
                  {{ new Date(e.event_at).getDate() }}
                </p>

                <p class="text-xs">
                  {{ new Date(e.event_at).toLocaleString('bn-BD', { month: 'short' }) }}
                </p>
              </div>

              <RouterLink
                :to="`/news/${e.id}`"
                class="text-sm hover:text-brand"
              >
                <span class="font-medium">{{ e.title }}</span>
                <br />
                <span class="text-stone-500">{{ e.venue }}</span>
              </RouterLink>
            </li>
          </ul>
        </section>
      </aside>
    </div>

    <!-- কার্যনির্বাহী কমিটি -->
    <section
      v-if="data?.committee?.members?.length"
      class="mt-14"
    >
      <div class="mb-4 flex items-end justify-between border-b pb-2">
        <h2 class="font-serif text-2xl font-bold text-brand-dark">
          {{ data.committee.title }} {{ data.committee.term }}
        </h2>

        <RouterLink
          to="/committee"
          class="text-sm text-brand underline"
        >
          পূর্ণাঙ্গ কমিটি →
        </RouterLink>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div
          v-for="m in data.committee.members.slice(0, 4)"
          :key="m.id"
          class="card flex items-center gap-3 p-4"
        >
          <Avatar
            :src="m.photo_url"
            :name="m.name"
          />

          <div>
            <p class="font-semibold">{{ m.name }}</p>
            <p class="text-sm text-stone-500">
              {{ m.designation }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ছবির গ্যালারি -->
    <section
      v-if="data?.albums?.length"
      class="mt-14"
    >
      <div class="mb-4 flex items-end justify-between border-b pb-2">
        <h2 class="font-serif text-2xl font-bold text-brand-dark">
          ছবির গ্যালারি
        </h2>

        <RouterLink
          to="/gallery"
          class="text-sm text-brand underline"
        >
          সকল অ্যালবাম →
        </RouterLink>
      </div>

      <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <RouterLink
          v-for="a in data.albums"
          :key="a.id"
          to="/gallery"
          class="group relative block aspect-[4/3] overflow-hidden rounded-lg bg-stone-200"
        >
          <img
            v-if="a.cover_image_url"
            :src="a.cover_image_url"
            :alt="a.title"
            class="h-full w-full object-cover transition group-hover:scale-105"
            loading="lazy"
          />

          <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 p-3 text-sm font-medium text-white">
            {{ a.title }}
          </span>
        </RouterLink>
      </div>
    </section>

  </StateBlock>
</template>
