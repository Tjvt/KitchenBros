/** The r77 subset used by FlavorReactor; the installed package ships no types. */
declare module 'three.js' {
    export class Color {
        constructor(color: string);
    }
    export class BufferAttribute {
        constructor(
            array: Float32Array | Uint16Array | Uint32Array,
            itemSize: number,
            normalized?: boolean,
        );
    }
    export class BufferGeometry extends Geometry {
        addAttribute(name: string, attribute: BufferAttribute): this;
        setIndex(attribute: BufferAttribute): this;
    }
    export class Texture {
        constructor(image?: HTMLImageElement | ImageBitmap);
        flipY: boolean;
        needsUpdate: boolean;
        dispose(): void;
    }
    export class Vector3 {
        constructor(x?: number, y?: number, z?: number);
        x: number;
        y: number;
        z: number;
        set(x: number, y: number, z: number): this;
    }
    export class Object3D {
        position: Vector3;
        rotation: Vector3;
        scale: Vector3;
        visible: boolean;
        add(object: Object3D): void;
    }
    export class Group extends Object3D {}
    export class Scene extends Object3D {}
    export class PerspectiveCamera extends Object3D {
        constructor(fov: number, aspect: number, near: number, far: number);
        aspect: number;
        lookAt(position: Vector3): void;
        updateProjectionMatrix(): void;
    }
    export class Geometry {
        vertices: Vector3[];
        computeVertexNormals(): void;
        dispose(): void;
    }
    export class TorusGeometry extends Geometry {
        constructor(
            radius: number,
            tube: number,
            radialSegments: number,
            tubularSegments: number,
            arc?: number,
        );
    }
    export class SphereGeometry extends Geometry {
        constructor(
            radius: number,
            widthSegments: number,
            heightSegments: number,
        );
    }
    export class BoxGeometry extends Geometry {
        constructor(width: number, height: number, depth: number);
    }
    export class PlaneGeometry extends Geometry {
        constructor(width: number, height: number);
    }
    export class Material {
        dispose(): void;
    }
    export class ShaderMaterial extends Material {
        constructor(parameters: {
            uniforms?: Record<string, { value: number | Color }>;
            vertexShader: string;
            fragmentShader: string;
            transparent?: boolean;
            blending?: number;
            depthWrite?: boolean;
            side?: number;
        });
    }
    export class MeshBasicMaterial extends Material {
        constructor(parameters: {
            color: number;
            map?: Texture;
            side?: number;
            transparent?: boolean;
            opacity?: number;
            wireframe?: boolean;
            blending?: number;
            depthWrite?: boolean;
        });
    }
    export class Mesh extends Object3D {
        constructor(geometry: Geometry, material: Material);
    }
    export class WebGLRenderer {
        constructor(parameters: {
            canvas: HTMLCanvasElement;
            antialias: boolean;
            alpha: boolean;
            powerPreference?: string;
        });
        setPixelRatio(ratio: number): void;
        setSize(width: number, height: number, updateStyle?: boolean): void;
        setClearColor(color: number, alpha: number): void;
        render(scene: Scene, camera: PerspectiveCamera): void;
        dispose(): void;
        forceContextLoss(): void;
    }
    export const AdditiveBlending: number;
    export const DoubleSide: number;
}
