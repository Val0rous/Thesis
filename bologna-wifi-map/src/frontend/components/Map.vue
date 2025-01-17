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
import {fetchAreas, fetchCrowdingAttendance} from "@/frontend/scripts/api.js";
import mapOptionsFactory from "@/frontend/scripts/mapOptionsFactory.js";
// import "leaflet-polylineoffset";
// import "leaflet-polylinedecorator";

const props = defineProps({
  view: View, // Receive current view as a prop
})

const emit = defineEmits(["update:view"]);

const setView = (newView) => {
  emit("update:view", newView);
}

/** @type {Ref<Area[]>} */
const areas = ref([]);
/** @type {Ref<Crowding[]>} */
const crowding = ref([]);
/** @type {Ref<Attendance[]>} */
const attendance = ref([]);
/** @type { Ref<Polygon<any>[]>} */
const polygons = ref([]);

const clickedPolygon = ref(null);
const lat = ref(0);
const lng = ref(0);
const map = ref();
const mapContainer = ref();

onMounted(async () => {
  map.value = L.map(mapContainer.value).setView([44.4949, 11.3426], 13);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    minZoom: 2,
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map.value);

  map.value.zoomControl.setPosition("bottomright");

  await fetchAreas(areas);
  await fetchCrowdingAttendance("2025-01-01", crowding, attendance);

  if (areas.value.length > 0) {
    areas.value.forEach((area) => {
      const polygon = L.polygon(area.coordinates, mapOptionsFactory(props.view, 2000)).addTo(map.value);
      polygons.value[area.zone_id] = polygon;
      polygon.bindPopup(`
      <b>${area.zone_name}</b></br>
      Coordinates: ${area.latitude},${area.longitude}
      `)
      // Add mouseover event to change style on hover
      polygon.on("mouseover", () => {
        if (clickedPolygon.value !== polygon) {
          polygon.setStyle(hoverOptions);
        }
      });

      // Add mouseout event to reset style when hover ends
      polygon.on("mouseout", () => {
        if (clickedPolygon.value !== polygon) {
          polygon.setStyle(mapOptionsFactory(props.view, 2000));
        }
      });

      // Add click event to zoom in on the polygon
      polygon.on("click", () => {
        if (clickedPolygon.value) {
          clickedPolygon.value.setStyle(defaultOptions);
        }
        clickedPolygon.value = polygon;
        polygon.setStyle(clickOptions);
        // Get the bounds of the polygon and zoom to it
        const bounds = polygon.getBounds();
        // Calculate area of the bounds (approximation based on lat/lng difference)
        const area = Math.abs(
            (bounds.getNorthEast().lat - bounds.getSouthWest().lat) *
            (bounds.getNorthEast().lng - bounds.getSouthWest().lng)
        );
        console.log(area * 1000000);

        // Determine the zoom level based on the area
        let targetZoom;
        if (area < 0.000001) {
          targetZoom = 20; // Very small polygons -> High zoom level
          console.log("High zoom")
        } else if (area < 0.00001) {
          targetZoom = 18; // Medium polygons -> Moderate zoom level
          console.log("Moderate zoom")
        } else {
          targetZoom = 16; // Large polygons -> Lower zoom level
          console.log("Low zoom")
        }

        map.value.fitBounds(bounds, {
          padding: [20, 20], // Add padding for a better view
          maxZoom: targetZoom, // Maximum zoom level
        });
      });
    })
  }

  // Prevent scroll propagation for buttons
  const buttonContainer = document.querySelector(".view-buttons");
  L.DomEvent.disableScrollPropagation(buttonContainer);

  // L.polyline([
  //   [44.495, 11.34],
  //   [44.497, 11.35],
  //   [44.493, 11.36],
  //   [44.495, 11.34],
  // ], defaultOptions)
  //     .addTo(map.value);

  // const polyline1 = L.polyline([
  //   [44.492098584136370, 11.343701612218704],
  //   [44.492918958032725, 11.344249912107363]
  // ], lineOptions).addTo(map.value)
  //
  // const polyline2 = L.polyline([
  //   [44.492918958032725, 11.344249912107363],
  //   [44.492098584136370, 11.343701612218704]
  // ], {
  //   color: "yellow",
  //   fillColor: "yellow",
  //   fillOpacity: 0.8,
  //   weight: 8,
  //   dashArray: "5, 10",
  // }).addTo(map.value)
})

const updatePolygons = (polygons, view) => {
  polygons.forEach((polygon) => {
    polygon.setStyle(mapOptionsFactory(view, 2000));
  })
}

watch(
    () => props.view,
    (newValue) => {
      // updatePolygons(polygons.value, newValue)
      areas.value.forEach((area) => {
        polygons.value[area.zone_id].setStyle(mapOptionsFactory(newValue, 2000));
      });
    }
)


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