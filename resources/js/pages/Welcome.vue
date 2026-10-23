<script setup lang="ts">
import Seitenkopf from '@/components/landing/Seitenkopf.vue';
import Kopfzeile from '@/components/landing/Kopfzeile.vue';
import Auftakt from '@/components/landing/Auftakt.vue';
import Laufband from '@/components/landing/Laufband.vue';
import Speisekarte from '@/components/landing/Speisekarte.vue';
import Ablauf from '@/components/landing/Ablauf.vue';
import Kuechenbrueder from '@/components/landing/Kuechenbrueder.vue';
import Anmeldung from '@/components/landing/Anmeldung.vue';
import Fusszeile from '@/components/landing/Fusszeile.vue';
</script>

<template>
    <div
        class="kitchen-landing overflow-clip bg-forge-void text-forge-paper antialiased"
    >
        <Seitenkopf />
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
