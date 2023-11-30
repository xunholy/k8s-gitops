FROM alpine:3.18.5

WORKDIR /etc/dhcp

RUN set -xe \
	&& apk add --no-cache --no-progress dhcp vim \
	&& rm -rf /var/cache/apk/*

RUN ["touch", "/var/lib/dhcp/dhcpd.leases"]

COPY config/ ./

CMD ["/usr/sbin/dhcpd", "-4", "-f", "-d", "--no-pid", "-cf", "/etc/dhcp/dhcpd.conf"]
