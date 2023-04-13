variable "project_id" {
  description = "(OPTIONAL) The GCP project ID to use for the cluster. (default: raspbernetes)"
  default     = "raspbernetes"
  type        = string
}

variable "region" {
  description = "(OPTIONAL) The GCP region to use for the cluster. (default: us-central1)"
  default     = "us-central1"
  type        = string
}
