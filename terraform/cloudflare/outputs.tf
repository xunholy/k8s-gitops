output "zone_id" {
  value       = data.cloudflare_zone.domain.id
  description = "The Cloudflare zone ID"
}

output "r2_bucket_name" {
  value       = cloudflare_r2_bucket.game_downloads.name
  description = "The R2 bucket name"
}

output "downloads_url" {
  value       = "https://downloads.${var.domain}"
  description = "The public download URL"
}
