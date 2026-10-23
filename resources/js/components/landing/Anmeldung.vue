<script setup lang="ts">
import DisplayHeading from './DisplayHeading.vue';
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import type { OrbState } from 'thinking-orbs-vue3';
import { store } from '@/routes/lunch';
import SectionLabel from './SectionLabel.vue';
import OrbSignal from './OrbSignal.vue';
const form = useForm({ name: '' });
const joined = ref(usePage().props.lunchRegistered === true);
const focused = ref(false);
const connectionError = ref('');
const state = computed<OrbState>(() => {
    if (joined.value) return 'shaping';
    if (form.processing) return 'working';
    if (form.errors.name || connectionError.value) return 'solving';
    if (focused.value) return 'listening';
    if (form.name.trim()) return 'composing';
    return 'searching';
});
const status = computed(
    () =>
        ({
            shaping: 'DU BIST TEIL DER CREW.',
            working: 'DEIN PLATZ WIRD EINGETRAGEN.',
            solving: 'EIN KLEINER MOMENT NOCH.',
            listening: 'WIR SIND GANZ OHR.',
            composing: 'BEREIT, WENN DU ES BIST.',
            searching: 'DIE CREW WARTET AUF DICH.',
        })[state.value],
);
function submit() {
    if (form.processing) return;
    connectionError.value = '';
    form.post(store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            joined.value = true;
            form.reset();
        },
        onNetworkError: () => {
            connectionError.value =
                'Die Verbindung wurde unterbrochen. Bitte versuche es noch einmal.';
            return false;
        },
        onHttpException: (response) => {
            connectionError.value =
                response.status === 429
                    ? 'Kurz durchatmen. Bitte versuche es in einer Minute noch einmal.'
                    : 'Das hat gerade nicht geklappt. Bitte versuche es noch einmal.';
            return false;
        },
    });
}
</script>
<template>
    <section
        id="mitessen"
        class="relative mx-auto grid max-w-400 grid-cols-1 items-center gap-12 border-t border-forge-line bg-forge-panel px-6 py-18 md:grid-cols-[1.2fr_1fr] md:px-[7%] md:py-26"
    >
        <div
            class="forge-grid pointer-events-none absolute inset-0 opacity-30"
            aria-hidden="true"
        ></div>
        <div
            class="relative z-10 [&_h2]:text-[clamp(2.6rem,5.2vw,5.4rem)] [&_h2>span]:text-forge-ember [&>p:not(:first-child)]:mt-7 [&>p:not(:first-child)]:text-sm [&>p:not(:first-child)]:leading-relaxed [&>p:not(:first-child)]:text-forge-muted"
        >
            <SectionLabel>03 / JOIN THE TABLE</SectionLabel
            ><DisplayHeading
                >DEIN HUNGER.<br />UNSERE MISSION.<br /><span
                    >LET’S EAT.</span
                ></DisplayHeading
            >
            <p>
                Die nächste Runde geht auf Vorfreude.<br />Trag dich ein. Wir
                sehen uns am Tisch.
            </p>
        </div>
        <div
            class="relative z-10 border border-forge-line bg-forge-void px-6 pt-5 pb-6 md:px-9"
        >
            <div class="relative mx-auto flex w-52 items-center justify-center">
                <OrbSignal
                    :state="state"
                    :size="160"
                    :speed="form.processing ? 1.5 : 0.65"
                    :paused="!focused && !form.processing && !joined"
                    :tone="joined ? 'success' : 'ember'"
                /><span
                    class="absolute top-1/2 left-0 -translate-y-1/2 font-mono text-5xl font-extralight text-forge-line"
                    >[</span
                ><span
                    class="absolute top-1/2 right-0 -translate-y-1/2 font-mono text-5xl font-extralight text-forge-line"
                    >]</span
                >
            </div>
            <p
                class="text-center font-mono text-[9px] tracking-widest text-forge-copper"
                role="status"
            >
                {{ status }}
            </p>
            <div v-if="joined" class="py-8 text-center" role="status">
                <h3
                    class="text-3xl leading-none font-black tracking-tight text-forge-success"
                >
                    DU STEHST<br />AUF DER LISTE.
                </h3>
                <p class="mt-5 text-xs leading-relaxed text-forge-muted">
                    Termin und Menü erfährst du von uns im Büro.<br />Bring
                    deinen Hunger mit.
                </p>
                <a
                    href="#menue"
                    class="mt-5 inline-flex items-center gap-3 text-[11px] text-forge-paper transition-colors hover:text-forge-ember"
                    >Zurück zur Vorfreude ↗︎</a
                >
            </div>
            <form
                v-else
                class="mt-9 flex flex-col gap-4 [&_button>span]:text-xl"
                @submit.prevent="submit"
            >
                <label
                    class="font-mono text-[9px] tracking-widest text-forge-muted"
                    for="guest-name"
                    >DEIN NAME. DEIN PLATZ.</label
                ><input
                    class="min-w-0 border border-forge-line bg-forge-panel px-4 py-4 text-sm text-forge-paper placeholder:text-forge-muted"
                    id="guest-name"
                    v-model="form.name"
                    name="name"
                    autocomplete="name"
                    placeholder="Wie heißt du?"
                    required
                    maxlength="80"
                    :disabled="form.processing"
                    :aria-invalid="!!form.errors.name"
                    :aria-describedby="
                        form.errors.name || connectionError
                            ? 'name-error'
                            : 'signup-note'
                    "
                    @focus="focused = true"
                    @blur="focused = false"
                    @input="form.clearErrors('name')"
                />
                <p
                    v-if="form.errors.name || connectionError"
                    id="name-error"
                    class="text-xs leading-relaxed text-forge-error"
                    role="alert"
                >
                    {{ form.errors.name || connectionError }}
                </p>
                <button
                    class="flex min-h-14 cursor-pointer items-center justify-between gap-3 bg-forge-ember px-5 py-4 text-xs font-bold tracking-wide transition-colors hover:bg-forge-copper disabled:cursor-wait disabled:opacity-60"
                    type="submit"
                    :disabled="form.processing"
                >
                    {{
                        form.processing ? 'Wird eingetragen …' : 'ICH BIN DABEI'
                    }}
                    <span aria-hidden="true">↗︎</span></button
                ><small
                    class="text-center text-[9px] text-forge-muted"
                    id="signup-note"
                    >Nur dein Name. Kein Account. Einfach gutes Essen.</small
                >
            </form>
            <span
                class="mt-8 block border-t border-forge-line pt-4 text-center font-mono text-[8px] tracking-[.17em] text-forge-muted"
                >KB / GUEST ACCESS — 001</span
            >
        </div>
        <div
            class="relative z-10 flex items-center justify-between gap-3 border-t border-forge-line pt-7 font-mono text-[8px] tracking-wider text-forge-muted md:col-span-2 [&>span:last-child]:hidden sm:[&>span:last-child]:block [&>span:nth-child(2)]:text-3xl [&>span:nth-child(2)]:text-forge-ember"
        >
            <span>GOOD FOOD CONNECTS GOOD PEOPLE.</span><span>✳︎</span
            ><span>SEE YOU AT THE TABLE.</span>
        </div>
    </section>
</template>
