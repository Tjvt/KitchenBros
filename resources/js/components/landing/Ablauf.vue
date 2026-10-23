<script setup lang="ts">
import DisplayHeading from './DisplayHeading.vue';
import SectionLabel from './SectionLabel.vue';
import OrbSignal from './OrbSignal.vue';
import type { OrbState } from 'thinking-orbs-vue3';
const steps: {
    number: string;
    title: string;
    text: string;
    state: OrbState;
    code: string;
}[] = [
    {
        number: '01',
        title: 'DU SAGST JA.',
        text: 'Dein Name auf der Liste. Mehr brauchen wir nicht. Dein Hunger ist die einzige Voraussetzung.',
        state: 'listening',
        code: 'CONNECT',
    },
    {
        number: '02',
        title: 'WIR DREHEN AUF.',
        text: 'Zutaten besorgen, Messer wetzen, Herd an. Wir kümmern uns um alles, was auf deinen Teller kommt.',
        state: 'composing',
        code: 'CREATE',
    },
    {
        number: '03',
        title: 'ALLE AN EINEN TISCH.',
        text: 'Laptop zu. Besteck raus. Gutes Essen, gute Gespräche und endlich eine Pause, die sich so anfühlt.',
        state: 'shaping',
        code: 'ENJOY',
    },
];
</script>
<template>
    <section
        class="mx-auto max-w-400 border-t border-forge-line px-6 py-18 md:px-[7%] md:py-26"
    >
        <SectionLabel>DAS PRINZIP IST EINFACH.</SectionLabel>
        <div
            class="mb-9 flex flex-col justify-between gap-6 md:mb-12 md:flex-row md:items-end [&>p]:text-sm [&>p]:leading-relaxed [&>p]:text-forge-muted"
        >
            <DisplayHeading
                >DREI SCHRITTE.<br /><span class="outline-text"
                    >VOLLER TELLER.</span
                ></DisplayHeading
            ><span class="font-mono text-[9px] tracking-wider text-forge-muted"
                >LOW EFFORT. HIGH FLAVOR. ↙</span
            >
        </div>
        <div
            class="grid grid-cols-1 border border-forge-line md:grid-cols-3 [&_article:not(:last-child)]:border-b md:[&_article:not(:last-child)]:border-r md:[&_article:not(:last-child)]:border-b-0"
        >
            <article
                class="group relative border-forge-line p-7"
                v-for="step in steps"
                :key="step.number"
            >
                <div
                    class="flex items-center justify-between gap-3 font-mono text-[9px] tracking-wide text-forge-copper"
                >
                    <span>{{ step.number }} / {{ step.code }}</span
                    ><OrbSignal :state="step.state" :size="60" :paused="true" />
                </div>
                <h3 class="mt-7 mb-3 text-lg font-black tracking-tight">
                    {{ step.title }}
                </h3>
                <p class="max-w-72 text-xs leading-7 text-forge-muted">
                    {{ step.text }}
                </p>
                <span
                    class="mt-8 block h-px w-10 bg-forge-ember"
                    aria-hidden="true"
                ></span>
            </article>
        </div>
    </section>
</template>
