FROM --platform=linux/arm64 ghcr.io/talos-systems/kernel:v0.8.0-1-gfad52ab as kernel
FROM ghcr.io/talos-systems/sidero-controller-manager:v0.5.0
COPY --from=kernel /dtb/rockchip/rk3399-rock-pi-4b.dtb /var/lib/sidero/tftp/dtb/rockchip/rk3399-rock-pi-4b.dtb
