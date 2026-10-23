<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { ThinkingOrb } from 'thinking-orbs-vue3';
import type { OrbState } from 'thinking-orbs-vue3';
const props = withDefaults(
    defineProps<{
        state: OrbState;
        size?: number;
        speed?: number;
        paused?: boolean;
        tone?: 'ember' | 'success';
    }>(),
    {
        size: 56,
        speed: 0.65,
        paused: false,
        tone: 'ember',
    },
);
const color = ref<string>();
function syncColor() {
    color.value = getComputedStyle(document.documentElement)
        .getPropertyValue(`--color-forge-${props.tone}`)
        .trim();
}
onMounted(syncColor);
watch(() => props.tone, syncColor);
</script>
<template>
    <span
        class="inline-flex shrink-0 items-center justify-center"
        aria-hidden="true"
        ><ThinkingOrb
            :state="state"
            :size="size"
            :speed="speed"
            :paused="paused"
            :color="color"
            theme="dark"
    /></span>
</template>
