# Cloud Infrastructure

## Existing Project

Use [terraformer](https://github.com/GoogleCloudPlatform/terraformer) to import existing cloud resources that have been created into TF files.

GCP Example:

```bash
terraformer import google --resources=gcs,forwardingRules,httpHealthChecks --connect=true --regions=europe-west1,europe-west4 --projects=aaa,fff
```

## Tips

Consider terraform repository structure [best practices](https://www.terraform-best-practices.com/code-structure)
