<script setup lang="ts">
import DisplayHeading from './DisplayHeading.vue';
import SectionLabel from './SectionLabel.vue';
</script>
<template>
    <section
        id="bros"
        class="mx-auto grid max-w-400 grid-cols-1 items-center gap-12 border-t border-forge-line px-6 py-18 md:grid-cols-2 md:gap-20 md:px-[7%] md:py-26"
    >
        <div
            class="relative flex aspect-square items-center justify-center overflow-hidden border border-forge-line bg-forge-panel"
            aria-hidden="true"
        >
            <span
                class="absolute top-6 left-6 font-mono text-[9px] tracking-widest text-forge-muted"
                >EST. IN THE OFFICE</span
            >
            <div
                class="relative z-10 -rotate-12 font-display text-[clamp(7rem,17vw,17rem)] leading-none font-black tracking-[-.12em] text-forge-paper [&>span]:absolute [&>span]:-top-8 [&>span]:right-0 [&>span]:text-[.5em] [&>span]:text-forge-ember"
            >
                K<span>↗︎</span>B
            </div>
            <div
                class="absolute inset-[12%] rotate-[-25deg] rounded-full border border-forge-ember/40 opacity-15 shadow-[0_0_80px_var(--color-forge-ember)]"
            ></div>
            <span
                class="absolute bottom-6 left-6 font-mono text-[9px] leading-relaxed tracking-widest text-forge-muted"
                >TWO COLLEAGUES.<br />ONE SHARED OBSESSION.</span
            ><span class="absolute right-6 bottom-6 text-4xl text-forge-ember"
                >✳︎</span
            >
        </div>
        <div
            class="[&>p:not(:first-child)]:mt-6 [&>p:not(:first-child)]:text-sm [&>p:not(:first-child)]:leading-7 [&>p:not(:first-child)]:text-forge-muted"
        >
            <SectionLabel>02 / MEET THE MAKERS</SectionLabel
            ><DisplayHeading
                >IM BÜRO<br />KOLLEGEN.<br /><span class="text-forge-ember"
                    >AM HERD BROS.</span
                ></DisplayHeading
            >
            <p>
                Wir sind zwei Kollegen mit einer Schwäche für gutes Fleisch,
                kräftige Röstaromen und ordentlich was auf dem Teller. Und wir
                kochen am liebsten für unsere Crew.
            </p>
            <p>
                Also machen wir aus der Mittagspause etwas, worauf man sich
                freut. Mit saftigen Cuts, ehrlichen Beilagen und einem Platz für
                dich.
            </p>
            <div
                class="mt-8 flex items-center gap-4 font-mono text-[10px] tracking-wide text-forge-paper [&>span]:text-3xl [&>span]:text-forge-ember"
            >
                <span>✳︎</span> KEINE ALLÜREN. NUR APPETIT.
            </div>
        </div>
    </section>
</template>
