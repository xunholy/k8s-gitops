# Required GCP APIs for Terraform operations
resource "google_project_service" "iam" {
  project            = var.project_id
  service            = "iam.googleapis.com"
  disable_on_destroy = false
}

resource "google_project_service" "cloudresourcemanager" {
  project            = var.project_id
  service            = "cloudresourcemanager.googleapis.com"
  disable_on_destroy = false
}

resource "google_project_service" "cloudkms" {
  project            = var.project_id
  service            = "cloudkms.googleapis.com"
  disable_on_destroy = false
}

resource "google_project_service" "storage" {
  project            = var.project_id
  service            = "storage.googleapis.com"
  disable_on_destroy = false
}

# Service account for GitHub Actions CI/CD
resource "google_service_account" "terraform_ci" {
  account_id   = "terraform-ci"
  display_name = "Terraform CI Service Account"
  description  = "Service account for GitHub Actions to run Terraform plan/apply"
}

# Grant required roles to terraform-ci service account
resource "google_project_iam_member" "terraform_ci_storage_admin" {
  project = var.project_id
  role    = "roles/storage.admin"
  member  = "serviceAccount:${google_service_account.terraform_ci.email}"
}

# Custom role for Terraform to manage KMS resources without decrypt access
resource "google_project_iam_custom_role" "terraform_ci_kms" {
  role_id     = "terraformCiKms"
  title       = "Terraform CI KMS Manager"
  description = "Allows Terraform to manage KMS resources without decrypt/encrypt permissions"
  permissions = [
    "cloudkms.cryptoKeys.create",
    "cloudkms.cryptoKeys.get",
    "cloudkms.cryptoKeys.list",
    "cloudkms.cryptoKeys.update",
    "cloudkms.cryptoKeyVersions.get",
    "cloudkms.cryptoKeyVersions.list",
    "cloudkms.keyRings.create",
    "cloudkms.keyRings.get",
    "cloudkms.keyRings.list",
    "cloudkms.locations.get",
    "cloudkms.locations.list",
    # Note: Does NOT include useToDecrypt or useToEncrypt
  ]
}

resource "google_project_iam_member" "terraform_ci_kms" {
  project = var.project_id
  role    = google_project_iam_custom_role.terraform_ci_kms.name
  member  = "serviceAccount:${google_service_account.terraform_ci.email}"
}

resource "google_project_iam_member" "terraform_ci_iam_sa_admin" {
  project = var.project_id
  role    = "roles/iam.serviceAccountAdmin"
  member  = "serviceAccount:${google_service_account.terraform_ci.email}"
}

resource "google_project_iam_member" "terraform_ci_iam_role_admin" {
  project = var.project_id
  role    = "roles/iam.roleAdmin"
  member  = "serviceAccount:${google_service_account.terraform_ci.email}"
}

# Generate SA key for GitHub Actions secret
resource "google_service_account_key" "terraform_ci" {
  service_account_id = google_service_account.terraform_ci.name
}

output "terraform_ci_sa_key" {
  value       = google_service_account_key.terraform_ci.private_key
  sensitive   = true
  description = "Base64-encoded JSON key for terraform-ci. Decode and set as GitHub secret GCP_SA_KEY"
}
