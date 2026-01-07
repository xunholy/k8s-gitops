# Claude Agents for GitOps Operations

This directory contains specialized Claude agent definitions for Kubernetes and DevOps/Platform engineering tasks in this FluxCD GitOps repository.

## Available Agents

| Agent | File | Purpose |
|-------|------|---------|
| **Flux Troubleshooter** | [flux-troubleshooter.md](./flux-troubleshooter.md) | Diagnose and resolve FluxCD resource issues |
| **GitOps Deployer** | [gitops-deployer.md](./gitops-deployer.md) | Deploy applications following repository patterns |
| **Security Auditor** | [security-auditor.md](./security-auditor.md) | Security review of manifests and configurations |
| **Dependency Mapper** | [dependency-mapper.md](./dependency-mapper.md) | Map and validate Flux dependency chains |
| **Resource Optimizer** | [resource-optimizer.md](./resource-optimizer.md) | Optimize resource requests/limits and autoscaling |

## How to Use These Agents

### Method 1: Reference Agent in Prompts

When working with Claude Code, reference an agent by name:

```
"As the Flux Troubleshooter agent, diagnose why the prometheus
HelmRelease in the observability namespace is failing to reconcile."
```

```
"As the GitOps Deployer agent, deploy Grafana Tempo to the
observability namespace using the official Grafana OCI Helm chart."
```

### Method 2: Use Agent Context

Claude Code will automatically use agent context when the task matches the agent's expertise:

```
"Why is my prometheus HelmRelease failing?"
→ Claude may automatically use Flux Troubleshooter agent context
```

```
"Add Redis to the database namespace"
→ Claude may automatically use GitOps Deployer agent context
```

### Method 3: Explicit Agent Mode

Some Claude Code interfaces may support explicit agent selection:

```
/agent flux-troubleshooter
Diagnose prometheus reconciliation failure
```

## Quick Start Examples

### Deploy New Application

```
As the GitOps Deployer agent:

Deploy Grafana Mimir to the observability namespace.
- Use official Grafana OCI Helm chart
- Configure S3 backend (use existing thanos bucket)
- Enable distributed mode with 3 replicas
- Create SOPS-encrypted secret for S3 credentials
```

**Expected Output:**
- Complete directory structure
- All required YAML manifests
- SOPS encryption commands
- Deployment instructions

### Troubleshoot Flux Issue

```
As the Flux Troubleshooter agent:

The kube-prometheus-stack HelmRelease is stuck in "Upgrade Failed".
Diagnose the root cause and provide remediation steps.
```

**Expected Output:**
- Status analysis
- Root cause identification
- Step-by-step remediation
- Verification commands

### Security Audit

```
As the Security Auditor agent:

Audit all manifests in the network-system namespace for security issues.
Focus on secret management, pod security standards, and RBAC.
```

**Expected Output:**
- Structured security report
- Severity-rated issues
- Remediation steps for each issue
- Compliance summary

### Map Dependencies

```
As the Dependency Mapper agent:

Map the complete dependency chain for all applications in the
istio-system and istio-ingress namespaces.
```

**Expected Output:**
- Dependency tree visualization
- Reconciliation order
- Validation results
- Optimization recommendations

### Optimize Resources

```
As the Resource Optimizer agent:

Analyze the observability namespace and recommend resource optimizations.
Provide cost savings estimates and HPA configurations.
```

**Expected Output:**
- Current vs actual usage analysis
- Optimized resource recommendations
- HPA configurations
- Cost savings estimates

## Common Workflows

### 1. Complete Application Deployment

Chain multiple agents for end-to-end deployment:

```
Step 1 - Deploy:
  As the GitOps Deployer agent, deploy <app> to <namespace>

Step 2 - Secure:
  As the Security Auditor agent, audit <namespace>/<app>

Step 3 - Validate:
  As the Dependency Mapper agent, verify <app> dependencies

Step 4 - Monitor:
  As the Flux Troubleshooter agent, verify <app> reconciliation
```

### 2. Incident Response

```
Step 1 - Diagnose:
  As the Flux Troubleshooter agent, diagnose <issue>

Step 2 - Dependencies:
  As the Dependency Mapper agent, check if dependencies are healthy

Step 3 - Optimize:
  As the Resource Optimizer agent, check for resource exhaustion
```

### 3. Security Hardening

```
Step 1 - Audit:
  As the Security Auditor agent, audit all namespaces

Step 2 - Validate:
  Review and apply security recommendations
```

