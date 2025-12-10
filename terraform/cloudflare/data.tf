# ==============================================
# Data Sources - All Cloudflare Zones
# ==============================================

data "cloudflare_zone" "haydenagencies" {
  name = var.domain
}

data "cloudflare_zone" "toemass" {
  name = var.personal_domain
}

data "cloudflare_zone" "auntalma" {
  name = var.auntalma_domain
}

data "cloudflare_zone" "dropdrape" {
  name = var.dropdrape_domain
}

data "sops_file" "secrets" {
  source_file = "secret.enc.yaml"
}

