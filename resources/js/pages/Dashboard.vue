<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Clock3, Flame, Search, Users } from '@lucide/vue';
import { home } from '@/routes';

type Registration = {
    id: number;
    name: string;
    created_at: string;
};

const props = defineProps<{
    registrations: Registration[];
    registrationCount: number;
    todayCount: number;
}>();

defineOptions({ layout: [] });

const searchTerm = ref('');

const filteredRegistrations = computed(() => {
    const normalizedSearchTerm = searchTerm.value
        .trim()
        .toLocaleLowerCase('de');

    if (!normalizedSearchTerm) {
        return props.registrations;
    }

    return props.registrations.filter((registration) =>
        registration.name
            .toLocaleLowerCase('de')
            .includes(normalizedSearchTerm),
    );
});

function formatRegistrationDate(date: string): string {
    return new Intl.DateTimeFormat('de-DE', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(date));
}
</script>

<template>
    <Head title="Küchenbuch — Kitchen Bros" />

    <main
        class="min-h-screen bg-forge-void text-forge-paper selection:bg-forge-ember selection:text-forge-paper"
    >
        <header class="border-b border-forge-line/80">
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8 lg:px-12"
            >
                <Link
                    :href="home()"
                    class="group inline-flex items-center gap-3"
                    aria-label="Zurück zur Startseite"
                >
                    <span
                        class="grid size-10 place-items-center border border-forge-ember/70 font-display text-xl text-forge-ember transition-colors group-hover:bg-forge-ember group-hover:text-forge-paper"
                        >K</span
                    >
                    <span
                        class="text-xs font-semibold tracking-[0.22em] text-forge-paper"
                        >KITCHEN<br />BROS</span
                    >
                </Link>

                <div
                    class="flex items-center gap-2 text-[10px] font-semibold tracking-[0.2em] text-forge-muted sm:gap-3"
                >
                    <span class="relative flex size-2">
                        <span
                            class="absolute inline-flex size-full animate-ping rounded-full bg-forge-success opacity-60"
                        ></span>
                        <span
                            class="relative inline-flex size-2 rounded-full bg-forge-success"
                        ></span>
                    </span>
                    KÜCHENBUCH
                    <span class="hidden text-forge-line sm:inline">/</span>
                    ADMIN
                </div>
            </div>
        </header>

        <div
            class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16 lg:px-12 lg:py-20"
        >
            <section
                class="grid gap-10 lg:grid-cols-[1.15fr_.85fr] lg:items-end"
            >
                <div>
                    <p
                        class="mb-5 flex items-center gap-2 font-mono text-[10px] tracking-[0.25em] text-forge-copper"
                    >
                        <Flame class="size-3.5" /> INTERN · SEITENBLICK AUF DEN
                        GRILL
                    </p>
                    <h1
                        class="max-w-3xl font-display text-5xl leading-[.94] tracking-[-0.065em] sm:text-7xl lg:text-8xl"
                    >
                        DER<br />
                        <span class="text-forge-ember">HUNGER.</span><br />
                        DIE LISTE.
                    </h1>
                    <p
                        class="mt-6 max-w-lg text-sm leading-6 text-forge-muted sm:text-base"
                    >
                        Alle, die mit an den Tisch wollen. Ein Blick, ein
                        sauberer Stand, kein Bürokratie-Beil.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <article
                        class="border border-forge-line bg-forge-panel p-5 sm:p-7"
                    >
                        <div
                            class="flex items-center justify-between text-forge-muted"
                        >
                            <span class="font-mono text-[9px] tracking-[0.2em]"
                                >AUF DER LISTE</span
                            >
                            <Users class="size-4 text-forge-copper" />
                        </div>
                        <p
                            class="mt-7 font-display text-5xl tracking-tight text-forge-paper sm:text-6xl"
                        >
                            {{ registrationCount.toString().padStart(2, '0') }}
                        </p>
                        <p class="mt-2 text-xs text-forge-muted">
                            Mitesser insgesamt
                        </p>
                    </article>
                    <article
                        class="border border-forge-line bg-forge-panel p-5 sm:p-7"
                    >
                        <div
                            class="flex items-center justify-between text-forge-muted"
                        >
                            <span class="font-mono text-[9px] tracking-[0.2em]"
                                >FRISCH DABEI</span
                            >
                            <Clock3 class="size-4 text-forge-ember" />
                        </div>
                        <p
                            class="mt-7 font-display text-5xl tracking-tight text-forge-paper sm:text-6xl"
                        >
                            {{ todayCount.toString().padStart(2, '0') }}
                        </p>
                        <p class="mt-2 text-xs text-forge-muted">
                            Anmeldungen heute
                        </p>
                    </article>
                </div>
            </section>

            <section
                class="mt-14 border border-forge-line bg-forge-panel sm:mt-20"
            >
                <div
                    class="flex flex-col gap-5 border-b border-forge-line px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7"
                >
                    <div>
                        <p
                            class="font-mono text-[9px] tracking-[0.22em] text-forge-copper"
                        >
                            DIE CREW
                        </p>
                        <h2
                            class="mt-2 font-display text-2xl tracking-tight sm:text-3xl"
                        >
                            MITESSENDE
                            <span class="text-forge-muted"
                                >/ {{ registrationCount }}</span
                            >
                        </h2>
                    </div>
                    <label
                        class="flex w-full items-center gap-3 border border-forge-line bg-forge-void px-4 py-3 text-forge-muted sm:max-w-xs"
                    >
                        <Search class="size-4 shrink-0" />
                        <span class="sr-only">Namen durchsuchen</span>
                        <input
                            v-model="searchTerm"
                            type="search"
                            placeholder="Namen durchsuchen"
                            class="min-w-0 flex-1 border-0 bg-transparent text-sm text-forge-paper outline-none placeholder:text-forge-muted/70 focus:ring-0"
                        />
                    </label>
                </div>

                <div
                    v-if="filteredRegistrations.length"
                    class="divide-y divide-forge-line/70"
                >
                    <article
                        v-for="(registration, index) in filteredRegistrations"
                        :key="registration.id"
                        class="group flex items-center gap-4 px-5 py-4 transition-colors hover:bg-forge-raised/70 sm:gap-6 sm:px-7 sm:py-5"
                    >
                        <span
                            class="w-8 shrink-0 font-mono text-[10px] text-forge-muted/70"
                            >{{ (index + 1).toString().padStart(2, '0') }}</span
                        >
                        <span
                            class="grid size-10 shrink-0 place-items-center border border-forge-line bg-forge-void font-display text-sm text-forge-copper transition-colors group-hover:border-forge-ember/60 group-hover:text-forge-ember"
                            >{{
                                registration.name
                                    .trim()
                                    .charAt(0)
                                    .toLocaleUpperCase('de')
                            }}</span
                        >
                        <span
                            class="min-w-0 flex-1 truncate font-medium text-forge-paper"
                            >{{ registration.name }}</span
                        >
                        <time
                            class="hidden shrink-0 font-mono text-[10px] tracking-wide text-forge-muted sm:block"
                            >{{
                                formatRegistrationDate(registration.created_at)
                            }}</time
                        >
                        <span
                            class="shrink-0 rounded-full border border-forge-success/20 px-2.5 py-1 font-mono text-[9px] tracking-wider text-forge-success"
                            >DABEI</span
                        >
                    </article>
                </div>

                <div
                    v-else
                    class="flex min-h-64 flex-col items-center justify-center px-6 py-14 text-center"
                >
                    <div
                        class="grid size-14 place-items-center border border-forge-line text-forge-copper"
                    >
                        <Users class="size-5" />
                    </div>
                    <h3 class="mt-5 font-display text-xl">
                        {{
                            searchTerm
                                ? 'NICHT AUF DEM ZETTEL.'
                                : 'DER TISCH IST NOCH LEER.'
                        }}
                    </h3>
                    <p class="mt-2 max-w-sm text-sm leading-6 text-forge-muted">
                        {{
                            searchTerm
                                ? 'Für diesen Namen gibt es keinen Treffer. Versuch’s mit einer anderen Schreibweise.'
                                : 'Sobald sich jemand zum Mitessen einträgt, steht der Name hier.'
                        }}
                    </p>
                </div>

                <footer
                    class="flex flex-col gap-2 border-t border-forge-line px-5 py-4 font-mono text-[9px] tracking-[0.14em] text-forge-muted sm:flex-row sm:items-center sm:justify-between sm:px-7"
                >
                    <span>KITCHEN BROS · INTERNES KÜCHENBUCH</span>
                    <span
                        >{{ filteredRegistrations.length }} VON
                        {{ registrationCount }} ANGEZEIGT</span
                    >
                </footer>
            </section>

            <footer
                class="mt-10 flex flex-wrap items-center justify-between gap-4"
            >
                <Link
                    :href="home()"
                    class="inline-flex items-center gap-2 text-xs font-semibold tracking-wide text-forge-muted transition-colors hover:text-forge-paper"
                >
                    <ArrowLeft class="size-3.5" /> ZUR LANDINGPAGE
                </Link>
                <span
                    class="font-mono text-[9px] tracking-[0.18em] text-forge-muted/60"
                    >ZWEI BRÜDER. EIN GRILL. KEIN QUATSCH.</span
                >
            </footer>
        </div>
    </main>
</template>
