<script setup>
import "@/frontend/utils/types.js";
import {onMounted, ref, defineProps, defineEmits, watch} from "vue";
import L from "leaflet";
import ViewButtons from "@/frontend/components/ViewButtons.vue"
import View from "@/frontend/utils/views.js";
import {
  defaultOptions,
  hoverOptions,
  clickOptions,
  lineOptions,
} from "@/frontend/utils/mapOptions.js";
import {fetchAreas, fetchCrowdingAttendance, fetchMovements} from "@/frontend/scripts/api.js";
import mapOptionsFactory from "@/frontend/scripts/mapOptionsFactory.js";
import {setupPolygons, updatePolygons} from "@/frontend/scripts/polygons.js";
// import "leaflet-polylineoffset";
// import "leaflet-polylinedecorator";

const props = defineProps({
  view: View, // Receive current view as a prop
  date: String,
  hour: Number,
});

const emit = defineEmits(["update:view"]);

const setView = (newView) => {
  emit("update:view", newView);
}

/** @type { Ref<Area[]> } */
const areas = ref([]);
/** @type { Ref<Crowding[]> } */
const crowding = ref([]);
/** @type { Ref<Attendance[]> } */
const attendance = ref([]);
/** @type { Ref<Movements[]> } */
const movements = ref([]);
/** @type { Ref<Medians[]> } */
const medians = ref([]);
/** @type { Ref<Polygon[]> } */
const polygons = ref([]);
/** @type { Ref<Polyline[]> } */
const polylines = ref([]);

// /** @type {Ref<string>} */

// const date = ref("2025-01-16");
// /** @type {Ref<number>} */
// const hour = ref(13);
const clickedPolygon = ref(null);

const lat = ref(0);
const lng = ref(0);
const map = ref();
const mapContainer = ref();
const data = {
  areas: areas,
  crowding: crowding,
  attendance: attendance,
  movements: movements,
  medians: medians,
  polygons: polygons,
  polylines: polylines,
  clickedPolygon: clickedPolygon,
};

onMounted(async () => {
  map.value = L.map(mapContainer.value).setView([44.4949, 11.3426], 13);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    minZoom: 2,
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map.value);

  map.value.zoomControl.setPosition("bottomright");

  // Prevent scroll propagation for buttons
  const buttonContainer = document.querySelector(".view-buttons");
  L.DomEvent.disableScrollPropagation(buttonContainer);

  await fetchAreas(areas);
  await fetchCrowdingAttendance(props.date, crowding, attendance);
  await fetchMovements(props.date, movements, medians);

  setupPolygons(props, data, map);
});

// const updatePolygons = (polygons, view) => {
//   polygons.forEach((polygon) => {
//     polygon.setStyle(mapOptionsFactory(view, 2000));
//   })
// }

watch(
    [() => props.view, () => props.date, () => props.hour],
    async ([newView, newDate, newHour], [oldView, oldDate, oldHour]) => {
      // updatePolygons(polygons.value, newValue)
      await updatePolygons(props, data, map, newView, newDate, newHour, oldDate);
    }
);

function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.watchPosition(position => {
      lat.value = position.coords.latitude;
      lng.value = position.coords.longitude;
      map.value.setView([lat.value, lng.value, 13]);

      L.marker([lat.value, lng.value], {draggable: false})
          .addTo(map.value)
          .on("dragend", (e) => {
            console.log(e);
          })
    })
  }
}
</script>

<template>
  <!--    <button @click="getLocation">Get Location</button>-->
  <!--  {{ lat }} , {{ lng }}-->

  <div ref="mapContainer" class="map">
    <ViewButtons @viewChange="setView"/>
  </div>
</template>

<style lang="scss" scoped>
.map {
  height: 100%;
  width: 100%;
  box-sizing: border-box;
  /* width: 100%;
  height: 100%; */
  /* border-radius: 14px; */
  //padding: 16px;
}
</style>