<script setup>

import Map from "@/frontend/components/Map.vue";
import TopBar from "@/frontend/components/TopBar.vue";

import {ref} from "vue";
import View from "@/frontend/utils/views.js";
import {getInitialDate} from "@/frontend/utils/utils.js";

/** @type {Ref<View>} */
const view = ref(View.Areas);
/** @type {Ref<String>} */
const date = ref(getInitialDate());
/** @type {Ref<Number>} */
const hour = ref(12);
const updateView = (newView) => {
  view.value = newView;
};
const updateDate = (newDate) => {
  date.value = newDate;
}
const updateHour = (newHour) => {
  hour.value = newHour;
}
</script>

<template>
  <header>
    <TopBar :date="date" :hour="hour"
            @update:date="updateDate" @update:hour="updateHour"/>
  </header>
  <main>
    <Map :date="date" :hour="hour" :view="view"
         @update:view="updateView"/>
  </main>
</template>

<style scoped>

</style>
