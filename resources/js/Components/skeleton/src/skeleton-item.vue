<template>
    <div class="hx-skeleton__item" :class="[`hx-skeleton__${variant}`]"></div>
</template>

<script lang="ts" setup>
const props = defineProps({
    variant: {
        type: String,
        values: [
            "circle",
            "rect",
            "h1",
            "h3",
            "text",
            "caption",
            "p",
            "image",
            "button",
            "card",
        ],
        default: "text",
    },
});
</script>
