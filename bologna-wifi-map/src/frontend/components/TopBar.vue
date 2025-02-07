<script setup>
import {computed, ref} from "vue";
import {defineProps, defineEmits} from "vue";
import DateIcon from "./icons/IconDate.vue";
import TimeIcon from "./icons/IconTime.vue";
import MenuIcon from "./icons/IconMenu.vue";
import PlusIcon from "./icons/IconPlus.vue";
import MinusIcon from "./icons/IconMinus.vue";
import {calculateDate} from "@/utils/utils.js";
import {useDark} from "@vueuse/core";
import IconArrowLeft from "@/components/icons/IconArrowLeft.vue";
import IconArrowRight from "@/components/icons/IconArrowRight.vue";
import IconKeyboardArrowLeft from "@/components/icons/IconKeyboardArrowLeft.vue";
import IconKeyboardArrowRight from "@/components/icons/IconKeyboardArrowRight.vue";

const props = defineProps({
  date: String,
  hour: Number,
});

const emit = defineEmits(["update:date", "update:hour"]);

const date = ref(props.date);
const hour = ref(props.hour);

const updateDate = () => {
  emit("update:date", date.value);
};

const updateHour = () => {
  emit("update:hour", hour.value);
};

const increaseHour = () => {
  if (hour.value !== 23) {
    hour.value += 1;
    emit("update:hour", hour.value);
  } else {
    hour.value = 0
    emit("update:hour", hour.value);
    date.value = calculateDate(date.value, 1);
    emit("update:date", date.value);
  }
}

const decreaseHour = () => {
  if (hour.value !== 0) {
    hour.value -= 1
    emit("update:hour", hour.value);
  } else {
    hour.value = 23
    emit("update:hour", hour.value);
    date.value = calculateDate(date.value, -1);
    emit("update:date", date.value);
  }
}

const isDark = useDark();
const lightLogo = new URL('@/frontend/assets/opendata.png', import.meta.url).href;
const darkLogo = new URL('@/frontend/assets/opendata_bw.png', import.meta.url).href;
const currentLogo = computed(() => (isDark.value ? darkLogo : lightLogo));
</script>

<template>
  <div class="header">
    <div class="menu">
      <MenuIcon class="icon"/>
      Bologna WiFi Map
    </div>
    <div class="logo">
      <img :src="currentLogo" alt="Open Data"/>
    </div>
  </div>
  <div class="datetime-setup">
    <div class="date-setup">
      <DateIcon class="icon"/>
      <!--      <label for="date">Date:</label>-->
      <input id="date" v-model="date" type="date" @change="updateDate"/>
    </div>

    <div class="time-setup">
      <TimeIcon class="icon"/>
      <!--      <label for="hour">Hour:</label>-->
      <IconKeyboardArrowLeft class="variation-icon minus icon" @click="decreaseHour"/>
      <input id="hour" v-model.number="hour" max="23" min="0" type="number" @change="updateHour"/>
      <IconKeyboardArrowRight class="variation-icon plus icon" @click="increaseHour"/>
    </div>
  </div>
</template>