provider "cloudflare" {
  api_token = data.sops_file.secrets.data["api_token"]
}
