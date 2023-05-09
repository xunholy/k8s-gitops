FROM ghcr.io/siderolabs/kernel:v1.1.0-alpha.0-43-g7305bd7 as kernel

FROM ghcr.io/siderolabs/sidero-controller-manager:v0.5.0-alpha.2-33-g5ed2352
COPY --from=kernel /boot/vmlinuz /var/lib/sidero/env/agent-amd64/vmlinuz
