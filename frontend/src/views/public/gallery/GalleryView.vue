
<script setup>
import { ref } from 'vue'
import api from '../../../api/client'
import { useFetch } from '../../../composables/useFetch'
import PageTitle from '../../../components/common/PageTitle.vue'
import StateBlock from '../../../components/common/StateBlock.vue'

const tab = ref('photos')
const { data, loading, error } = useFetch('/albums')
const { data: videos, loading: vLoading } = useFetch('/videos')
const album = ref(null)
const active = ref(null)

async function openAlbum(id) {
  album.value = (await api.get(`/albums/${id}`)).data
}
</script>

<template>
  <PageTitle title="ছবির গ্যালারি" />

  <!-- ছবি ও ভিডিও ট্যাব -->
  <div class="mb-5 flex gap-2">
    <button
      :class="[tab === 'photos' ? 'btn-primary' : 'btn-outline', 'btn btn-sm']"
      @click="tab = 'photos'; album = null"
    >
      ছবি
    </button>

    <button
      :class="[tab === 'videos' ? 'btn-primary' : 'btn-outline', 'btn btn-sm']"
      @click="tab = 'videos'"
    >
      ভিডিও
    </button>
  </div>

  <!-- ছবি গ্যালারি -->
  <template v-if="tab === 'photos'">
    <StateBlock
      :loading="loading"
      :error="error"
      :empty="!data?.data?.length"
      empty-text="এখনো কোনো অ্যালবাম নেই।"
    >
      <!-- অ্যালবামের তালিকা -->
      <div
        v-if="!album"
        class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4"
      >
        <button
          v-for="a in data.data"
          :key="a.id"
          class="card group overflow-hidden text-left"
          @click="openAlbum(a.id)"
        >
          <div class="aspect-[4/3] overflow-hidden bg-stone-200">
            <img
              v-if="a.cover_image_url"
              :src="a.cover_image_url"
              :alt="a.title"
              class="h-full w-full object-cover transition group-hover:scale-105"
              loading="lazy"
            />
          </div>

          <div class="p-3">
            <h3 class="font-semibold">{{ a.title }}</h3>

            <p class="text-xs text-stone-500">
              {{ a.photos_count }}টি ছবি
            </p>
          </div>
        </button>
      </div>

      <!-- নির্দিষ্ট অ্যালবামের ছবি -->
      <div v-else>
        <button
          class="mb-3 text-sm text-brand underline"
          @click="album = null"
        >
          ← সকল অ্যালবামে ফিরে যান
        </button>

        <h2 class="mb-4 font-serif text-2xl font-bold">
          {{ album.title }}
        </h2>

        <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-4">
          <img
            v-for="p in album.photos"
            :key="p.id"
            :src="p.image_url"
            :alt="p.caption || ''"
            class="aspect-[4/3] w-full cursor-zoom-in rounded-lg object-cover"
            loading="lazy"
            @click="active = p.image_url"
          />
        </div>
      </div>
    </StateBlock>
  </template>

  <!-- ভিডিও গ্যালারি -->
  <StateBlock
    v-else
    :loading="vLoading"
    :empty="!videos?.length"
    empty-text="এখনো কোনো ভিডিও নেই।"
  >
    <div class="grid gap-5 md:grid-cols-2">
      <div
        v-for="v in videos"
        :key="v.id"
        class="card overflow-hidden"
      >
        <div v-if="v.embed_url" class="aspect-video">
          <iframe
            :src="v.embed_url"
            class="h-full w-full"
            :title="v.title"
            allowfullscreen
            loading="lazy"
          />
        </div>

        <div class="p-3">
          <h3 class="font-semibold">{{ v.title }}</h3>

          <p
            v-if="v.description"
            class="text-sm text-stone-500"
          >
            {{ v.description }}
          </p>
        </div>
      </div>
    </div>
  </StateBlock>

  <!-- পূর্ণ পর্দায় ছবি দেখা -->
  <Teleport to="body">
    <div
      v-if="active"
      class="fixed inset-0 z-50 grid place-items-center bg-black/90 p-4"
      @click="active = null"
    >
      <img
        :src="active"
        class="max-h-full max-w-full rounded"
        alt=""
      />
    </div>
  </Teleport>
</template>
