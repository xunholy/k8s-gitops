data "cloudflare_zone" "domain" {
  filter {
    name = var.domain
  }
}

resource "cloudflare_r2_bucket" "game_downloads" {
  account_id = var.cloudflare_account_id
  name       = "game-downloads"
  location   = "APAC"
}

resource "cloudflare_r2_custom_domain" "downloads" {
  account_id  = var.cloudflare_account_id
  bucket_name = cloudflare_r2_bucket.game_downloads.name
  domain      = "downloads.${var.domain}"
  zone_id     = data.cloudflare_zone.domain.id
  enabled     = true
}
