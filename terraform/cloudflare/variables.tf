variable "cloudflare_api_token" {
  description = "Cloudflare API token with R2 and DNS permissions"
  type        = string
  sensitive   = true
}

variable "cloudflare_account_id" {
  description = "Cloudflare account ID"
  type        = string
}

variable "domain" {
  description = "The domain name managed in Cloudflare"
  default     = "owncloud.ai"
  type        = string
}
