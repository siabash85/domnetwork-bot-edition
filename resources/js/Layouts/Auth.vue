<template>
    <div>
        <RouterView />
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useDisplay } from "vuetify";
const { mobile, smAndDown, mdAndDown } = useDisplay();

const drawer = ref(true);
onMounted(() => {
    if (mobile.value) {
        drawer.value = false;
    }
});
</script>

<style></style>
