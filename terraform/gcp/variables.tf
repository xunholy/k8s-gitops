variable "project_id" {
  description = "(OPTIONAL) The GCP project ID to use for the cluster. (default: hayden-agencies-infra)"
  default     = "hayden-agencies-infra"
  type        = string
}

variable "region" {
  description = "(OPTIONAL) The GCP region to use for the cluster. (default: us-central1)"
  default     = "us-central1"
  type        = string
}

variable "billing_account" {
  description = "GCP billing account ID for new projects"
  default     = "01CA75-6E963A-0AD9ED"
  type        = string
}
