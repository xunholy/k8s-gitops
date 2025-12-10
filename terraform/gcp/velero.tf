locals {
  velero_permissions = [
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

resource "google_service_account" "velero" {
  account_id   = "velero"
  display_name = "Service account for Velero"
}

resource "google_project_iam_custom_role" "velero" {
  role_id     = "velero"
  title       = "Velero Role"
  description = "This role grants all required Velero permissions"
  permissions = local.velero_permissions
}

resource "google_project_iam_binding" "velero" {
  project = var.project_id
  role    = google_project_iam_custom_role.velero.name
  members = [
    "serviceAccount:${google_service_account.velero.email}",
  ]
}

resource "google_storage_bucket_iam_binding" "binding" {
  bucket = google_storage_bucket.openebs.name
  role   = "projects/${var.project_id}/roles/${google_project_iam_custom_role.velero.role_id}"
  members = [
    "serviceAccount:${google_service_account.velero.email}",
  ]
}

resource "google_storage_bucket" "velero" {
  default_event_based_hold    = "false"
  force_destroy               = "false"
  location                    = "AUSTRALIA-SOUTHEAST2"
  name                        = "hayden-velero-backups"
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

resource "google_storage_bucket" "openebs" {
  default_event_based_hold    = "false"
  force_destroy               = "false"
  location                    = "AUSTRALIA-SOUTHEAST2"
  name                        = "hayden-openebs-backups"
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

# Grant Velero SA access to its bucket
resource "google_storage_bucket_iam_member" "velero_object_admin" {
  bucket = google_storage_bucket.velero.name
  role   = "roles/storage.objectAdmin"
  member = "serviceAccount:${google_service_account.velero.email}"
}

# SA key managed externally via 1Password (velero-gcs)
