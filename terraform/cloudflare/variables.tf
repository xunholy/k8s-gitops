# ==============================================
# Domain Variables
# ==============================================

variable "domain" {
  description = "The primary domain name for business/cluster services."
  default     = "haydenagencies.com.au"
  type        = string
}

variable "personal_domain" {
  description = "The personal domain name for gaming/media services."
  default     = "toemass.com"
  type        = string
}

variable "auntalma_domain" {
  description = "The auntalma domain."
  default     = "auntalma.com.au"
  type        = string
}

variable "dropdrape_domain" {
  description = "The dropdrape domain."
  default     = "dropdrape.com.au"
  type        = string
}

# ==============================================
# Access Application Variables
# ==============================================

variable "kubernetes_cluster_api" {
  description = "The Kubernetes cluster API endpoint hostname."
  default     = "api.haydenagencies.com.au"
  type        = string
}

variable "session_duration" {
  description = "The default session duration for Access applications."
  default     = "24h"
  type        = string
}

variable "odoo_webhook_allowed_ips" {
  description = "CIDR list of Magento egress IPs permitted to call the odoo-webhook endpoint."
  default     = ["103.21.130.236/32"]
  type        = list(string)
  validation {
    condition     = length(var.odoo_webhook_allowed_ips) > 0
    error_message = "Provide at least one CIDR for odoo_webhook_allowed_ips."
  }
}

# ==============================================
# WAF Variables - Primary Domain (haydenagencies.com.au)
# ==============================================

variable "haydenagencies_whitelisted_countries" {
  description = "Countries allowed without challenge on haydenagencies.com.au"
  default     = ["AU", "NZ", "GB", "US", "GR"]
  type        = list(string)
}

variable "haydenagencies_rate_limit_requests" {
  description = "Rate limit requests per period for haydenagencies.com.au"
  default     = 100
  type        = number
}

variable "haydenagencies_rate_limit_period" {
  description = "Rate limit period in seconds for haydenagencies.com.au"
  default     = 10
  type        = number
}
