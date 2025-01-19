<script setup>
import {ref} from "vue";
import {defineProps, defineEmits} from "vue";

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
</script>

<template>
  <div class="header">
    <div class="menu">
      <span class="material-symbols-rounded">&#xe5d2;</span>
      Bologna WiFi Map
    </div>
    <div class="logo">
      <img alt="OpenData" src="@/frontend/assets/opendata.png"/>
    </div>
  </div>
  <div class="datetime-setup">
    <label for="date">Date:</label>
    <input id="date" v-model="date" type="date" @change="updateDate"/>

    <label for="hour">Hour:</label>
    <input id="hour" v-model.number="hour" max="23" min="0" type="number" @change="updateHour"/>
  </div>
</template>