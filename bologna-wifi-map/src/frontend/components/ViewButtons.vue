<script setup>
import {defineProps, defineEmits, ref, watch} from "vue";
import CrowdingIcon from "./icons/IconCrowding.vue";
import AttendanceIcon from "./icons/IconAttendance.vue";
import MovementsIcon from "./icons/IconMovements.vue";
import View from "@/frontend/utils/views.js";


const props = defineProps({
  currentView: View,
})
const emit = defineEmits(["viewChange"]);

/** @type {Ref<View>} */
const selectedView = ref(View.Areas);

const changeView = (newView) => {
  const oldView = selectedView.value;
  let view;
  if (oldView !== newView) {
    view = newView;
  } else {
    view = View.Areas;
  }
  console.log(oldView, "changed to", view);
  selectedView.value = view;
  emit("viewChange", view);
}
</script>

<template>
  <div class="view-buttons" @touchstart.stop @touchmove.stop>
    <button :class="{ selected: selectedView === View.Crowding }"
            class="map-button crowding"
            @click="changeView(View.Crowding)">
      <span class="icon">
        <CrowdingIcon/>
      </span>
      Crowding
    </button>
    <button :class="{ selected: selectedView === View.Attendance }"
            class="map-button attendance"
            @click="changeView(View.Attendance)">
      <span class="icon">
      <slot>
        <AttendanceIcon/>
      </slot>
        </span>
      Attendance
    </button>

    <button :class="{ selected: selectedView === View.Movements }"
            class="map-button movements"
            @click="changeView(View.Movements)">
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