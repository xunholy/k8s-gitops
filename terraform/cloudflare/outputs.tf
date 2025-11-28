output "zone_id" {
  value       = data.cloudflare_zone.domain.id
  description = "The primary domain zone ID in Cloudflare."
}

output "personal_zone_id" {
  value       = data.cloudflare_zone.personal_domain.id
  description = "The personal domain zone ID in Cloudflare."
}
