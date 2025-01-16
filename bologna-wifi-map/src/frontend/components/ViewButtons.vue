<script setup>
import {defineProps, defineEmits} from "vue";
import CrowdingIcon from "./icons/IconCrowding.vue";
import AttendanceIcon from "./icons/IconAttendance.vue";

defineProps({
  currentView: String,
})
const emit = defineEmits(["viewChange"]);
const changeView = (view) => {
  emit("viewChange", view);
}
</script>

<template>
  <div class="view-buttons">
    <button class="map-button" @click="changeView('Crowding')">
      <span class="icon">
        <slot>
          <CrowdingIcon/>
        </slot>
      </span>
      Crowding
    </button>
    <button class="map-button" @click="changeView('Attendance')">
      <span class="icon">
      <slot>
        <AttendanceIcon/>
      </slot>
        </span>
      Attendance
    </button>
  </div>
</template>

<style lang="scss" scoped>

</style>