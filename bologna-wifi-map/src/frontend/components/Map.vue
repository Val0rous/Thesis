<script setup>
import {onMounted, ref} from "vue"
import L from "leaflet"
import "leaflet-polylineoffset";
import "leaflet-polylinedecorator";

/**
 * @typedef {Object} Area
 * @property {string} zone_id - ID of the area
 * @property {string} zone_name - Name of the area
 * @property {number} latitude - Latitude of the area
 * @property {number} longitude - Longitude of the area
 * @property {[number, number][]} coordinates - Coordinates of the area polygon
 * @type {import("vue").Ref<Area[]>}
 */
const areas = ref([]);
const polygons = ref([]);

const fetchAreas = async () => {
  try {
    const response = await fetch("http://localhost/backend/api/areas.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      }
    });
    const data = await response.json();
    if (data.success) {
      areas.value = data.results;
    } else {
      console.error("Failed to fetch areas: ", data.message)
    }
  } catch (error) {
    console.error("Failed to fetch areas: an error occurred.\n");
  }
}

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

  let defaultOptions = {
    color: "blue",
    fillColor: "blue",
    fillOpacity: 0.3,
  };

  let hoverOptions = {
    color: "red",
    fillColor: "red",
    fillOpacity: 0.5,
  }

  let lineOptions = {
    color: "orange",
    fillColor: "orange",
    fillOpacity: 0.8,
    weight: 8,
  }

  await fetchAreas();
  if (areas.value.length > 0) {
    areas.value.forEach((/** @type {Area} */ area) => {
      const polygon = L.polygon(area.coordinates, defaultOptions).addTo(map.value);
      polygons.value[area.zone_id] = polygon;
      polygon.bindPopup(`
      <b>${area.zone_name}</b></br>
      Coordinates: ${area.latitude},${area.longitude}
      `)
      // Add mouseover event to change style on hover
      polygon.on("mouseover", () => {
        polygon.setStyle(hoverOptions);
      });

      // Add mouseout event to reset style when hover ends
      polygon.on("mouseout", () => {
        polygon.setStyle(defaultOptions);
      });
    })
  }
  L.polyline([
    [44.495, 11.34],
    [44.497, 11.35],
    [44.493, 11.36],
    [44.495, 11.34],
  ], defaultOptions)
      .addTo(map.value);

  const polyline1 = L.polyline([
    [44.492098584136370, 11.343701612218704],
    [44.492918958032725, 11.344249912107363]
  ], lineOptions).addTo(map.value)

  const polyline2 = L.polyline([
    [44.492918958032725, 11.344249912107363],
    [44.492098584136370, 11.343701612218704]
  ], {
    color: "yellow",
    fillColor: "yellow",
    fillOpacity: 0.8,
    weight: 8,
    dashArray: "5, 10",
  }).addTo(map.value)
})

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

  <div ref="mapContainer" class="map"></div>
</template>

<style scoped>
.map {
  width: 100vw;
  height: 100vh;
  /* border-radius: 14px; */
  padding: 16px;
}
</style>