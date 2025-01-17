<script setup>

import Map from "@/frontend/components/Map.vue";
import TopBar from "@/frontend/components/TopBar.vue";

import {ref} from "vue";
import View from "@/frontend/utils/views.js";

const view = ref(View.Areas);
const updateView = (newView) => {
  view.value = newView;
};
</script>

<template>
  <header>
    <TopBar/>
  </header>
  <main>
    <Map :view="view" @update:view="updateView"/>
    {{ console.log(view) }}
  </main>
</template>

<style scoped>

</style>
