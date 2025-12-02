# Odoo PostgreSQL (CNPG) Backup Infrastructure
#
# This creates:
# - GCS bucket for Barman backups (uses shared KMS key from kms.tf)
# - Service account with Storage Object Admin role
#
# After applying, retrieve the SA key and save to 1Password:
#   terraform output -raw odoo_backup_sa_key | base64 -d > /tmp/odoo-sa-key.json
#   # Copy contents to 1Password item "odoo-objstore" field "serviceAccount"

locals {
  odoo_backup_bucket = "hayden-odoo-backups"
}

# -----------------------------------------------------------------------------
# GCS Bucket for CNPG/Barman backups
# -----------------------------------------------------------------------------

resource "google_storage_bucket" "odoo_backups" {
  name                        = local.odoo_backup_bucket
  location                    = upper(local.kms_location)
  project                     = var.project_id
  storage_class               = "STANDARD"
  uniform_bucket_level_access = true
  public_access_prevention    = "enforced"
  force_destroy               = false

  encryption {
    default_kms_key_name = google_kms_crypto_key.backup_encryption.id
  }

  versioning {
    enabled = false
  }

  # Lifecycle rule: delete old backups after 30 days (CNPG also has 7d retention)
  lifecycle_rule {
    condition {
      age = 30
    }
    action {
      type = "Delete"
    }
  }

  depends_on = [
    google_kms_crypto_key_iam_member.gcs_backup_encryption
  ]
}

# -----------------------------------------------------------------------------
# Service Account for CNPG/Barman
# -----------------------------------------------------------------------------

resource "google_service_account" "odoo_backup" {
  account_id   = "odoo-pg-backup"
  display_name = "Odoo PostgreSQL Backup (CNPG/Barman)"
  description  = "Service account for CNPG to write PostgreSQL backups to GCS"
}

# Grant SA permission to read/write/delete objects in the bucket
resource "google_storage_bucket_iam_member" "odoo_backup_object_admin" {
  bucket = google_storage_bucket.odoo_backups.name
  role   = "roles/storage.objectAdmin"
  member = "serviceAccount:${google_service_account.odoo_backup.email}"
}

# Generate SA key (JSON format for gcsApplicationCredentials)
resource "google_service_account_key" "odoo_backup" {
  service_account_id = google_service_account.odoo_backup.name
}

# -----------------------------------------------------------------------------
# Outputs
# -----------------------------------------------------------------------------

output "odoo_backup_sa_email" {
  value       = google_service_account.odoo_backup.email
  description = "Email of the Odoo backup service account"
}

output "odoo_backup_sa_key" {
  value       = google_service_account_key.odoo_backup.private_key
  sensitive   = true
  description = "Base64-encoded JSON key. Decode and save to 1Password item 'odoo-objstore' field 'serviceAccount'"
}

output "odoo_backup_bucket" {
  value       = google_storage_bucket.odoo_backups.name
  description = "GCS bucket name for Odoo backups"
}
