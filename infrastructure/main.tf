variable "project_name" {}
variable "billing_account" {}
variable "org_id" {}
variable "region" {}

provider "google" {
  region = var.region
}

resource "google_storage_bucket" "tfstate" {
  bucket_policy_only          = "false"
  default_event_based_hold    = "false"
  force_destroy               = "false"
  location                    = "US"
  name                        = "raspbernetes"
  project                     = "raspbernetes"
  requester_pays              = "false"
  storage_class               = "STANDARD"
  uniform_bucket_level_access = "false"

  versioning {
    enabled = "true"
  }
}

resource "google_storage_bucket" "velero" {
  bucket_policy_only          = "false"
  default_event_based_hold    = "false"
  force_destroy               = "false"
  location                    = "US"
  name                        = "raspbernetes-velero-backups"
  project                     = "raspbernetes"
  requester_pays              = "false"
  storage_class               = "STANDARD"
  uniform_bucket_level_access = "false"
}

resource "google_storage_bucket" "thanos" {
  bucket_policy_only          = "false"
  default_event_based_hold    = "false"
  force_destroy               = "false"
  location                    = "ASIA"
  name                        = "thanos-raspbernetes-storage"
  project                     = "raspbernetes"
  requester_pays              = "false"
  storage_class               = "STANDARD"
  uniform_bucket_level_access = "false"
}
