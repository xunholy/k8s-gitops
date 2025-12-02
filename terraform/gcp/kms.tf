# Shared KMS Infrastructure for GCS Bucket Encryption
#
# This key ring and key are used by all backup buckets:
# - hayden-odoo-backups
# - hayden-thanos-storage
# - hayden-velero-backups
# - hayden-openebs-backups

locals {
  kms_location   = "australia-southeast2"
  project_number = "854101959314"
}

# -----------------------------------------------------------------------------
# KMS Key Ring and Key
# -----------------------------------------------------------------------------

resource "google_kms_key_ring" "backups" {
  name     = "k8s-backups"
  location = local.kms_location
}

resource "google_kms_crypto_key" "backup_encryption" {
  name     = "backup-encryption"
  key_ring = google_kms_key_ring.backups.id
  purpose  = "ENCRYPT_DECRYPT"

  lifecycle {
    prevent_destroy = true
  }
}

# Allow GCS service agent to use the CMEK key for all buckets
resource "google_kms_crypto_key_iam_member" "gcs_backup_encryption" {
  crypto_key_id = google_kms_crypto_key.backup_encryption.id
  role          = "roles/cloudkms.cryptoKeyEncrypterDecrypter"
  member        = "serviceAccount:service-${local.project_number}@gs-project-accounts.iam.gserviceaccount.com"
}

# -----------------------------------------------------------------------------
# Outputs
# -----------------------------------------------------------------------------

output "backup_kms_key_id" {
  value       = google_kms_crypto_key.backup_encryption.id
  description = "KMS key ID used for CMEK encryption of all backup buckets"
}
