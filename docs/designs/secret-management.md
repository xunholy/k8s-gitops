# Secret Management

!!! note "Work in progress"
    This document is a work in progress.

## Requirements

- Store encrypted secrets in Git
- DIFF ciphertext in Git
- View secret metadata without decryption
- View secret spec structure without decryption
- Decrypt secrets from any machine
- Support multi-cluster encrypt/decrypt keys
- Support kube RBAC for CRUD operations
- Support IAM for decryption
- Secrets decoupled from application configuration
- Protection against committing unencrypted secrets to Git

## Proposed Solution

A combination of Sealed Secrets with SOPs...*But why?*

**Justification:**

- SOPs decryption in conjunction with Flux decrypts secrets on the fly and applies them as encoded Kubernetes secret resources, which reduces the ability to issue permissive RBAC controls to engineers without exposing the secret. Whereas Sealed Secrets would provide the ability to grant permissive RBAC controls to the encrypted custom resource that it provides whilst still protecting the sensitive information.
- Sealed Secrets can only be decrypted by the operator itself in the cluster or by utilizing the private key. If a cluster becomes unavailable and no longer recoverable you will need to recover these secrets, hence persisting the private key is required, however this key must also be protected and encrypted. SOPs natively integrates with cloud KMS and can encrypt the Sealed Secrets private key.

Utilizing both Sealed Secerets and SOPs meets the following criteria:

- [x] Store encrypted secrets in Git

Sealed Secrets allows for secrets to be stored as encrypted values in source control securely. SOPs likewise allows for the private key to be stored as an encrypted value in source control.

- [x] DIFF ciphertext in Git

Both Sealed Secrets and SOPs provide the ciphertext in source control which can be versioned and DIFF'ed between changes.

- [x] View secret metadata without decryption

Sealed Secrets doesn't encrypted the secret metadata which is useful to view the `name` and `namespace` attributes of a secret resource. SOPs by default will encrypt these values.

- [x] View secret spec structure without decryption

Both Sealed Secrets and SOPs provide the ability to view the data keys in the secret resource whilst encryption of the values to those keys.

- [x] Decrypt secrets from any machine

SOPs integrates with cloud KMS therefore no manual GPG key management is required.

- [x] Support multi-cluster encrypt/decrypt keys

Both Sealed Secrets and SOPs provide the capability to manage multi-cluster encrypt/decrypt keys, however, Sealed Secrets public cert ensures ease of use in the OSS environment without additional IAM.

- [x] Support kube RBAC for CRUD operations

Sealed Secrets provides a better RBAC model as it allows provisioning CRUD operations to the custom resource, and strict RBAC protection of the secret resource, whilst also allowing permissive RBAC of the encrypted secret custom resource.

- [x] Support IAM for decryption

SOPs provides integration with cloud KMS which inherently grants strict IAM models. Access to the private key for offline decryption should be extremely protected and only used in a break-glass scenario.

- [x] Secrets decoupled from application configuration

Applications should consume native Kubernetes secrets resources and be decoupled from the implementation of the secret provisioning. Both Sealed Secrets and SOPs can provide his behaviour.

- [ ] Protection against committing unencrypted secrets to Git

Currently a missing fundamental component is missing in both these tools which prohibits committing unencrypted values to source control. This is a function that will need to be addressed via additional tooling and/or automation.
