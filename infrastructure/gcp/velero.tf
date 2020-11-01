locals {
  velero_permissions = [
    "compute.disks.get",
    "compute.disks.create",
    "compute.disks.createSnapshot",
    "compute.snapshots.get",
    "compute.snapshots.create",
    "compute.snapshots.useReadOnly",
    "compute.snapshots.delete",
    "compute.zones.get"
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

resource "google_service_account_iam_binding" "velero" {
  service_account_id = google_service_account.velero.name
  role               = google_project_iam_custom_role.velero.name
  members = [
    "serviceAccount:${google_service_account.velero.email}",
  ]
}

resource "google_storage_bucket" "velero" {
  default_event_based_hold = "false"
  force_destroy            = "false"
  location                 = "US"
  name                     = "raspbernetes-velero-backups"
  project                  = "raspbernetes"
  requester_pays           = "false"
  storage_class            = "STANDARD"
}
