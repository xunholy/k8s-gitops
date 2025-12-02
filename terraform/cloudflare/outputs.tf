output "zone_id" {
  value       = data.cloudflare_zone.domain.id
  description = "The primary domain zone ID in Cloudflare."
}

output "personal_zone_id" {
  value       = data.cloudflare_zone.personal_domain.id
  description = "The personal domain zone ID in Cloudflare."
}

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
