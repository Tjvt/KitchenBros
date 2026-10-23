<script setup lang="ts">
import { computed, ref } from 'vue';
import type { OrbState } from 'thinking-orbs-vue3';
import ActionLink from './ActionLink.vue';
import SectionLabel from './SectionLabel.vue';
import FlavorReactor from './FlavorReactor.vue';
import OrbSignal from './OrbSignal.vue';

const modes: {
    state: OrbState;
    label: string;
    description: string;
    code: string;
    glyph: string;
    activeClass: string;
}[] = [
    {
        state: 'working',
        label: 'Anfeuern',
        description: 'Die Küche läuft heiß.',
        code: 'IGNITION',
        glyph: '✳',
        activeClass:
            'border-forge-ember bg-forge-ember/10 text-forge-ember shadow-[inset_0_-2px_0_var(--color-forge-ember)]',
    },
    {
        state: 'searching',
        label: 'Entdecken',
        description: 'Immer hungrig auf Neues.',
        code: 'DISCOVERY',
        glyph: '⌕',
        activeClass:
            'border-forge-copper bg-forge-copper/10 text-forge-copper shadow-[inset_0_-2px_0_var(--color-forge-copper)]',
    },
    {
        state: 'solving',
        label: 'Tüfteln',
        description: 'Bis jeder Bissen sitzt.',
        code: 'PRECISION',
        glyph: '⊙',
        activeClass:
            'border-forge-paper bg-forge-paper/10 text-forge-paper shadow-[inset_0_-2px_0_var(--color-forge-paper)]',
    },
    {
        state: 'listening',
        label: 'Verbinden',
        description: 'Gutes Essen bringt uns zusammen.',
        code: 'CONNECTION',
        glyph: '∿',
        activeClass:
            'border-forge-success bg-forge-success/10 text-forge-success shadow-[inset_0_-2px_0_var(--color-forge-success)]',
    },
    {
        state: 'composing',
        label: 'Würzen',
        description: 'Aus Zutaten wird Vorfreude.',
        code: 'CREATION',
        glyph: '✦',
        activeClass:
            'border-forge-copper bg-forge-copper/10 text-forge-copper shadow-[inset_0_-2px_0_var(--color-forge-copper)]',
    },
    {
        state: 'shaping',
        label: 'Servieren',
        description: 'Bereit für deinen großen Hunger.',
        code: 'SERVING',
        glyph: '↗',
        activeClass:
            'border-forge-error bg-forge-error/10 text-forge-error shadow-[inset_0_-2px_0_var(--color-forge-error)]',
    },
];
const selected = ref(0);
const intensity = ref(65);
const paused = ref(false);
const mode = computed(() => modes[selected.value]);
</script>
<template>
    <section
        id="start"
        class="relative mx-auto grid max-w-400 grid-cols-1 px-6 pt-8 md:grid-cols-2 md:px-[5%] md:pt-10"
    >
        <div
            class="relative z-10 mb-8 flex items-center justify-between gap-5 md:col-span-2 md:mb-12"
        >
            <SectionLabel>FIRE UP YOUR LUNCH.</SectionLabel
            ><span
                class="hidden font-mono text-[9px] tracking-widest text-forge-muted sm:block"
                >KITCHEN BROS / VOL. 001</span
            >
        </div>
        <div class="relative z-10 md:pb-12 [&_h1>span]:text-forge-ember">
            <h1
                class="font-display text-[clamp(4.3rem,9.6vw,9.2rem)] leading-[.84] font-black tracking-[-.075em]"
            >
                FEUER.<br />FLEISCH.<br /><span>MITTAG.</span>
            </h1>
            <p
                class="mt-7 text-sm leading-relaxed text-forge-muted md:mt-9 md:text-base"
            >
                Deine Mittagspause verdient ein Upgrade.<br />Zwei Kollegen.
                Saftiges Fleisch. Volles Brett.
            </p>
            <div class="mt-7 flex flex-wrap items-center gap-6 md:mt-8">
                <ActionLink>Ich will mitessen</ActionLink
                ><a
                    class="inline-flex items-center gap-3 text-[11px] text-forge-paper transition-colors hover:text-forge-ember"
                    href="#menue"
                    >Entdecke die Mission <span>↓</span></a
                >
            </div>
            <div
                class="mt-7 flex items-center gap-2 font-mono text-[8px] tracking-[.12em] text-forge-muted"
            >
                <span
                    class="inline-block size-1.5 shrink-0 rounded-full bg-forge-ember shadow-[0_0_9px_var(--color-forge-ember)]"
                ></span>
                NUR DEIN NAME. DANN BIST DU DABEI.
            </div>
        </div>
        <div
            class="relative min-w-0 self-center md:-ml-10 md:h-full md:min-h-120"
        >
            <span
                class="pointer-events-none absolute top-7 left-5 z-20 flex gap-4 font-mono text-[8px] tracking-[.13em] text-forge-muted md:top-0 [&>span]:text-forge-ember"
                >KB—01 <span>FLAVOR CORE</span></span
            >
            <FlavorReactor
                :state="mode.state"
                :intensity="intensity"
                :paused="paused"
            />
            <span
                class="pointer-events-none absolute top-1/2 -right-7 z-20 origin-center rotate-90 font-mono text-[8px] tracking-[.13em] text-forge-muted max-md:hidden"
                >100% HANDMADE / 0% BORING</span
            >
            <div
                class="absolute bottom-8 left-2 z-20 flex items-center gap-3 text-[9px] leading-relaxed tracking-wide text-forge-muted md:bottom-12 md:left-10"
            >
                <span class="text-3xl font-light text-forge-ember">+</span
                ><span
                    >ECHTES FEUER.<br /><strong
                        class="font-semibold text-forge-paper"
                        >VOLLER GESCHMACK.</strong
                    ></span
                >
            </div>
            <div
                class="absolute right-1 bottom-8 z-20 flex flex-col items-end gap-1 font-mono text-[8px] tracking-widest text-forge-muted md:right-5 md:bottom-12 [&>strong]:text-4xl [&>strong]:font-light [&>strong]:tracking-tighter [&>strong]:text-forge-paper"
            >
                <span>ENERGY LEVEL</span
                ><strong>{{ intensity }}<small class="text-sm">%</small></strong
                ><span
                    class="mt-2 flex gap-0.5 [&_i.lit]:bg-forge-ember"
                    aria-hidden="true"
                    ><i
                        class="h-3 w-1 bg-forge-line"
                        v-for="n in 12"
                        :key="n"
                        :class="{ lit: n <= intensity / 8.34 }"
                    ></i
                ></span>
            </div>
        </div>
        <div
            class="relative z-20 grid grid-cols-1 border border-forge-line bg-forge-panel md:col-span-2 lg:grid-cols-[1.1fr_1.5fr_.9fr]"
        >
            <div
                class="flex items-center gap-3 border-b border-forge-line px-4 py-4 lg:border-r lg:border-b-0"
            >
                <OrbSignal
                    :state="mode.state"
                    :size="62"
                    :speed="0.3 + intensity / 65"
                    :paused="paused"
                />
                <div>
                    <span
                        class="font-mono text-[8px] tracking-widest text-forge-muted"
                        >DER GESCHMACKSREAKTOR</span
                    ><strong
                        class="mt-1 flex items-center gap-3 font-mono text-xs tracking-widest"
                        >{{ mode.code
                        }}<span
                            class="size-1 rounded-full bg-forge-copper"
                        ></span
                    ></strong>
                    <p
                        class="mt-1 text-[10px] text-forge-muted"
                        aria-live="polite"
                    >
                        {{ mode.description }}
                    </p>
                </div>
            </div>
            <div
                class="grid grid-cols-3 divide-x divide-forge-line border-b border-forge-line lg:border-r lg:border-b-0"
                role="group"
                aria-label="Reaktormodus"
            >
                <button
                    class="group relative flex min-h-16 cursor-pointer flex-col justify-center gap-1 border-t-2 border-transparent px-3 py-2.5 text-left text-[10px] text-forge-muted transition duration-300 hover:bg-forge-raised focus-visible:z-10 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-forge-copper"
                    v-for="(item, index) in modes"
                    :key="item.state"
                    type="button"
                    :aria-pressed="selected === index"
                    :class="
                        selected === index
                            ? item.activeClass
                            : 'hover:text-forge-paper'
                    "
                    @click="selected = index"
                >
                    <span
                        class="flex w-full items-center justify-between font-mono text-[8px] tracking-widest opacity-70"
                        ><span>{{ item.glyph }}</span
                        ><span>0{{ index + 1 }}</span></span
                    ><span class="font-medium">{{ item.label }}</span>
                </button>
            </div>
            <div
                class="flex flex-col justify-center gap-3 px-5 py-4 [&_label>span]:text-forge-copper"
            >
                <label
                    class="flex justify-between gap-3 font-mono text-[8px] tracking-wider text-forge-muted"
                    for="reactor-energy"
                    >WÜRZE AUFDREHEN <span>{{ intensity }}%</span></label
                ><input
                    class="h-1 w-full cursor-pointer accent-forge-ember"
                    id="reactor-energy"
                    v-model.number="intensity"
                    type="range"
                    min="10"
                    max="100"
                    step="1"
                /><button
                    type="button"
                    class="cursor-pointer self-start font-mono text-[8px] text-forge-muted transition-colors hover:text-forge-paper"
                    :aria-pressed="paused"
                    @click="paused = !paused"
                >
                    {{
                        paused ? '▷ Animation starten' : 'Ⅱ Animation pausieren'
                    }}
                </button>
            </div>
        </div>
        <div
            class="flex items-center justify-between gap-4 py-6 font-mono text-[8px] tracking-widest text-forge-muted md:col-span-2 [&>span:last-child]:hidden sm:[&>span:last-child]:block"
        >
            <span>SCROLL TO FEED YOUR CURIOSITY</span
            ><a
                class="flex size-7 items-center justify-center border border-forge-line text-base"
                href="#menue"
                aria-label="Menü entdecken"
                >↓</a
            ><span>GEMACHT FÜR UNSERE CREW.</span>
        </div>
    </section>
</template>
