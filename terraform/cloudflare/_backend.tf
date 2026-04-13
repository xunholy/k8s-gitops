terraform {
  backend "gcs" {
    bucket = "raspbernetes-cloudflare-terraform-state"
    prefix = "terraform/state"
  }
}
