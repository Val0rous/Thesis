<script setup>
import LayersIcon from "./icons/IconLayers.vue";
import {ref} from "vue";
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
        <span class="icon">
          <IconMap/>
        </span>
        Default
      </button>
      <button :class="{ selected: selectedMap === Maps.Satellite }" @click="changeMap(Maps.Satellite)">
        <span class="icon">
          <IconSatelliteAlt/>
        </span>
        Satellite
      </button>
      <button :class="{ selected: selectedMap === Maps.Terrain }" @click="changeMap(Maps.Terrain)">
        <span class="icon">
          <IconLandscape/>
        </span>
        Terrain
      </button>
    </div>
  </div>
</template>

<style scoped>

</style>