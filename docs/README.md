# Documentation

## Claude AI Agents for GitOps

Specialized Claude sub-agents designed for Kubernetes and DevOps/Platform engineers working with FluxCD GitOps repositories.

### Quick Start

Claude agents are located in `.claude/agents/` and can be referenced directly in conversations with Claude Code:

```
"As the [Agent Name] agent, [your task]"
```

See [.claude/agents/README.md](../.claude/agents/README.md) for complete usage guide.

### Agent Catalog

| Agent | Purpose | File |
|-------|---------|------|
| **Flux Troubleshooter** | Diagnose FluxCD issues | [flux-troubleshooter.md](../.claude/agents/flux-troubleshooter.md) |
| **GitOps Deployer** | Deploy new applications | [gitops-deployer.md](../.claude/agents/gitops-deployer.md) |
| **Security Auditor** | Security review | [security-auditor.md](../.claude/agents/security-auditor.md) |
| **Dependency Mapper** | Map Flux dependencies | [dependency-mapper.md](../.claude/agents/dependency-mapper.md) |
| **Resource Optimizer** | Optimize resources | [resource-optimizer.md](../.claude/agents/resource-optimizer.md) |

### Documentation Files

- **[.claude/agents/](../.claude/agents/)** - Claude agent definitions
- **[CLAUDE.md](../CLAUDE.md)** - Repository guidance for Claude Code

### Example Workflows

#### Deploy New Application
```
As the GitOps Deployer agent:

Deploy Grafana Tempo to the observability namespace using
the official Grafana OCI Helm chart with distributed mode.
```

Then validate:
```
As the Security Auditor agent:

Audit the newly created Grafana Tempo manifests in
kubernetes/apps/base/observability/tempo/
```

#### Troubleshoot Failing Resource
```
As the Flux Troubleshooter agent:

The prometheus HelmRelease in the observability namespace
is stuck in "Upgrade Failed". Diagnose the root cause.
```

Then check dependencies:
```
As the Dependency Mapper agent:

Map dependencies for the prometheus HelmRelease to verify
all dependencies are healthy.
```

#### Security Review
```
As the Security Auditor agent:

Perform a comprehensive security audit of all manifests
in the network-system namespace, focusing on secret
management and pod security standards.
```

### Using with Claude Code

Reference agents by name in your Claude Code conversations:

```
"As the [Agent Name] agent, [specific task]"
```

The agent will apply its specialized knowledge, workflows, and repository context to help you accomplish the task.

### Advanced Usage

#### Chain Multiple Agents

For complex tasks, work with multiple agents in sequence:

**Complete Application Deployment:**
1. GitOps Deployer → Create manifests
2. Security Auditor → Security review
3. Dependency Mapper → Verify dependencies
4. Flux Troubleshooter → Confirm reconciliation

**Incident Response:**
1. Flux Troubleshooter → Diagnose issue
2. Dependency Mapper → Check dependencies
3. Resource Optimizer → Check resource exhaustion

### Tips

1. **Be Specific**: Provide clear context (namespace, app name, requirements)
2. **Iterate**: Review output, refine with follow-up prompts
3. **Validate**: Always validate agent-generated manifests before applying
4. **Learn**: Study agent outputs to understand GitOps patterns
5. **Customize**: Fork and modify agent prompts for your needs

### Contributing

To improve or add agents:

1. Create or edit agent files in `.claude/agents/`
2. Follow the agent template in [.claude/agents/README.md](../.claude/agents/README.md)
3. Test agents with real repository tasks
4. Update documentation
5. Submit PR with improvements

### Support

- **Agent Documentation**: See [.claude/agents/](../.claude/agents/)
- **Repository Guide**: See [CLAUDE.md](../CLAUDE.md)
- **FluxCD Docs**: https://fluxcd.io

---

## Other Documentation

- **Architecture**: See [CLAUDE.md](../CLAUDE.md) for repository architecture
- **Bootstrap**: See `.taskfiles/bootstrap/` for cluster setup
- **Security**: See `security-system/` applications for policy enforcement
- **Monitoring**: See `observability/` for monitoring stack
