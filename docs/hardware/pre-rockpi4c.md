# Rock Pi 4C

The following are prerequisites to create a kubernetes on the RockPi(s) 4C

## Hardware

Since I decided to use an eMMC module rather than the typical microSD I needed to purchase either a eMMC to USB adapter or eMMC to microSD adapter. I purchased both and they work equally as fine except you can save some **$$$** if you choose to purchase the eMMC to microSD adapter.

Either can be purchased with the below links:

- [eMMC microSD adapter](https://ameridroid.com/products/emmc-adapter?_pos=2&_sid=dbd8c41c9&_ss=r)
- [eMMC USB3.0 adapter](https://ameridroid.com/products/usb-3-0-emmc-module-writer?_pos=8&_sid=dbd8c41c9&_ss=r)

## Instruction

When running `apt-get upgrade` you will get the following error:

```bash
Err:4 http://apt.radxa.com/buster-stable buster InRelease
  The following signatures were invalid: EXPKEYSIG 5761288B2B52CC90 Radxa <dev@radxa.com>
Reading package lists... Done
W: GPG error: http://apt.radxa.com/buster-stable buster InRelease: The following signatures were invalid: EXPKEYSIG 5761288B2B52CC90 Radxa <dev@radxa.com>
E: The repository 'http://apt.radxa.com/buster-stable buster InRelease' is not signed.
N: Updating from such a repository can't be done securely, and is therefore disabled by default.
N: See apt-secure(8) manpage for repository creation and user configuration details.
```

To fix this error use the following steps.

Install wget

```bash
sudo apt-get update
sudo apt-get install -y wget
```

Edit /etc/apt/sources.list.d/apt-radxa-com.list

```bash
deb http://apt.radxa.com/buster-stable/ buster main
deb http://apt.radxa.com/buster-testing/ buster main
```

Add the public apt-key

```bash
wget -O - apt.radxa.com/buster-testing/public.key | sudo apt-key add -
wget -O - apt.radxa.com/buster-stable/public.key | sudo apt-key add -
```

You will no longer have the above mentioned issue. Follow instructions here to complete the kubernetes bootstrap process: https://github.com/raspbernetes/k8s-cluster-installation
