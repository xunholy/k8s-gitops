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
