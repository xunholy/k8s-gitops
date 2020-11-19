locals {
  sops_permissions = [
    "cloudkms.cryptoKeyVersions.useToDecrypt",
    "cloudkms.cryptoKeyVersions.useToEncrypt",
    "cloudkms.cryptoKeyVersions.viewPublicKey",
    "resourcemanager.projects.get"
  ]
}

resource "google_project_iam_custom_role" "sops" {
  role_id     = "sops"
  title       = "SOPS Role"
  description = "This role grants all required SOPS permissions"
  permissions = local.sops_permissions
}

# Approved list of GCP users with the ability to encrypt/decrypt project secrets
resource "google_project_iam_binding" "sops" {
  project = var.project_id
  role    = google_project_iam_custom_role.sops.name
  members = [
    "user:saurabh.c.pandit@gmail.com",
  ]
}

resource "google_kms_key_ring" "sops" {
  name     = "sops"
  location = "global"
}

resource "google_kms_crypto_key" "sops" {
  name     = "sops-key"
  key_ring = google_kms_key_ring.sops.id
  purpose  = "ENCRYPT_DECRYPT"
  lifecycle {
    prevent_destroy = true
  }
}