## Agent Capabilities Summary

### Flux Troubleshooter
✅ Diagnose HelmRelease failures
✅ Diagnose Kustomization failures
✅ Check controller health
✅ Trace dependency issues
✅ Provide remediation steps

### GitOps Deployer
✅ Create application directory structure
✅ Generate HelmRelease + OCIRepository
✅ Configure Flux Kustomizations
✅ Set up SOPS secrets
✅ Create cluster overlays
✅ Follow repository conventions

### Security Auditor
✅ Scan for unencrypted secrets
✅ Validate pod security standards
✅ Review RBAC configurations
✅ Check network policies
✅ Identify privilege escalation
✅ Verify SOPS encryption

### Dependency Mapper
✅ Build dependency graphs
✅ Detect circular dependencies
✅ Calculate reconciliation order
✅ Validate cross-namespace refs
✅ Suggest optimizations
✅ Identify critical path

### Resource Optimizer
✅ Analyze resource usage
✅ Recommend requests/limits
✅ Configure HPA
✅ Calculate cost savings
✅ Right-size workloads
✅ Identify over/under-provisioning

## Repository Context

All agents have access to repository-specific context:

- **Cluster**: cluster-00 (default)
- **Branch**: main (auto-reconciled by Flux)
- **Bootstrap**: Flux Operator (not traditional `flux bootstrap`)
- **Charts**: OCIRepository (not HelmRepository)
- **Secrets**: SOPS with PGP + Age + GCP KMS
- **SOPS PGP**: `0635B8D34037A9453003FB7B93CAA682FF4C9014`
- **SOPS Age**: `age19gj66fq5v2veu940ftyj4pkw0w5tgxgddlyqnd00pnjzyndevurqx70g4t`

## Agent Development

### Creating New Agents

To create a new agent:

1. **Create agent file**: `.claude/agents/my-agent.md`
2. **Define expertise**: List capabilities and focus area
3. **Document workflow**: Step-by-step process the agent follows
4. **Provide examples**: Commands, YAML snippets, expected outputs
5. **Include context**: Repository-specific patterns and conventions
6. **Test**: Validate agent with real repository tasks

### Agent Template

```markdown
# [Agent Name] Agent

You are a [domain] expert specializing in [specific focus].

## Expertise

- List capabilities
- Specific skills
- Tools and technologies

## Workflow

When [task description], follow this approach:

1. **Step 1**: Description
2. **Step 2**: Description
3. **Step 3**: Description

## Common Patterns

[Provide patterns, examples, code snippets]

## Repository-Specific Context

[Repository conventions and specifics]

## Output Format

[Expected output structure]
```

## Best Practices

1. **Be Specific**: Provide clear context (namespace, app name, requirements)
2. **Iterate**: Review agent output, refine with follow-up questions
3. **Validate**: Always validate agent-generated code before applying
4. **Chain Agents**: Use multiple agents for complex workflows
5. **Learn**: Study agent outputs to understand GitOps patterns
6. **Customize**: Fork and modify agents for your specific needs

## Limitations

Agents are guidance tools, not autonomous actors:

- ⚠️ **Always review** generated YAML manifests
- ⚠️ **Test in staging** before production
- ⚠️ **Validate** against cluster policies
- ⚠️ **Understand** the recommendations before applying
- ⚠️ **Monitor** after applying changes

## Integration Points

### With FluxCD
- Agents understand Flux Operator patterns
- Generate FluxCD-compatible manifests
- Follow repository's Flux conventions

### With SOPS
- Agents use repository SOPS configuration
- Generate properly encrypted secrets
- Configure Flux decryption automatically

### With Kustomize
- Agents follow base/overlay pattern
- Generate valid Kustomize patches
- Respect repository structure

### With Helm
- Agents use OCIRepository (not HelmRepository)
- Follow HelmRelease conventions
- Apply repository-wide defaults

## Support

- **Repository Guide**: See [/CLAUDE.md](../../CLAUDE.md)
- **Documentation**: See [/docs](../../docs/)
- **Task Automation**: See [/.taskfiles](../../.taskfiles/)
- **FluxCD Docs**: https://fluxcd.io

## Contributing

Improvements welcome:

1. Enhance existing agent workflows
2. Add new agents for common tasks
3. Improve examples and documentation
4. Share success stories and patterns

Submit PRs with agent updates or create issues for new agent ideas.
