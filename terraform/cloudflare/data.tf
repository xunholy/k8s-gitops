data "cloudflare_zone" "domain" {
  name = var.domain
}

data "cloudflare_zone" "personal_domain" {
  name = var.personal_domain
}

data "sops_file" "secrets" {
  source_file = "secret.enc.yaml"
}
