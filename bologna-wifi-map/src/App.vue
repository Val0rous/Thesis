<script setup>

import Map from "@/components/Map.vue";
import TopBar from "@/components/TopBar.vue";

import {ref} from "vue";
import View from "@/utils/views.js";
import Maps from "@/utils/maps.js";
import {getInitialDate} from "@/utils/utils.js";

/** @type {Ref<View>} */
const view = ref(View.Areas);
/** @type {Ref<Maps>} */
const maps = ref(Maps.Default);
/** @type {Ref<String>} */
const date = ref(getInitialDate());
/** @type {Ref<Number>} */
const hour = ref(12);
const updateView = (newView) => {
  view.value = newView;
};
const updateMaps = (newMap) => {
  maps.value = newMap;
}
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
    <Map :date="date" :hour="hour" :maps="maps" :view="view"
         @update:view="updateView" @update:maps="updateMaps"/>
  </main>
</template>

<style scoped>

</style>
