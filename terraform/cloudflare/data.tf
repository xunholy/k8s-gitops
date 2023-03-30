data "cloudflare_zone" "domain" {
  name = var.domain
}

data "sops_file" "secrets" {
  source_file = "secret.enc.yaml"
}
