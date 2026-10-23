<script setup lang="ts">
import DisplayHeading from './DisplayHeading.vue';
import SectionLabel from './SectionLabel.vue';
import ActionLink from './ActionLink.vue';
import OrbSignal from './OrbSignal.vue';
</script>
<template>
    <section
        id="menue"
        class="relative mx-auto max-w-400 px-6 py-18 md:px-[7%] md:py-26"
    >
        <div
            class="mb-9 flex flex-col justify-between gap-6 md:mb-12 md:flex-row md:items-end [&>p]:text-sm [&>p]:leading-relaxed [&>p]:text-forge-muted"
        >
            <div>
                <SectionLabel>01 / FUEL FOR YOUR DAY</SectionLabel
                ><DisplayHeading
                    >GROSSE CUTS.<br /><span class="outline-text"
                        >GROSSER HUNGER.</span
                    ></DisplayHeading
                >
            </div>
            <p>
                Frische Zutaten. Echter Einsatz.<br />Die beste Ausrede,
                deinen<br />Laptop endlich zuzuklappen.
            </p>
        </div>
        <div
            class="grid grid-cols-1 border border-forge-line bg-forge-panel md:grid-cols-2"
        >
            <div class="relative min-h-95 overflow-hidden md:min-h-135">
                <img
                    class="absolute size-full object-cover brightness-60 saturate-60"
                    src="https://images.unsplash.com/photo-1600891964092-4316c288032e?auto=format&fit=crop&w=1100&q=85"
                    alt="Saftiges Steak mit kräftigen Röstaromen als Inspiration für unsere nächste Kochrunde"
                    loading="lazy"
                /><span
                    class="absolute top-6 left-6 z-10 font-mono text-[8px] tracking-widest text-forge-paper/70"
                    >KB KITCHEN LAB / SERVING INSPIRATION</span
                >
                <div
                    class="absolute bottom-10 left-7 z-10 font-display text-6xl leading-[.88] font-black tracking-tighter md:text-7xl [&>span]:text-forge-ember"
                >
                    MEAT.<br /><span>HEAT.</span>
                </div>
                <span
                    class="absolute right-7 bottom-9 z-10 text-5xl text-forge-paper"
                    aria-hidden="true"
                    >↗︎</span
                >
            </div>
            <div
                class="flex flex-col items-start p-7 md:p-10 [&>a]:w-full [&>h3]:mb-6 [&>h3]:font-display [&>h3]:text-[clamp(1.9rem,3vw,2.8rem)] [&>h3]:leading-none [&>h3]:font-black [&>h3]:tracking-tighter [&>h3>span]:text-forge-copper [&>p:not([class*=menu-eyebrow])]:text-sm [&>p:not([class*=menu-eyebrow])]:leading-relaxed [&>p:not([class*=menu-eyebrow])]:text-forge-muted [&>small]:mt-4 [&>small]:text-[10px] [&>small]:leading-relaxed [&>small]:text-forge-muted"
            >
                <div
                    class="mb-10 flex w-full items-center justify-between gap-3 font-mono text-[8px] tracking-widest text-forge-muted"
                >
                    <span
                        class="inline-flex items-center gap-2 border border-forge-line px-3 py-2 text-[8px]"
                        ><span
                            class="inline-block size-1.5 shrink-0 rounded-full bg-forge-ember shadow-[0_0_9px_var(--color-forge-ember)]"
                        ></span>
                        MENÜ-VORSCHAU</span
                    ><span>DROP / 001</span>
                </div>
                <p
                    class="menu-eyebrow mb-3 font-mono text-[9px] tracking-widest text-forge-copper"
                >
                    SEAR. SIZZLE. SATISFY.
                </p>
                <h3>VOLLES BRETT.<br /><span>VOLLER GESCHMACK.</span></h3>
                <p>
                    Saftiges Steak. Kräftige Kruste. Rosmarinkartoffeln und
                    hausgemachte Kräuterbutter. So stellen wir uns eine
                    Mittagspause mit Ansage vor.
                </p>
                <div
                    class="mt-6 flex flex-wrap gap-2 font-mono text-[8px] tracking-wide text-forge-muted [&>span]:border [&>span]:border-forge-line [&>span]:px-2 [&>span]:py-1.5"
                >
                    <span>01 / FRISCH</span><span>02 / HANDGEMACHT</span
                    ><span>03 / MIT LIEBE</span>
                </div>
                <div
                    class="my-6 flex w-full items-center gap-3 border-y border-forge-line py-4 [&_div>span]:block [&_div>span]:font-mono [&_div>span]:text-[8px] [&_div>span]:tracking-widest [&_div>span]:text-forge-muted"
                >
                    <OrbSignal state="searching" :size="42" :paused="true" />
                    <div>
                        <span>DIE NÄCHSTE KOCHRUNDE</span
                        ><strong class="mt-1 block text-xs font-medium"
                            >Termin & Menü folgen.</strong
                        >
                    </div>
                </div>
                <ActionLink>Für die nächste Runde eintragen</ActionLink
                ><small
                    >Ein Vorgeschmack auf das, was kommt. Gericht und Termin
                    stimmen wir im Büro ab. Allergien? Sprich uns vor dem Essen
                    an.</small
                >
            </div>
        </div>
    </section>
</template>
