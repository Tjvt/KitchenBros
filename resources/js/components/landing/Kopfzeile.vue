<script setup lang="ts">
import BrandMark from './BrandMark.vue';
import ActionLink from './ActionLink.vue';
</script>
<template>
    <header
        class="relative z-20 mx-auto flex h-24 max-w-400 items-center justify-between gap-4 border-b border-forge-line px-6 md:h-28 md:px-[5%] [&_nav>a:not(:last-child)]:hidden [&_nav>a:not(:last-child)]:text-[10px] [&_nav>a:not(:last-child)]:tracking-widest [&_nav>a:not(:last-child)]:text-forge-muted md:[&_nav>a:not(:last-child)]:block [&_nav>a>span:first-child:not(:last-child)]:mr-2 [&_nav>a>span:first-child:not(:last-child)]:text-forge-ember"
    >
        <BrandMark />
        <nav class="flex items-center gap-9" aria-label="Hauptnavigation">
            <a href="#menue"><span>01</span> DAS MENÜ</a
            ><a href="#bros"><span>02</span> DIE BROS</a
            ><ActionLink>PLATZ SICHERN</ActionLink>
        </nav>
    </header>
</template>
