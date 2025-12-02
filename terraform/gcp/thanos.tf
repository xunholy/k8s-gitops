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
  default_event_based_hold = "false"
  force_destroy            = "false"
  location                 = "ASIA"
  name                     = "hayden-thanos-storage"
  project                  = var.project_id
  requester_pays           = "false"
  storage_class            = "STANDARD"
}
