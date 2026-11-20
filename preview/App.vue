<script setup lang="ts">
import Kopfzeile from '../resources/js/components/landing/Kopfzeile.vue';
import Auftakt from '../resources/js/components/landing/Auftakt.vue';
import Laufband from '../resources/js/components/landing/Laufband.vue';
import Speisekarte from '../resources/js/components/landing/Speisekarte.vue';
import Ablauf from '../resources/js/components/landing/Ablauf.vue';
import Kuechenbrueder from '../resources/js/components/landing/Kuechenbrueder.vue';
import Fusszeile from '../resources/js/components/landing/Fusszeile.vue';
import Anmeldung from './Anmeldung.vue';
</script>
<template>
    <div class="kitchen-landing overflow-clip bg-forge-void text-forge-paper antialiased">
        <aside class="border-b border-forge-line bg-forge-panel px-6 py-3 text-center text-xs text-forge-muted">
            Designvorschau · Anmeldungen werden hier nicht gespeichert.
        </aside>
        <Kopfzeile />
        <Auftakt />
        <Laufband />
        <Speisekarte />
        <Ablauf />
        <Kuechenbrueder />
        <Anmeldung />
        <Fusszeile />
    </div>
</template>
