terraform {
  backend "gcs" {
    bucket = "raspbernetes-gitops-terraform-state"
    prefix = "terraform/state"
  }
}
