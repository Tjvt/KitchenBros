<script setup lang="ts">
withDefaults(defineProps<{ href?: string; light?: boolean }>(), {
    href: '#mitessen',
    light: false,
});
</script>
<template>
    <a
        :href="href"
        class="group inline-flex min-h-13 items-center justify-between gap-7 border border-transparent px-6 py-4 text-xs font-bold tracking-wide uppercase transition duration-200 hover:-translate-y-0.5 hover:border-forge-copper hover:bg-forge-copper hover:text-forge-void"
        :class="
            light
                ? 'bg-forge-paper text-forge-void'
                : 'bg-forge-ember text-forge-paper'
        "
        ><slot /><span
            class="text-xl leading-none transition-transform group-hover:translate-x-0.5"
            aria-hidden="true"
            >↗︎</span
        ></a
    >
</template>
