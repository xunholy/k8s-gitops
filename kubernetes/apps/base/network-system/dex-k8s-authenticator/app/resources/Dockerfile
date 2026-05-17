FROM golang:1.25-alpine AS builder

RUN apk add --no-cache ca-certificates

WORKDIR /src

COPY go.mod go.sum ./
RUN go mod download

COPY *.go ./

RUN CGO_ENABLED=0 go build -ldflags="-s -w" -o /app/bin/dex-k8s-authenticator .

FROM alpine:3.21

RUN apk add --no-cache ca-certificates tini \
    && addgroup -g 1000 -S app \
    && adduser -u 1000 -S app -G app

WORKDIR /app

COPY --from=builder /app/bin/dex-k8s-authenticator /app/bin/dex-k8s-authenticator
COPY html/ /app/html/
COPY templates/ /app/templates/
COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh && mkdir -p /certs && chown -R app:app /app /certs

USER 1000:1000

EXPOSE 5555

ENTRYPOINT ["/sbin/tini", "--", "/entrypoint.sh"]
CMD ["--config", "/app/config.yaml"]
