variable "domain" {
  description = "(OPTIONAL) The domain name to use for the cluster. (default: raspbernetes.com)"
  default     = "raspbernetes.com"
  type        = string
}

variable "kubernetes_cluster_api" {
  description = "(OPTIONAL) The Kubernetes cluster API endpoint to use for the cluster. (default: https://api.raspbernetes.com)"
  default     = "api.raspbernetes.com"
  type        = string
}

variable "session_duration" {
  description = "(OPTIONAL) The session duration for the cluster. (default: 24h)"
  default     = "24h"
  type        = string
}
