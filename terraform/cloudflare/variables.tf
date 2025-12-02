variable "domain" {
  description = "(OPTIONAL) The primary domain name for business/cluster services. (default: haydenagencies.com.au)"
  default     = "haydenagencies.com.au"
  type        = string
}

variable "personal_domain" {
  description = "(OPTIONAL) The personal domain name for gaming/media services. (default: toemass.com)"
  default     = "toemass.com"
  type        = string
}

variable "kubernetes_cluster_api" {
  description = "(OPTIONAL) The Kubernetes cluster API endpoint to use for the cluster. (default: https://api.haydenagencies.com.au)"
  default     = "api.haydenagencies.com.au"
  type        = string
}

variable "session_duration" {
  description = "(OPTIONAL) The session duration for the cluster. (default: 24h)"
  default     = "24h"
  type        = string
}

variable "odoo_webhook_allowed_ips" {
  description = "(REQUIRED) CIDR list of Magento egress IPs permitted to call odoo-webhook.${var.domain}."
  default     = ["103.21.130.236/32"]
  type        = list(string)
  validation {
    condition     = length(var.odoo_webhook_allowed_ips) > 0
    error_message = "Provide at least one CIDR for odoo_webhook_allowed_ips (e.g. [\"203.0.113.10/32\"])."
  }
}
