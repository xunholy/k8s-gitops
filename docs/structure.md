# Repo Structure

```bash
.
└── k8s/
    ├── clusters/
    │   ├── production/                         # One folder per cluster.
    │   │   ├── flux-system/                    # Folder containing flux-system manifests.
    │   │   │   ├── ...                         # Flux component resource manifests.
    │   │   │   └── kustomization.yaml          # Generated kustomization per cluster bootstrap.
    │   │   └── kustomization.yaml              # Kustomization per cluster referring all manifests in core and namespace directory.
    │   └── staging/
    │       ├── flux-system/
    │       │   ├── ...
    │       │   └── kustomization.yaml
    │       └── kustomization.yaml
    ├── core/
    │   ├── base/
    │   │   └── .../                            # One folder per resource type and/or app with its core dependency with prune disabled.
    │   │       └── application/                # One folder per application with core manifests.
    │   │           └── kustomization.yaml      # Kustomization per core application.
    │   └── overlays/
    │       ├── production/
    │       │   ├── kustomization.yaml          # Kustomization per cluster referencing each core app required.
    │       │   └── patch.yaml                  # Optional patch for each environment.
    │       └── staging/
    │           ├── kustomization.yaml
    │           └── patch.yaml
    └── namespaces/
        ├── base/
        │   └── namespace/                      # One folder per namespace containing base resources.
        │       ├── namespace.yaml              # Namespace manifest.
        │       ├── kustomization.yaml          # Kustomization per namespace referring all manifests in this current directory.
        │       └── application/                # Folder per app containing manifests and patches for each application.
        │           └── kustomizaiton.yaml      # Kustomization per app referring all manifests in this directory.
        └── overlays/
            ├── production/
            │   ├── kustomization.yaml          # Kustomization per cluster referencing each namespace and app required.
            │   └── patch.yaml                  # Optional patch for each environment.
            └── staging/
                ├── kustomization.yaml
                └── patch.yaml
```
