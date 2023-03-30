output "zone_id" {
  value       = data.cloudflare_zone.domain.id
  description = "The zone ID in Cloudflare."
}
