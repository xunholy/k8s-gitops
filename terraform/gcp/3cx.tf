# 3CX Backup Storage
#
# Separate GCP project for 3CX backups
# This isolates 3CX's Storage Admin requirement from the main infra project
# SA key managed via GCP Console

locals {
  threecx_project_id = "hayden-3cx-backups"
  org_id             = "197554449154"
}

# -----------------------------------------------------------------------------
# GCP Project
# -----------------------------------------------------------------------------

resource "google_project" "threecx" {
  name            = "Hayden 3CX Backups"
  project_id      = local.threecx_project_id
  org_id          = local.org_id
  billing_account = var.billing_account
}

resource "google_project_service" "threecx_storage" {
  project = google_project.threecx.project_id
  service = "storage.googleapis.com"
}

# -----------------------------------------------------------------------------
# GCS Bucket
# -----------------------------------------------------------------------------

resource "google_storage_bucket" "threecx_backups" {
  name                        = "hayden-agencies-3cx-backups"
  location                    = upper(local.kms_location)
  project                     = google_project.threecx.project_id
  storage_class               = "STANDARD"
  uniform_bucket_level_access = true
  public_access_prevention    = "enforced"
  force_destroy               = false

  depends_on = [google_project_service.threecx_storage]
}

# -----------------------------------------------------------------------------
# Service Account with Storage Admin (project-level, but isolated project)
# -----------------------------------------------------------------------------

resource "google_service_account" "threecx_backup" {
  project      = google_project.threecx.project_id
  account_id   = "threecx-backup"
  display_name = "3CX Phone System Backup"
  description  = "Service account for 3CX to write backups to GCS"
}

resource "google_project_iam_member" "threecx_storage_admin" {
  project = google_project.threecx.project_id
  role    = "roles/storage.admin"
  member  = "serviceAccount:${google_service_account.threecx_backup.email}"
}

# -----------------------------------------------------------------------------
# Outputs
# -----------------------------------------------------------------------------

output "threecx_project_id" {
  value       = google_project.threecx.project_id
  description = "GCP project ID for 3CX backups"
}

output "threecx_backup_bucket" {
  value       = google_storage_bucket.threecx_backups.name
  description = "GCS bucket name for 3CX backups"
}

output "threecx_backup_sa_email" {
  value       = google_service_account.threecx_backup.email
  description = "Email of the 3CX backup service account"
}
