<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch } from 'vue';
import type { OrbState } from 'thinking-orbs-vue3';
import type { Geometry, Material, Texture, WebGLRenderer } from 'three.js';

const props = defineProps<{
    state: OrbState;
    intensity: number;
    paused: boolean;
}>();
const host = ref<HTMLDivElement | null>(null);
const canvas = ref<HTMLCanvasElement | null>(null);
const ready = ref(false);
const unavailable = ref(false);
const modelLoaded = ref(false);
let disposeScene: (() => void) | undefined;
let refreshScene: (() => void) | undefined;
let destroyed = false;
let modelLoadController: AbortController | undefined;

watch(
    () => [props.state, props.intensity, props.paused],
    () => refreshScene?.(),
);

onMounted(async () => {
    let renderer: WebGLRenderer | undefined;
    const geometries: Geometry[] = [];
    const materials: Material[] = [];
    const textures: Texture[] = [];
    const modelBitmaps: ImageBitmap[] = [];
    let frame = 0;
    let resizeObserver: ResizeObserver | undefined;
    let intersectionObserver: IntersectionObserver | undefined;
    let removeListeners: (() => void) | undefined;
    disposeScene = () => {
        cancelAnimationFrame(frame);
        resizeObserver?.disconnect();
        intersectionObserver?.disconnect();
        removeListeners?.();
        modelLoadController?.abort();
        textures.forEach((texture) => texture.dispose());
        modelBitmaps.forEach((bitmap) => bitmap.close());
        geometries.forEach((geometry) => geometry.dispose());
        materials.forEach((material) => material.dispose());
        renderer?.dispose();
        renderer?.forceContextLoss();
    };

    try {
        const THREE = await import('three.js');
        if (destroyed || !canvas.value || !host.value) return;
        const element = host.value;
        const canvasElement = canvas.value;
        renderer = new THREE.WebGLRenderer({
            canvas: canvasElement,
            antialias: true,
            alpha: true,
        });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
        renderer.setClearColor(0x090a09, 0);
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(36, 1, 0.1, 100);
        camera.position.set(0, 5.1, 10.5);
        camera.lookAt(new THREE.Vector3(0, 0, 0));
        const reactor = new THREE.Group();
        // The plate model's top faces +Z. Tilt the shared scene toward a tabletop view.
        reactor.rotation.x = -1.2;
        scene.add(reactor);

        const theme = getComputedStyle(document.documentElement);
        const ember = theme.getPropertyValue('--color-forge-ember').trim();
        const copper = theme.getPropertyValue('--color-forge-copper').trim();
        const uniforms = {
            uEmber: { value: new THREE.Color(ember) },
            uCopper: { value: new THREE.Color(copper) },
            uAccent: { value: new THREE.Color(ember) },
            uTime: { value: 0 },
            uIntensity: { value: 0.65 },
            uMode: { value: 0 },
        };
        const vertexShader = `
            varying vec3 vNormal;
            varying vec3 vPosition;
            varying vec2 vUv;
            void main() {
                vUv = uv;
                vNormal = normalize(normalMatrix * normal);
                vec4 viewPosition = modelViewMatrix * vec4(position, 1.0);
                vPosition = viewPosition.xyz;
                gl_Position = projectionMatrix * viewPosition;
            }
        `;
        const moltenMaterial = new THREE.ShaderMaterial({
            uniforms,
            vertexShader,
            fragmentShader: `
                uniform float uTime;
                uniform float uIntensity;
                uniform float uMode;
                uniform vec3 uEmber;
                uniform vec3 uCopper;
                uniform vec3 uAccent;
                varying vec3 vNormal;
                varying vec3 vPosition;
                varying vec2 vUv;
                void main() {
                    vec3 n = normalize(vNormal);
                    vec3 view = normalize(-vPosition);
                    float fresnel = pow(1.0 - abs(dot(n, view)), 2.6);
                    float flow = sin(vUv.x * (65.0 + uMode * 7.0) + sin(vUv.y * (14.0 + uMode) + uTime) * 3.0 - uTime * 2.0);
                    float bands = smoothstep(0.25 - uIntensity * 0.12, 1.0, flow);
                    float shine = pow(max(dot(reflect(-normalize(vec3(-1.0, 2.0, 3.0)), n), view), 0.0), 28.0);
                    vec3 hot = mix(mix(uEmber, uCopper, 0.42), uAccent, 0.58);
                    vec3 color = hot * (0.2 + bands * 0.6 + fresnel * 1.4) + vec3(1.0, 0.77, 0.42) * shine;
                    float luminance = dot(color, vec3(0.299, 0.587, 0.114));
                    color = mix(vec3(luminance), color, 1.0 + uIntensity * 0.55);
                    color *= 0.72 + uIntensity * 0.58;
                    gl_FragColor = vec4(color, 1.0);
                }
            `,
        });
        const chromeMaterial = new THREE.ShaderMaterial({
            uniforms,
            vertexShader,
            fragmentShader: `
                varying vec3 vNormal;
                varying vec3 vPosition;
                void main() {
                    vec3 n = normalize(vNormal);
                    vec3 r = reflect(normalize(vPosition), n);
                    float stripe = pow(abs(sin(r.y * 4.0 + r.x * 2.0)), 8.0);
                    float rim = pow(1.0 - abs(dot(n, normalize(-vPosition))), 3.0);
                    vec3 metal = mix(vec3(0.07, 0.09, 0.085), vec3(0.86, 0.91, 0.85), stripe);
                    metal += vec3(1.0, 0.27, 0.06) * pow(max(r.x, 0.0), 4.0) * 0.8;
                    gl_FragColor = vec4(metal + rim * 0.35, 1.0);
                }
            `,
        });
        const sparkMaterial = new THREE.MeshBasicMaterial({
            color: Number.parseInt(copper.slice(1), 16),
        });
        materials.push(
            moltenMaterial,
            chromeMaterial,
            sparkMaterial,
        );

        function mesh(
            geometry: Geometry,
            material: Material,
            parent = reactor,
        ) {
            geometries.push(geometry);
            const object = new THREE.Mesh(geometry, material);
            parent.add(object);
            return object;
        }
        const mainRing = mesh(
            new THREE.TorusGeometry(2.05, 0.025, 8, 120),
            moltenMaterial,
        );
        mainRing.position.z = -0.04;
        mainRing.rotation.set(0.28, -0.3, 0);
        const outerRing = mesh(
            new THREE.TorusGeometry(2.22, 0.035, 8, 140),
            chromeMaterial,
        );
        outerRing.position.z = -0.08;
        outerRing.rotation.set(0.45, -0.38, 0.2);
        const orbitRing = mesh(
            new THREE.TorusGeometry(2.55, 0.012, 5, 120, Math.PI * 1.7),
            sparkMaterial,
        );
        orbitRing.position.z = -0.12;
        orbitRing.rotation.set(0.7, 0.3, -0.6);
        const shadowMaterial = new THREE.ShaderMaterial({
            transparent: true,
            depthWrite: false,
            vertexShader,
            fragmentShader: `
                varying vec2 vUv;
                void main() {
                    float distanceFromCenter = length((vUv - 0.5) * 2.0);
                    float alpha = (1.0 - smoothstep(0.2, 1.0, distanceFromCenter)) * 0.38;
                    gl_FragColor = vec4(vec3(0.0), alpha);
                }
            `,
        });
        materials.push(shadowMaterial);
        const groundShadow = mesh(
            new THREE.PlaneGeometry(4.2, 3.2),
            shadowMaterial,
        );
        groundShadow.position.z = -0.02;

        modelLoadController = new AbortController();
        const steakPlate = new THREE.Group();
        steakPlate.position.z = 0.2;
        reactor.add(steakPlate);
        void loadSteakPlate(THREE, steakPlate, modelLoadController.signal);
        const sparkGeometry = new THREE.SphereGeometry(0.018, 5, 4);
        geometries.push(sparkGeometry);
        const sparks = Array.from({ length: 52 }, (_, index) => {
            const spark = new THREE.Mesh(sparkGeometry, sparkMaterial);
            const angle = index * 2.39996;
            const radius = 2.5 + (index % 7) * 0.11;
            spark.position.set(
                Math.cos(angle) * radius,
                Math.sin(angle) * radius,
                Math.sin(index * 4.7) * 0.8,
            );
            reactor.add(spark);
            return spark;
        });

        const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
        let visible = true;
        let contextLost = false;
        let pointerX = 0;
        let pointerY = 0;
        let time = 0;
        let previous = 0;
        const modes: OrbState[] = [
            'working',
            'searching',
            'solving',
            'listening',
            'composing',
            'shaping',
        ];
        const modeProfiles = {
            working: {
                accent: new THREE.Color(ember),
                ringSpeed: 1.55,
                orbitSpeed: -0.85,
                pulse: 0.035,
                pulseSpeed: 1.8,
                scanSpeed: 0.5,
                sway: 0,
                sparks: 'rise',
            },
            searching: {
                accent: new THREE.Color(copper),
                ringSpeed: -0.55,
                orbitSpeed: 1.8,
                pulse: 0.11,
                pulseSpeed: 0.9,
                scanSpeed: 1.7,
                sway: 0.035,
                sparks: 'scan',
            },
            solving: {
                accent: new THREE.Color(
                    theme.getPropertyValue('--color-forge-paper').trim(),
                ),
                ringSpeed: 0.16,
                orbitSpeed: -0.38,
                pulse: 0.018,
                pulseSpeed: 0.6,
                scanSpeed: 0.25,
                sway: 0,
                sparks: 'lock',
            },
            listening: {
                accent: new THREE.Color(
                    theme.getPropertyValue('--color-forge-success').trim(),
                ),
                ringSpeed: 0.34,
                orbitSpeed: 0.28,
                pulse: 0.14,
                pulseSpeed: 0.82,
                scanSpeed: 0.72,
                sway: 0.018,
                sparks: 'breathe',
            },
            composing: {
                accent: new THREE.Color(copper),
                ringSpeed: 1.12,
                orbitSpeed: -1.42,
                pulse: 0.075,
                pulseSpeed: 1.35,
                scanSpeed: 1.1,
                sway: 0.022,
                sparks: 'spiral',
            },
            shaping: {
                accent: new THREE.Color(
                    theme.getPropertyValue('--color-forge-error').trim(),
                ),
                ringSpeed: -1.24,
                orbitSpeed: 0.72,
                pulse: 0.055,
                pulseSpeed: 1.1,
                scanSpeed: 1.35,
                sway: 0.012,
                sparks: 'focus',
            },
        } satisfies Record<
            OrbState,
            {
                accent: InstanceType<typeof THREE.Color>;
                ringSpeed: number;
                orbitSpeed: number;
                pulse: number;
                pulseSpeed: number;
                scanSpeed: number;
                sway: number;
                sparks: string;
            }
        >;
        const modeElements: Record<OrbState, InstanceType<typeof THREE.Group>> = {
            working: new THREE.Group(),
            searching: new THREE.Group(),
            solving: new THREE.Group(),
            listening: new THREE.Group(),
            composing: new THREE.Group(),
            shaping: new THREE.Group(),
        };
        for (const [state, group] of Object.entries(modeElements)) {
            group.visible = state === 'working';
            reactor.add(group);
        }
        function accentMaterial(color: string, opacity = 0.72): Material {
            const material = new THREE.MeshBasicMaterial({
                color: Number.parseInt(color.slice(1), 16),
                transparent: true,
                opacity,
                depthWrite: false,
            });
            materials.push(material);
            return material;
        }
        function modeMesh(
            state: OrbState,
            geometry: Geometry,
            material: Material,
            x = 0,
            y = 0,
            z = -0.01,
            angle = 0,
        ) {
            const object = mesh(geometry, material, modeElements[state]);
            object.position.set(x, y, z);
            object.rotation.z = angle;
            return object;
        }
        const modeMaterials = {
            working: accentMaterial(ember),
            searching: accentMaterial(copper),
            solving: accentMaterial(
                theme.getPropertyValue('--color-forge-paper').trim(),
                0.58,
            ),
            listening: accentMaterial(
                theme.getPropertyValue('--color-forge-success').trim(),
            ),
            composing: accentMaterial(copper),
            shaping: accentMaterial(
                theme.getPropertyValue('--color-forge-error').trim(),
            ),
        };

        // Anfeuern: short heat rays surround the plate.
        for (let index = 0; index < 12; index++) {
            const angle = (index / 12) * Math.PI * 2;
            modeMesh(
                'working',
                new THREE.BoxGeometry(0.025, index % 3 === 0 ? 0.32 : 0.19, 0.025),
                modeMaterials.working,
                Math.cos(angle) * 2.18,
                Math.sin(angle) * 2.18,
                -0.015,
                angle,
            );
        }

        // Entdecken: a scanning arm sweeps a broken copper orbit.
        modeMesh(
            'searching',
            new THREE.TorusGeometry(2.64, 0.018, 6, 100, Math.PI * 1.35),
            modeMaterials.searching,
        ).rotation.set(0.08, -0.1, 0);
        modeMesh(
            'searching',
            new THREE.SphereGeometry(0.075, 10, 8),
            modeMaterials.searching,
            2.62,
            0,
            0.03,
        );

        // Tüfteln: precision guides and alignment ticks.
        modeMesh(
            'solving',
            new THREE.TorusGeometry(2.08, 0.012, 5, 120),
            modeMaterials.solving,
        );
        for (let index = 0; index < 8; index++) {
            const angle = (index / 8) * Math.PI * 2;
            modeMesh(
                'solving',
                new THREE.BoxGeometry(0.014, index % 2 === 0 ? 0.24 : 0.12, 0.014),
                modeMaterials.solving,
                Math.cos(angle) * 2.28,
                Math.sin(angle) * 2.28,
                0.01,
                angle,
            );
        }

        // Verbinden: soft concentric waves breathe around the dish.
        const listeningWaves = [2.28, 2.58, 2.88].map((radius) =>
            modeMesh(
                'listening',
                new THREE.TorusGeometry(radius, 0.018, 6, 100),
                modeMaterials.listening,
            ),
        );

        // Würzen: crossed ingredient orbits with small orbiting nodes.
        const composingOrbits = [0, 1, 2].map((index) => {
            const orbit = modeMesh(
                'composing',
                new THREE.TorusGeometry(2.52 + index * 0.2, 0.014, 5, 100),
                modeMaterials.composing,
            );
            orbit.rotation.set(index * 0.28, index * 0.42, index * 0.7);
            return orbit;
        });
        for (let index = 0; index < 6; index++) {
            const angle = (index / 6) * Math.PI * 2;
            modeMesh(
                'composing',
                new THREE.SphereGeometry(0.045, 8, 6),
                modeMaterials.composing,
                Math.cos(angle) * 3.02,
                Math.sin(angle) * 3.02,
                0.02,
            );
        }

        // Servieren: four clean corner marks frame the finished plate.
        for (const x of [-1, 1]) {
            for (const y of [-1, 1]) {
                modeMesh(
                    'shaping',
                    new THREE.BoxGeometry(0.34, 0.025, 0.025),
                    modeMaterials.shaping,
                    x * 2.12,
                    y * 2.12,
                    0.02,
                    x * y > 0 ? 0 : Math.PI / 2,
                );
                modeMesh(
                    'shaping',
                    new THREE.BoxGeometry(0.025, 0.34, 0.025),
                    modeMaterials.shaping,
                    x * 2.12,
                    y * 2.12,
                    0.02,
                    x * y > 0 ? 0 : Math.PI / 2,
                );
            }
        }
        function render(now: number) {
            frame = 0;
            const animate =
                !props.paused &&
                !motion.matches &&
                visible &&
                !document.hidden &&
                !contextLost;
            const elapsed = Math.min((now - previous) / 1000, 0.05);
            const animationSpeed = 0.35 + (props.intensity / 100) * 2.65;
            if (animate) time += elapsed * animationSpeed;
            previous = now;
            uniforms.uTime.value = time;
            uniforms.uIntensity.value = props.intensity / 100;
            uniforms.uMode.value = modes.indexOf(props.state);
            const profile = modeProfiles[props.state];
            for (const state of modes) {
                modeElements[state].visible = state === props.state;
            }
            uniforms.uAccent.value = profile.accent;
            const mode = uniforms.uMode.value;
            reactor.rotation.y +=
                ((motion.matches ? 0 : pointerX * 0.24) +
                    Math.sin(time * 0.45) * profile.sway -
                    reactor.rotation.y) *
                0.05;
            reactor.rotation.x +=
                ((-1.2 + (motion.matches ? 0 : pointerY * 0.045)) -
                    reactor.rotation.x) *
                0.05;
            const pulse = 1 + Math.sin(time * profile.pulseSpeed) * profile.pulse;
            mainRing.scale.set(pulse, pulse, 1);
            mainRing.rotation.z = time * profile.ringSpeed;
            outerRing.rotation.y =
                -0.38 + Math.sin(time * profile.scanSpeed) * 0.22 + mode * 0.08;
            orbitRing.rotation.z = time * profile.orbitSpeed;
            modeElements.searching.rotation.z = -time * 0.48;
            listeningWaves.forEach((wave, index) => {
                const waveScale =
                    1 + Math.sin(time * profile.pulseSpeed - index * 0.8) * 0.06;
                wave.scale.set(waveScale, waveScale, 1);
            });
            composingOrbits.forEach((orbit, index) => {
                orbit.rotation.z = time * (0.16 + index * 0.08);
            });
            steakPlate.rotation.z = Math.sin(time * 0.18) * 0.035;
            sparks.forEach((spark, index) => {
                const angle = index * 2.39996;
                const radius = 2.5 + (index % 7) * 0.11;
                const phase = time * profile.scanSpeed + index;
                const sparkSize =
                    profile.sparks === 'breathe'
                        ? 0.7 + Math.sin(time * profile.pulseSpeed) * 0.25 + 0.7
                        : profile.sparks === 'lock'
                          ? 0.82 + (index % 3) * 0.08
                          : 0.75 + (Math.sin(phase) + 1) * 0.35;
                spark.scale.set(sparkSize, sparkSize, sparkSize);

                if (profile.sparks === 'scan') {
                    spark.position.x = Math.cos(angle + time * 0.75) * radius;
                    spark.position.y = Math.sin(angle + time * 0.75) * radius;
                    spark.position.z = Math.sin(phase * 1.8) * 0.22;
                } else if (profile.sparks === 'spiral') {
                    const spiralAngle = angle + time * 0.42;
                    const spiralRadius = radius + Math.sin(phase) * 0.12;
                    spark.position.x = Math.cos(spiralAngle) * spiralRadius;
                    spark.position.y = Math.sin(spiralAngle) * spiralRadius;
                    spark.position.z = Math.sin(phase) * 0.24;
                } else if (profile.sparks === 'focus') {
                    const focusedRadius = radius * (0.88 + Math.sin(time) * 0.12);
                    spark.position.x = Math.cos(angle) * focusedRadius;
                    spark.position.y = Math.sin(angle) * focusedRadius;
                    spark.position.z = Math.sin(phase * 0.25) * 0.1;
                } else if (profile.sparks === 'rise') {
                    spark.position.z = Math.sin(phase * 1.8) * 0.8;
                } else if (profile.sparks === 'breathe') {
                    spark.position.z = Math.sin(time * profile.pulseSpeed) * 0.12;
                } else {
                    spark.position.z = index % 2 === 0 ? 0.06 : -0.06;
                }
            });
            if (!contextLost) renderer?.render(scene, camera);
            if (animate) frame = requestAnimationFrame(render);
        }
        function refresh() {
            cancelAnimationFrame(frame);
            render(performance.now());
        }
        refreshScene = refresh;

        async function loadSteakPlate(
            three: typeof THREE,
            parent: InstanceType<typeof THREE.Group>,
            signal: AbortSignal,
        ): Promise<void> {
            const pendingResources: Geometry[] = [];
            type Accessor = {
                bufferView: number;
                byteOffset?: number;
                componentType: number;
                count: number;
                type: string;
            };
            type Primitive = {
                attributes: Record<string, number>;
                indices: number;
            };
            type GlbDocument = {
                accessors: Accessor[];
                bufferViews: {
                    byteLength?: number;
                    byteOffset?: number;
                    byteStride?: number;
                }[];
                images: { bufferView: number; mimeType: string }[];
                meshes: { primitives: Primitive[] }[];
                nodes: { children?: number[]; mesh?: number }[];
                scenes: { nodes: number[] }[];
                scene: number;
            };
            type PendingPrimitive = {
                geometry: InstanceType<typeof THREE.BufferGeometry>;
                positions: Float32Array;
                indices: Uint16Array | Uint32Array;
                uvs: Float32Array;
            };

            try {
                const response = await fetch(
                    '/3D/steak-plate.glb',
                    { signal },
                );
                if (!response.ok)
                    throw new Error('The steak model could not be loaded.');
                const glb = await response.arrayBuffer();
                const view = new DataView(glb);
                if (view.getUint32(0, true) !== 0x46546c67) {
                    throw new Error('The steak model is not a valid GLB file.');
                }

                let documentStart = 12;
                let jsonStart = 0;
                let documentLength = 0;
                let binaryStart = 0;
                let binaryLength = 0;
                while (documentStart + 8 <= glb.byteLength) {
                    const chunkLength = view.getUint32(documentStart, true);
                    const chunkType = view.getUint32(documentStart + 4, true);
                    if (chunkType === 0x4e4f534a) {
                        jsonStart = documentStart + 8;
                        documentLength = chunkLength;
                    } else if (chunkType === 0x004e4942) {
                        binaryStart = documentStart + 8;
                        binaryLength = chunkLength;
                        break;
                    }
                    documentStart += chunkLength + 8;
                }
                if (!documentLength || !binaryStart || !binaryLength) {
                    throw new Error('The GLB file is missing model data.');
                }
                const metadata = JSON.parse(
                    new TextDecoder().decode(
                        glb.slice(jsonStart, jsonStart + documentLength),
                    ),
                ) as GlbDocument;
                const binary = new DataView(glb, binaryStart, binaryLength);

                function readAccessor(index: number): Float32Array {
                    const accessor = metadata.accessors[index];
                    const bufferView =
                        metadata.bufferViews[accessor.bufferView];
                    const itemSize = accessor.type === 'VEC3' ? 3 : 2;
                    const byteStride = bufferView.byteStride ?? itemSize * 4;
                    const values = new Float32Array(accessor.count * itemSize);
                    const start =
                        (bufferView.byteOffset ?? 0) +
                        (accessor.byteOffset ?? 0);
                    for (let vertex = 0; vertex < accessor.count; vertex++) {
                        for (
                            let component = 0;
                            component < itemSize;
                            component++
                        ) {
                            values[vertex * itemSize + component] =
                                binary.getFloat32(
                                    start + vertex * byteStride + component * 4,
                                    true,
                                );
                        }
                    }
                    return values;
                }

                function readIndices(index: number): Uint16Array | Uint32Array {
                    const accessor = metadata.accessors[index];
                    const bufferView =
                        metadata.bufferViews[accessor.bufferView];
                    const byteWidth = accessor.componentType === 5125 ? 4 : 2;
                    const values =
                        byteWidth === 4
                            ? new Uint32Array(accessor.count)
                            : new Uint16Array(accessor.count);
                    const start =
                        (bufferView.byteOffset ?? 0) +
                        (accessor.byteOffset ?? 0);
                    for (
                        let position = 0;
                        position < accessor.count;
                        position++
                    ) {
                        values[position] =
                            byteWidth === 4
                                ? binary.getUint32(
                                      start + position * byteWidth,
                                      true,
                                  )
                                : binary.getUint16(
                                      start + position * byteWidth,
                                      true,
                                  );
                    }
                    return values;
                }

                const pending: PendingPrimitive[] = [];
                const minimum = [Infinity, Infinity, Infinity];
                const maximum = [-Infinity, -Infinity, -Infinity];
                const visited = new Set<number>();
                function collectMeshes(nodeIndex: number): void {
                    if (visited.has(nodeIndex)) return;
                    visited.add(nodeIndex);
                    const node = metadata.nodes[nodeIndex];
                    if (node.mesh !== undefined) {
                        for (const primitive of metadata.meshes[node.mesh]
                            .primitives) {
                            const positions = readAccessor(
                                primitive.attributes.POSITION,
                            );
                            const uvs = readAccessor(
                                primitive.attributes.TEXCOORD_0,
                            );
                            for (
                                let offset = 0;
                                offset < positions.length;
                                offset += 3
                            ) {
                                minimum[0] = Math.min(
                                    minimum[0],
                                    positions[offset],
                                );
                                minimum[1] = Math.min(
                                    minimum[1],
                                    positions[offset + 1],
                                );
                                minimum[2] = Math.min(
                                    minimum[2],
                                    positions[offset + 2],
                                );
                                maximum[0] = Math.max(
                                    maximum[0],
                                    positions[offset],
                                );
                                maximum[1] = Math.max(
                                    maximum[1],
                                    positions[offset + 1],
                                );
                                maximum[2] = Math.max(
                                    maximum[2],
                                    positions[offset + 2],
                                );
                            }
                            pending.push({
                                geometry: new three.BufferGeometry(),
                                positions,
                                uvs,
                                indices: readIndices(primitive.indices),
                            });
                            pendingResources.push(
                                pending[pending.length - 1].geometry,
                            );
                        }
                    }
                    node.children?.forEach(collectMeshes);
                }
                metadata.scenes[metadata.scene].nodes.forEach(collectMeshes);
                if (pending.length === 0) {
                    throw new Error('The GLB file does not contain a mesh.');
                }

                const center = minimum.map(
                    (value, index) => (value + maximum[index]) / 2,
                );
                const scale =
                    3.7 /
                    Math.max(
                        maximum[0] - minimum[0],
                        maximum[1] - minimum[1],
                        maximum[2] - minimum[2],
                    );
                for (const item of pending) {
                    for (
                        let offset = 0;
                        offset < item.positions.length;
                        offset += 3
                    ) {
                        item.positions[offset] =
                            (item.positions[offset] - center[0]) * scale;
                        item.positions[offset + 1] =
                            (item.positions[offset + 1] - center[1]) * scale;
                        item.positions[offset + 2] =
                            (item.positions[offset + 2] - center[2]) * scale;
                    }
                }

                const image = metadata.images[0];
                const imageView = metadata.bufferViews[image.bufferView];
                const imageStart = imageView.byteOffset ?? 0;
                const imageEnd = imageStart + (imageView.byteLength ?? 0);
                const bitmap = await createImageBitmap(
                    new Blob(
                        [
                            glb.slice(
                                binaryStart + imageStart,
                                binaryStart + imageEnd,
                            ),
                        ],
                        {
                            type: image.mimeType,
                        },
                    ),
                );
                if (destroyed || signal.aborted) {
                    bitmap.close();
                    pendingResources.forEach((geometry) => geometry.dispose());
                    return;
                }

                modelBitmaps.push(bitmap);
                const texture = new three.Texture(bitmap);
                texture.flipY = false;
                texture.needsUpdate = true;
                textures.push(texture);
                const material = new three.MeshBasicMaterial({
                    color: 0xffffff,
                    map: texture,
                    side: three.DoubleSide,
                });
                materials.push(material);

                for (const item of pending) {
                    item.geometry.addAttribute(
                        'position',
                        new three.BufferAttribute(item.positions, 3),
                    );
                    item.geometry.addAttribute(
                        'uv',
                        new three.BufferAttribute(item.uvs, 2),
                    );
                    item.geometry.setIndex(
                        new three.BufferAttribute(item.indices, 1),
                    );
                    geometries.push(item.geometry);
                    parent.add(new three.Mesh(item.geometry, material));
                }
                pendingResources.length = 0;
                modelLoaded.value = true;
                refreshScene?.();
            } catch {
                pendingResources.forEach((geometry) => geometry.dispose());
                if (!signal.aborted && !destroyed) unavailable.value = true;
            }
        }
        function resize() {
            const { width, height } = element.getBoundingClientRect();
            camera.aspect = width / Math.max(height, 1);
            camera.updateProjectionMatrix();
            renderer?.setSize(width, height, false);
            refresh();
        }
        function move(event: PointerEvent) {
            if (event.pointerType !== 'mouse') return;
            const bounds = element.getBoundingClientRect();
            pointerX = (event.clientX - bounds.left) / bounds.width - 0.5;
            pointerY = (event.clientY - bounds.top) / bounds.height - 0.5;
        }
        function resetPointer() {
            pointerX = 0;
            pointerY = 0;
        }
        function loseContext(event: Event) {
            event.preventDefault();
            contextLost = true;
            unavailable.value = true;
            ready.value = false;
            cancelAnimationFrame(frame);
        }
        function restoreContext() {
            contextLost = false;
            unavailable.value = false;
            ready.value = true;
            refresh();
        }
        element.addEventListener('pointermove', move);
        element.addEventListener('pointerleave', resetPointer);
        document.addEventListener('visibilitychange', refresh);
        motion.addEventListener('change', refresh);
        canvasElement.addEventListener('webglcontextlost', loseContext);
        canvasElement.addEventListener('webglcontextrestored', restoreContext);
        removeListeners = () => {
            element.removeEventListener('pointermove', move);
            element.removeEventListener('pointerleave', resetPointer);
            document.removeEventListener('visibilitychange', refresh);
            motion.removeEventListener('change', refresh);
            canvasElement.removeEventListener('webglcontextlost', loseContext);
            canvasElement.removeEventListener(
                'webglcontextrestored',
                restoreContext,
            );
        };
        resizeObserver = new ResizeObserver(resize);
        resizeObserver.observe(element);
        intersectionObserver = new IntersectionObserver(([entry]) => {
            visible = entry.isIntersecting;
            refresh();
        });
        intersectionObserver.observe(element);
        resize();
        ready.value = true;
    } catch {
        unavailable.value = true;
        disposeScene?.();
        disposeScene = undefined;
    }
});
onUnmounted(() => {
    destroyed = true;
    disposeScene?.();
});
</script>

<template>
    <div
        ref="host"
        class="reactor-scene relative aspect-square w-full max-md:my-5 md:absolute md:inset-0 md:aspect-auto"
        :class="{ 'is-ready': ready }"
        :data-model-loaded="modelLoaded"
        role="img"
        aria-label="Ein gegrilltes Fleischgericht liegt auf einem Teller in einer interaktiven 3D-Szene"
    >
        <div
            v-if="!ready || !modelLoaded"
            class="reactor-fallback absolute inset-[22%] flex items-center justify-center rounded-full text-8xl text-forge-ember"
            aria-hidden="true"
        >
            <span>✳︎</span>
        </div>
        <canvas
            class="relative z-10 block size-full"
            ref="canvas"
            aria-hidden="true"
        />
        <span
            v-if="unavailable"
            class="absolute bottom-4 left-0 w-full text-center text-xs text-forge-muted"
            >Geschmack kennt keine Grenzen.</span
        >
    </div>
</template>
