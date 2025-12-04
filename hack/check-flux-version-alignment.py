#!/usr/bin/env python3
"""
Guardrail: ensure bootstrap helmfile versions match Flux ResourceSet OCI tags.

This prevents flux-operator/flux-instance version skew between:
- kubernetes/bootstrap/helmfile.yaml (used during local bootstrap)
- kubernetes/clusters/cluster-00/... ResourceSets (GitOps reconciliation)
"""

from __future__ import annotations

import sys
from pathlib import Path

import yaml


ROOT = Path(__file__).resolve().parent.parent
HELMFILE = ROOT / "kubernetes" / "bootstrap" / "helmfile.yaml"

# Target ResourceSets to check: name -> path
RESOURCESETS = {
    "flux-operator": ROOT
    / "kubernetes"
    / "clusters"
    / "cluster-00"
    / "flux-system"
    / "flux-operator"
    / "app"
    / "helmrelease.yaml",
    "flux-instance": ROOT
    / "kubernetes"
    / "clusters"
    / "cluster-00"
    / "flux-system"
    / "flux-instance"
    / "app"
    / "helmrelease.yaml",
}


def load_yaml(path: Path) -> dict:
    with path.open() as f:
        return yaml.safe_load(f)


def normalize_version(value: str | None) -> str | None:
    if value is None:
        return None
    # Handle tags like v0.35.0 vs 0.35.0
    return value.lstrip("v")


def extract_helmfile_versions() -> dict[str, str]:
    data = load_yaml(HELMFILE)
    versions: dict[str, str] = {}
    for release in data.get("releases", []):
        name = release.get("name")
        if name in RESOURCESETS:
            versions[name] = str(release.get("version", "")).strip()
    return versions


def extract_resourceset_tag(path: Path, name: str) -> str | None:
    data = load_yaml(path)
    resources = data.get("spec", {}).get("resources", [])
    for resource in resources:
        if resource.get("kind") != "OCIRepository":
            continue
        if resource.get("metadata", {}).get("name") != name:
            continue
        return str(resource.get("spec", {}).get("ref", {}).get("tag") or "").strip()
    return None


def main() -> int:
    errors: list[str] = []

    if not HELMFILE.exists():
        print(f"Helmfile not found: {HELMFILE}", file=sys.stderr)
        return 1

    helmfile_versions = extract_helmfile_versions()

    for name, path in RESOURCESETS.items():
        if not path.exists():
            errors.append(f"[missing] ResourceSet file not found for {name}: {path}")
            continue

        hf_version = normalize_version(helmfile_versions.get(name))
        rs_tag = normalize_version(extract_resourceset_tag(path, name))

        if not hf_version:
            errors.append(f"[missing] Helmfile version for {name} not found in {HELMFILE}")
        if not rs_tag:
            errors.append(f"[missing] ResourceSet tag for {name} not found in {path}")

        if hf_version and rs_tag and hf_version != rs_tag:
            errors.append(
                f"[mismatch] {name}: helmfile={hf_version} vs resourceset={rs_tag} "
                f"({path})",
            )

    if errors:
        print("Flux version alignment check failed:")
        for line in errors:
            print(f" - {line}")
        return 1

    print("✓ Flux bootstrap and ResourceSet versions are aligned")
    return 0


if __name__ == "__main__":
    sys.exit(main())
