FROM alpine:latest
RUN set -xe \
	&& apk add --update --no-progress dhcp \
	&& rm -rf /var/cache/apk/*
RUN ["touch", "/var/lib/dhcp/dhcpd.leases"]
CMD ["/usr/sbin/dhcpd", "-4", "-f", "-d", "--no-pid", "-cf", "/etc/dhcp/dhcpd.conf"]
