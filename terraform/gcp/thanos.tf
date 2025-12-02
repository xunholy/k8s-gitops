locals {
  thanos_permissions = [
    "compute.snapshots.create",
    "compute.snapshots.delete",
    "compute.snapshots.get",
    "compute.snapshots.list",
    "compute.snapshots.useReadOnly",
    "compute.zones.get",
    "compute.zones.list",
    "resourcemanager.projects.get",
    "storage.objects.create",
    "storage.objects.delete",
    "storage.objects.get",
    "storage.objects.getIamPolicy",
    "storage.objects.list",
    "storage.objects.setIamPolicy",
    "storage.objects.update"
  ]
}

resource "google_service_account" "thanos" {
  account_id   = "thanos"
  display_name = "Service account for Thanos"
}

resource "google_project_iam_custom_role" "thanos" {
  role_id     = "thanos"
  title       = "Thanos Role"
  description = "This role grants all required Thanos permissions"
  permissions = local.thanos_permissions
}

resource "google_project_iam_binding" "thanos" {
  project = var.project_id
  role    = google_project_iam_custom_role.thanos.name
  members = [
    "serviceAccount:${google_service_account.thanos.email}",
  ]
}

resource "google_storage_bucket" "thanos" {
  default_event_based_hold    = "false"
  force_destroy               = "false"
  location                    = "AUSTRALIA-SOUTHEAST2"
  name                        = "hayden-thanos-storage"
  project                     = var.project_id
  requester_pays              = "false"
  storage_class               = "STANDARD"
  uniform_bucket_level_access = true
  public_access_prevention    = "enforced"

  encryption {
    default_kms_key_name = google_kms_crypto_key.backup_encryption.id
  }

  depends_on = [
    google_kms_crypto_key_iam_member.gcs_backup_encryption
  ]
}

# Grant Thanos SA access to its bucket
resource "google_storage_bucket_iam_member" "thanos_object_admin" {
  bucket = google_storage_bucket.thanos.name
  role   = "roles/storage.objectAdmin"
  member = "serviceAccount:${google_service_account.thanos.email}"
}

# Generate SA key for 1Password
resource "google_service_account_key" "thanos" {
  service_account_id = google_service_account.thanos.name
}

output "thanos_sa_key" {
  value       = google_service_account_key.thanos.private_key
  sensitive   = true
  description = "Base64-encoded JSON key for Thanos. Decode and save to 1Password item 'thanos-objstore' field 'serviceAccount'"
}
