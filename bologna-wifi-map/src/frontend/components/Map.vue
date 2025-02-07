<script setup>
import "@/utils/types.js";
import {onMounted, ref, defineProps, defineEmits, watch, computed} from "vue";
import L from "leaflet";
import ViewButtons from "@/components/ViewButtons.vue"
import Layers from "@/components/Layers.vue";
import View from "@/utils/views.js";
import Maps from "@/utils/maps.js";
import {
  defaultOptions,
  hoverOptions,
  clickOptions,
  lineOptions,
} from "@/utils/mapOptions.js";
import {fetchAreas, fetchCrowdingAttendance, fetchMovements} from "@/scripts/api.js";
import mapOptionsFactory from "@/scripts/mapOptionsFactory.js";
import {setupPolygons, updatePolygons} from "@/scripts/polygons.js";
import maps from "@/utils/maps.js";
// import "leaflet-polylineoffset";
// import "leaflet-polylinedecorator";

const props = defineProps({
  view: View, // Receive current view as a prop
  maps: Maps,
  date: String,
  hour: Number,
});

const emit = defineEmits(["update:view", "update:maps"]);

const setView = (newView) => {
  emit("update:view", newView);
}

const setMapLayer = (newLayer) => {
  emit("update:maps", newLayer);
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
const mapTilerApiKey = 'sGIp9VsEALaxJyTFqagx';
const thunderforestApiKey = '61c2e4cabac64561b6187e7869981efe';
const mapStyle = {
  openStreetMap: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
  openTopoMap: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
  esriStreets: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
  esriStreetsDark: 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}',
  esriSatellite: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
  esriTopographic: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
  // esriTerrain: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Terrain_Base/MapServer/tile/{z}/{y}/{x}',
  mapTilerStreets: `https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=${mapTilerApiKey}`,
  mapTilerTopo: `https://api.maptiler.com/maps/topo-v2/{z}/{x}/{y}.png?key=${mapTilerApiKey}`,
  mapTilerDataviz: `https://api.maptiler.com/maps/dataviz/{z}/{x}/{y}.png?key=${mapTilerApiKey}`,
  mapTilerBasic: `https://api.maptiler.com/maps/basic-v2/{z}/{x}/{y}.png?key=${mapTilerApiKey}`,
  mapTilerSatellite: `https://api.maptiler.com/maps/satellite/{z}/{x}/{y}.jpg?key=${mapTilerApiKey}`,
  mapTilerHybrid: `https://api.maptiler.com/maps/hybrid/{z}/{x}/{y}.jpg?key=${mapTilerApiKey}`,
  // usgsSatellite: 'https://basemap.nationalmap.gov/arcgis/rest/services/USGSImageryOnly/MapServer/tile/{z}/{y}/{x}',
  thunderforestTransport: `https://tile.thunderforest.com/transport/{z}/{x}/{y}.png?apikey=${thunderforestApiKey}`,
  thunderforestLandscape: `https://tile.thunderforest.com/landscape/{z}/{x}/{y}.png?apikey=${thunderforestApiKey}`,
  thunderforestAtlas: `https://tile.thunderforest.com/atlas/{z}/{x}/{y}.png?apikey=${thunderforestApiKey}`,
  thunderforestNeighborhood: `https://tile.thunderforest.com/neighbourhood/{z}/{x}/{y}.png?apikey=${thunderforestApiKey}`,
}

const attributions = {
  openStreetMap: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  openTopoMap: '&copy; <a href="https://opentopomap.org">OpenTopoMap</a> & contributors',
  esri: '&copy; <a href="https://www.esri.com/">Esri</a> & contributors',
  mapTiler: '&copy; <a href="https://www.maptiler.com/">MapTiler</a>',
  // usgs: '&copy; <a href="https://www.usgs.gov/">USGS</a>'
  thunderforest: '&copy; <a href="https://www.thunderforest.com/">Thunderforest</a>',
}

const tileLayer = ref(null);

onMounted(async () => {
  map.value = L.map(mapContainer.value).setView([44.4949, 11.3426], 13);
  tileLayer.value = L.tileLayer(mapStyle.esriStreets, {
    minZoom: 2,
    maxZoom: 19,
    attribution: attributions.esri,
    errorTileUrl: 'https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg' // Optional placeholder
  }).addTo(map.value);

  const fallbackLayer = L.tileLayer(mapStyle.openStreetMap, {
    minZoom: 2,
    maxZoom: 19,
    attribution: attributions.openStreetMap
  });

  tileLayer.value.on("tileerror", function (e) {
    console.error("Map tile failed. Switching to OpenStreetMap...", e);
    map.value.removeLayer(tileLayer);
    fallbackLayer.addTo(map.value);
  });
  map.value.zoomControl.setPosition("topright");

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
    () => props.maps,
    (newMapLayer) => {
      console.log(props.maps, "to", newMapLayer);
      const currentMapStyle = computed(() => {
        switch (newMapLayer) {
          case Maps.Default:
            return mapStyle.esriStreets;
          case Maps.Satellite:
            return mapStyle.esriSatellite;
          case Maps.Terrain:
            return mapStyle.esriTopographic;
          default:
            return mapStyle.openStreetMap;
        }
      });
      map.value.removeLayer(tileLayer.value);
      tileLayer.value = L.tileLayer(currentMapStyle.value, {
        minZoom: 2,
        maxZoom: 19,
        attribution: attributions.esri,
        errorTileUrl: 'https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg' // Optional placeholder
      }).addTo(map.value);
      const fallbackLayer = L.tileLayer(mapStyle.openStreetMap, {
        minZoom: 2,
        maxZoom: 19,
        attribution: attributions.openStreetMap
      });

      tileLayer.value.on("tileerror", function (e) {
        console.error("Map tile failed. Switching to OpenStreetMap...", e);
        map.value.removeLayer(tileLayer);
        fallbackLayer.addTo(map.value);
      })
    }
)

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
    <Layers @mapChange="setMapLayer"/>
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