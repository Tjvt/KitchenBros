# Kitchen Bros Vorschau

Statische Designvorschau für GitHub Pages. Die Vue Komponenten der Landingpage werden wiederverwendet. Das Demoformular speichert und überträgt keine Namen. Laravel, Login und Adminbereich sind nicht enthalten.

## Build

```sh
npm ci
npx vite build --config preview/vite.config.ts
mkdir -p dist-preview/3D
cp public/3D/steak-plate.glb dist-preview/3D/
touch dist-preview/.nojekyll
```

Inhalt von `dist-preview` auf den Branch `gh-pages` veröffentlichen. Unter Settings → Pages als Quelle `Deploy from a branch`, Branch `gh-pages`, Ordner `/ (root)` auswählen.

Die Vorschau wird unter `https://tjvt.github.io/KitchenBros/` erreichbar, sobald GitHub Pages aktiviert ist und der Build abgeschlossen wurde. Für Pages aus privaten Repositories ist ein passender GitHub Tarif erforderlich.

Bei Änderungen am ursprünglichen Anmeldeformular die separate, deaktivierte Demovariante in `preview/Anmeldung.vue` entsprechend aktualisieren.
