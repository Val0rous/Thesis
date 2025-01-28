<script setup>
import LayersIcon from "./icons/IconLayers.vue";
import {ref} from "vue";

const isPickerVisible = ref(false);
const togglePickerVisibility = () => {
  isPickerVisible.value = !isPickerVisible.value;
}
</script>

<template>
  <div class="layers">
    <button>
      <LayersIcon @click="togglePickerVisibility"/>
    </button>
    <div v-if="isPickerVisible" class="layer-picker">
      <button>

      </button>
    </div>
  </div>
</template>

<style scoped>

</style>