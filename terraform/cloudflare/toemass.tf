# ==============================================
# toemass.com - Personal Domain
# Zone ID: 1884c399c3d0c0410b1b99bf3ad98131
# ==============================================

locals {
  toemass_zone_id       = data.cloudflare_zone.toemass.id
  toemass_odoo_hostname = "odoo-api.${var.personal_domain}"
  # Magento server IP for odoo-api access
  magento_ip = "103.21.130.236"
}

# ==============================================
# Access Applications and Policies
# ==============================================

# Existing Odoo Webhook API access application
resource "cloudflare_access_application" "toemass_odoo_api" {
  account_id                 = data.cloudflare_zone.toemass.account_id
  app_launcher_visible       = true
  auto_redirect_to_identity  = false
  domain                     = local.toemass_odoo_hostname
  enable_binding_cookie      = false
  http_only_cookie_attribute = false
  name                       = "Odoo Webhook API"
  session_duration           = "0s"
  type                       = "self_hosted"
}

# Note: This application uses the account-level hayden-tunnel-access service token
# The policy is embedded in the access application in newer Cloudflare API versions

# ==============================================
# WAF - Rulesets (Modern - replaces legacy filters/firewall_rules)
# ==============================================

resource "cloudflare_ruleset" "toemass_firewall_custom" {
  zone_id = local.toemass_zone_id
  kind    = "zone"
  name    = "default"
  phase   = "http_request_firewall_custom"

  rules {
    action      = "block"
    description = "block all traffic to odoo tunnel that isnt magento"
    enabled     = true
    expression  = "(http.host eq \"${local.toemass_odoo_hostname}\" and ip.src ne ${local.magento_ip})"
  }
}

# ==============================================
# Zone Settings (matching current Cloudflare config)
# ==============================================

resource "cloudflare_zone_settings_override" "toemass" {
  zone_id = local.toemass_zone_id
  settings {
    always_online             = "off"
    always_use_https          = "on"
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

# NOTE: Argo Smart Routing cannot be managed via API token
# It requires Argo subscription and special billing-level access
# Manage via Cloudflare Dashboard instead
# ==============================================
# Argo Smart Routing
# ==============================================
# resource "cloudflare_argo" "toemass" {
#   zone_id        = local.toemass_zone_id
#   smart_routing  = "on"
#   tiered_caching = "on"
# }
