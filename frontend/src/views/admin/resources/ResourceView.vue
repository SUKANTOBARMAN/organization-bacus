<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { resources } from '../../../config/resources'
import ResourceCrud from '../../../components/admin/ResourceCrud.vue'

const route = useRoute()
const config = computed(() => resources[route.meta.resource])
</script>

<template>
  <ResourceCrud :key="config.key" :config="config" />
</template>
