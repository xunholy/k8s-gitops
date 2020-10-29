export USER=xunholy
export TF_VAR_org_id=raspbernetes
export TF_VAR_billing_account=
# export TF_ADMIN=${USER}-terraform-admin
export TF_ADMIN=${TF_VAR_org_id}
export TF_CREDS=~/.config/gcloud/${USER}-terraform-admin.json

# Create a new project and link it to your billing account
# gcloud projects create ${TF_ADMIN} \
#   --organization ${TF_VAR_org_id} \
#   --set-as-default

# gcloud beta billing projects link ${TF_ADMIN} \
#   --billing-account ${TF_VAR_billing_account}

# Create the service account in the Terraform admin project and download the JSON credentials
gcloud iam service-accounts create terraform \
  --display-name "Terraform admin account"

gcloud iam service-accounts keys create ${TF_CREDS} \
  --iam-account terraform@${TF_ADMIN}.iam.gserviceaccount.com

# Grant the service account permission to view the Admin Project and manage Cloud Storage
gcloud projects add-iam-policy-binding ${TF_ADMIN} \
  --member serviceAccount:terraform@${TF_ADMIN}.iam.gserviceaccount.com \
  --role roles/viewer

gcloud projects add-iam-policy-binding ${TF_ADMIN} \
  --member serviceAccount:terraform@${TF_ADMIN}.iam.gserviceaccount.com \
  --role roles/storage.admin

# Any actions that Terraform performs require that the API be enabled to do so
gcloud services enable cloudresourcemanager.googleapis.com
gcloud services enable cloudbilling.googleapis.com
gcloud services enable iam.googleapis.com
gcloud services enable compute.googleapis.com
gcloud services enable serviceusage.googleapis.com

# Grant the service account permission to create projects and assign billing accounts
gcloud organizations add-iam-policy-binding ${TF_VAR_org_id} \
  --member serviceAccount:terraform@${TF_ADMIN}.iam.gserviceaccount.com \
  --role roles/resourcemanager.projectCreator

gcloud organizations add-iam-policy-binding ${TF_VAR_org_id} \
  --member serviceAccount:terraform@${TF_ADMIN}.iam.gserviceaccount.com \
  --role roles/billing.user

# Create the remote backend bucket in Cloud Storage and the backend.tf file for storage of the terraform.tfstate file
gsutil mb -p ${TF_ADMIN} gs://${TF_ADMIN}

cat > backend.tf << EOF
terraform {
  backend "gcs" {
    bucket  = "${TF_ADMIN}"
    prefix  = "terraform/state"
  }
}
EOF

# Enable versioning for the remote bucket
gsutil versioning set on gs://${TF_ADMIN}

# Configure your environment for the Google Cloud Terraform provider
export GOOGLE_APPLICATION_CREDENTIALS=${TF_CREDS}
export GOOGLE_PROJECT=${TF_ADMIN}


