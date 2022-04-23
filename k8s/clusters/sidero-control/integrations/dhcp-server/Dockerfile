FROM alpine:latest

WORKDIR /etc/dhcp

RUN set -xe \
	&& apk add --update --no-progress dhcp vim \
	&& rm -rf /var/cache/apk/*

RUN ["touch", "/var/lib/dhcp/dhcpd.leases"]

COPY config/ ./

CMD ["/usr/sbin/dhcpd", "-4", "-f", "-d", "--no-pid", "-cf", "/etc/dhcp/dhcpd.conf"]
