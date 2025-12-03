# ==============================================
# auntalma.com.au - Additional Domain
# Zone ID: 889a3d92f5045d3901cb850324d45506
# ==============================================

locals {
  auntalma_zone_id = data.cloudflare_zone.auntalma.id
}

# ==============================================
# Zone Settings (matching current Cloudflare config)
# ==============================================

resource "cloudflare_zone_settings_override" "auntalma" {
  zone_id = local.auntalma_zone_id
  settings {
    always_online             = "off"
    always_use_https          = "off"
    automatic_https_rewrites  = "on"
    brotli                    = "on"
    browser_cache_ttl         = 14400
    browser_check             = "on"
    cache_level               = "aggressive"
    challenge_ttl             = 1800
    cname_flattening          = "flatten_at_root"
    development_mode          = "off"
    early_hints               = "off"
    email_obfuscation         = "on"
    hotlink_protection        = "off"
    http3                     = "on"
    ip_geolocation            = "on"
    ipv6                      = "on"
    max_upload                = 100
    min_tls_version           = "1.0"
    opportunistic_encryption    = "on"
    opportunistic_onion         = "on"
    privacy_pass                = "on"
    pseudo_ipv4                 = "off"
    rocket_loader               = "off"
    security_header {
      enabled            = false
      include_subdomains = false
      max_age            = 0
      nosniff            = false
      preload            = false
    }
    security_level              = "medium"
    server_side_exclude         = "on"
    ssl                         = "full"
    tls_1_3                     = "on"
    tls_client_auth             = "off"
    websockets                  = "on"
    zero_rtt                    = "off"
  }
}
