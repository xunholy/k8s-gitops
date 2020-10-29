terraform {
  backend "gcs" {
    bucket  = "raspbernetes"
    prefix  = "terraform/state"
  }
}
