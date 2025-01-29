<script setup>
import LayersIcon from "./icons/IconLayers.vue";
import {defineProps, defineEmits, ref} from "vue";
import IconMap from "@/frontend/components/icons/IconMap.vue";
import IconSatelliteAlt from "@/frontend/components/icons/IconSatelliteAlt.vue";
import IconLandscape from "@/frontend/components/icons/IconLandscape.vue";
import IconClose from "@/frontend/components/icons/IconClose.vue";
import Maps from "@/frontend/utils/maps.js";

const isPickerVisible = ref(false);
const showPicker = () => {
  isPickerVisible.value = true;
};
const hidePicker = () => {
  isPickerVisible.value = false;
};

const props = defineProps({
  currentMap: Maps,
});
const emit = defineEmits(["mapChange"]);

const selectedMap = ref(Maps.Default);

const changeMap = (newMap) => {
  selectedMap.value = newMap;
  emit("mapChange", newMap);
  hidePicker();
}
</script>

<template>
  <div class="layers">
    <button @click="showPicker">
      <LayersIcon/>
    </button>
  </div>
  <div v-if="isPickerVisible" class="layer-picker">
    <div class="layer-picker-topbar">
      <span class="label">Map type</span>
      <span class="icon">
        <IconClose @click="hidePicker"/>
      </span>
    </div>
    <div class="layer-picker-bottombar">
      <button :class="{ selected: selectedMap === Maps.Default }" @click="changeMap(Maps.Default)">
        <!--        <span class="icon">-->
        <!--          <IconMap/>-->
        <!--        </span>-->
        <span class="icon">
          <img alt="Default" src="./icons/streets_96.png"/>
        </span>
        Default
      </button>
      <button :class="{ selected: selectedMap === Maps.Satellite }" @click="changeMap(Maps.Satellite)">
        <!--        <span class="icon">-->
        <!--          <IconSatelliteAlt/>-->
        <!--        </span>-->
        <span class="icon">
          <img alt="Satellite" src="./icons/satellite_96.png"/>
        </span>
        Satellite
      </button>
      <button :class="{ selected: selectedMap === Maps.Terrain }" @click="changeMap(Maps.Terrain)">
        <!--        <span class="icon">-->
        <!--          <IconLandscape/>-->
        <!--        </span>-->
        <span class="icon">
          <img alt="Terrain" src="./icons/terrain_96.png"/>
        </span>
        Terrain
      </button>
    </div>
  </div>
</template>

<style scoped>

</style>