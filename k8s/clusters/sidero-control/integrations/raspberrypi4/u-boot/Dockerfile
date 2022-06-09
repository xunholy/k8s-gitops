FROM --platform=linux/arm64 ghcr.io/talos-systems/u-boot:v0.10.0-alpha.0-35-g325439e as u-boot
FROM --platform=linux/arm64 ghcr.io/talos-systems/raspberrypi-firmware:v0.10.0-alpha.0-35-g325439e as boot

FROM ghcr.io/frezbo/sidero-controller-manager:v0.5.1-dirty

COPY --from=u-boot /rpi_4/u-boot.bin /var/lib/sidero/tftp/
COPY --from=u-boot /rpi_4/bl31.bin /var/lib/sidero/tftp/

COPY --from=boot /boot/start4.elf /var/lib/sidero/tftp/start4.elf
COPY --from=boot /boot/overlays/disable-bt.dtbo /var/lib/sidero/tftp/overlays/disable-bt.dtbo
COPY --from=boot /boot/bcm2711-rpi-4-b.dtb /var/lib/sidero/tftp/bcm2711-rpi-4-b.dtb
COPY --from=boot /boot/fixup4.dat /var/lib/sidero/tftp/fixup4.dat

COPY config.txt /var/lib/sidero/tftp/config.txt
