<script setup>
import {onMounted, ref} from "vue"
import L from "leaflet"

const areas = ref([]);

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
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map.value);
  let options = {
    color: "blue",
    fillColor: "blue",
    fillOpacity: 0.2,
  };
  await fetchAreas();
  if (areas.value.length > 0) {
    areas.value.forEach((area) => {
      L.polygon(area.coordinates, options).addTo(map.value);
    })
  }
  // L.polygon([
  //   [44.495, 11.34],
  //   [44.497, 11.35],
  //   [44.493, 11.36],
  //   [44.495, 11.34],
  // ], options).addTo(map.value);
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