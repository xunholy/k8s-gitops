# ==============================================
# haydenagencies.com.au - Primary Business Domain
# Zone ID: 012445366374fc5a3f865cc809e459e0
# ==============================================

locals {
  haydenagencies_zone_id = data.cloudflare_zone.haydenagencies.id
  odoo_webhook_hostname  = "odoo-webhook.${var.domain}"
}

# ==============================================
# IP Lists (WAF)
# ==============================================

resource "cloudflare_list" "whitelisted_ips" {
  account_id  = data.cloudflare_zone.haydenagencies.account_id
  name        = "whitelisted_ips"
  description = "IPs that bypass rate limiting and WAF challenges"
  kind        = "ip"

  item {
    value {
      ip = "119.18.0.248"
    }
    comment = "Rival - Justin"
  }

  item {
    value {
      ip = "144.6.92.244"
    }
    comment = "Hayden"
  }

  item {
    value {
      ip = "121.200.5.240"
    }
    comment = "Thomas home IP"
  }
}

# ==============================================
# Access Applications and Policies
# ==============================================

resource "cloudflare_access_application" "kubernetes_cluster_access" {
  zone_id                    = local.haydenagencies_zone_id
  app_launcher_visible       = true
  auto_redirect_to_identity  = false
  domain                     = var.kubernetes_cluster_api
  enable_binding_cookie      = false
  http_only_cookie_attribute = false
  name                       = "Kubernetes Cluster Access"
  session_duration           = var.session_duration
  skip_interstitial          = true
  type                       = "self_hosted"
  cors_headers {
    allow_all_methods = true
    allow_all_origins = true
    allowed_methods   = ["CONNECT", "TRACE", "GET", "HEAD", "POST", "PATCH", "PUT", "OPTIONS", "DELETE"]
  }
}

resource "cloudflare_access_policy" "kubernetes_cluster_access_allow_thomas" {
  zone_id        = local.haydenagencies_zone_id
  application_id = cloudflare_access_application.kubernetes_cluster_access.id
  name           = "Allow thomas@haydenagencies.com.au"
  precedence     = 1
  decision       = "allow"

  include {
    email = ["thomas@haydenagencies.com.au"]
  }
}

# Service token for Odoo webhook (account-level, can be used across zones)
resource "cloudflare_access_service_token" "odoo_webhook" {
  account_id = data.cloudflare_zone.haydenagencies.account_id
  name       = "odoo-webhook"
}

resource "cloudflare_access_application" "odoo_webhook" {
  zone_id                    = local.haydenagencies_zone_id
  app_launcher_visible       = false
  auto_redirect_to_identity  = false
  domain                     = local.odoo_webhook_hostname
  enable_binding_cookie      = false
  http_only_cookie_attribute = true
  name                       = "Odoo Webhook"
  session_duration           = var.session_duration
  skip_interstitial          = true
  type                       = "self_hosted"
}

resource "cloudflare_access_policy" "odoo_webhook" {
  zone_id        = local.haydenagencies_zone_id
  application_id = cloudflare_access_application.odoo_webhook.id
  decision       = "non_identity"
  name           = "Magento webhook allowlist"
  precedence     = 1

  include {
    service_token = [cloudflare_access_service_token.odoo_webhook.id]
  }

  require {
    ip = var.odoo_webhook_allowed_ips
  }
}

# ==============================================
# WAF - Rulesets (Modern - replaces legacy filters/firewall_rules)
# ==============================================

resource "cloudflare_ruleset" "haydenagencies_rate_limit" {
  zone_id = local.haydenagencies_zone_id
  kind    = "zone"
  name    = "default"
  phase   = "http_ratelimit"

  rules {
    action      = "block"
    description = "Rate Limit"
    enabled     = true
    expression  = "(not http.request.uri.path contains \"media\" and not http.request.uri.path contains \"static\" and not http.request.uri.path contains \"haydenadmin\")"
    ratelimit {
      characteristics     = ["ip.src", "cf.colo.id"]
      mitigation_timeout  = var.haydenagencies_rate_limit_period
      period              = var.haydenagencies_rate_limit_period
      requests_per_period = var.haydenagencies_rate_limit_requests
    }
  }
}

# NOTE: Cache settings ruleset requires additional permissions
# resource "cloudflare_ruleset" "haydenagencies_cache_settings" {
#   zone_id = local.haydenagencies_zone_id
#   kind    = "zone"
#   name    = "default"
#   phase   = "http_request_cache_settings"
# }

resource "cloudflare_ruleset" "haydenagencies_firewall_custom" {
  zone_id = local.haydenagencies_zone_id
  kind    = "zone"
  name    = "default"
  phase   = "http_request_firewall_custom"

  rules {
    action = "skip"
    action_parameters {
      phases  = ["http_ratelimit", "http_request_firewall_managed", "http_request_sbfm"]
      ruleset = "current"
    }
    description = "Whitelisted IPs"
    enabled     = true
    expression  = "(ip.src in $whitelisted_ips)"
    logging {
      enabled = true
    }
  }

  rules {
    action      = "managed_challenge"
    description = "Challenge non-whitelisted countries"
    enabled     = true
    expression  = "(not ip.geoip.country in {\"${join("\" \"", var.haydenagencies_whitelisted_countries)}\"})"
  }
}

# ==============================================
# Zone Settings (matching current Cloudflare config)
# ==============================================

resource "cloudflare_zone_settings_override" "haydenagencies" {
  zone_id = local.haydenagencies_zone_id
  settings {
    always_online            = "off"
    always_use_https         = "off"
    automatic_https_rewrites = "on"
    brotli                   = "on"
    browser_cache_ttl        = 14400
    browser_check            = "on"
    cache_level              = "aggressive"
    challenge_ttl            = 1800
    cname_flattening         = "flatten_at_root"
    development_mode         = "off"
    early_hints              = "off"
    email_obfuscation        = "on"
    hotlink_protection       = "off"
    http3                    = "on"
    ip_geolocation           = "on"
    ipv6                     = "on"
    max_upload               = 100
    min_tls_version          = "1.0"
    opportunistic_encryption = "on"
    opportunistic_onion      = "on"
    privacy_pass             = "on"
    pseudo_ipv4              = "off"
    rocket_loader            = "off"
    security_header {
      enabled            = false
      include_subdomains = false
      max_age            = 0
      nosniff            = false
      preload            = false
    }
    security_level      = "medium"
    server_side_exclude = "on"
    ssl                 = "full"
    tls_1_3             = "on"
    tls_client_auth     = "off"
    websockets          = "on"
    zero_rtt            = "off"
  }
}

# NOTE: Argo Smart Routing cannot be managed via API token
# It requires Argo subscription and special billing-level access
# Manage via Cloudflare Dashboard instead
# ==============================================
# Argo Smart Routing
# ==============================================
# resource "cloudflare_argo" "haydenagencies" {
#   zone_id        = local.haydenagencies_zone_id
#   smart_routing  = "on"
#   tiered_caching = "on"
# }
