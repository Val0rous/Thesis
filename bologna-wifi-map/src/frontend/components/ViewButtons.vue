<script setup>
import {defineProps, defineEmits} from "vue";
import CrowdingIcon from "./icons/IconCrowding.vue";
import AttendanceIcon from "./icons/IconAttendance.vue";
import MovementsIcon from "./icons/IconMovements.vue";
import View from "@/frontend/utils/views.js";


defineProps({
  currentView: String,
})
const emit = defineEmits(["viewChange"]);
const changeView = (view) => {
  emit("viewChange", view);
}
</script>

<template>
  <div class="view-buttons" @touchstart.stop @touchmove.stop>
    <button class="map-button" @click="changeView(View.Crowding)">
      <span class="icon">
        <slot>
          <CrowdingIcon/>
        </slot>
      </span>
      Crowding
    </button>
    <button class="map-button" @click="changeView(View.Attendance)">
      <span class="icon">
      <slot>
        <AttendanceIcon/>
      </slot>
        </span>
      Attendance
    </button>

    <button class="map-button" @click="changeView(View.Movements)">
      <span class="icon">
      <slot>
        <MovementsIcon/>
      </slot>
        </span>
      Movements
    </button>
  </div>
</template>

<style lang="scss" scoped>

</style>