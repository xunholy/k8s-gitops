# ==============================================
# Zone ID Outputs
# ==============================================

output "haydenagencies_zone_id" {
  value       = data.cloudflare_zone.haydenagencies.id
  description = "The haydenagencies.com.au zone ID in Cloudflare."
}

output "toemass_zone_id" {
  value       = data.cloudflare_zone.toemass.id
  description = "The toemass.com zone ID in Cloudflare."
}

output "auntalma_zone_id" {
  value       = data.cloudflare_zone.auntalma.id
  description = "The auntalma.com.au zone ID in Cloudflare."
}

output "dropdrape_zone_id" {
  value       = data.cloudflare_zone.dropdrape.id
  description = "The dropdrape.com.au zone ID in Cloudflare."
}

# ==============================================
# Service Token Outputs (for Magento/Odoo integration)
# ==============================================

output "odoo_webhook_service_token_client_id" {
  value       = cloudflare_access_service_token.odoo_webhook.client_id
  description = "Cloudflare Access service token client_id for odoo-webhook host."
  sensitive   = true
}

output "odoo_webhook_service_token_client_secret" {
  value       = cloudflare_access_service_token.odoo_webhook.client_secret
  description = "Cloudflare Access service token client_secret for odoo-webhook host."
  sensitive   = true
}
