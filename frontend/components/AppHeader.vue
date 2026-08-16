<script setup lang="ts">
defineProps<{
  title: string
}>()
</script>

<template>
  <header class="border-b bg-white px-8 py-5 flex items-center justify-between sticky top-0 z-30">
    <div>
      <h2 class="text-xl font-bold text-slate-800">
        {{ title }}
      </h2>
    </div>
  </header>
</template>
