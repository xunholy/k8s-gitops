#!/bin/bash
# Block secret-related bash commands from Claude Code agents
# Exit code 0 = allow, Exit code 2 = block

# The tool input is provided via stdin as JSON
# Parse the command from the JSON input
INPUT=$(cat)
COMMAND=$(echo "$INPUT" | jq -r '.tool_input.command // empty' 2>/dev/null)

# If jq parsing fails, try to get raw input
if [ -z "$COMMAND" ]; then
  COMMAND="$INPUT"
fi

# Patterns to block - protect secrets and private keys
BLOCKED_PATTERNS=(
  # Age key access
  "cat.*keys\.txt"
  "cat.*/age/"
  "cat.*\.age"
  # SOPS decryption
  "sops\s+-d"
  "sops\s+--decrypt"
  "sops\s+.*\.enc\."
  # GPG secret key operations
  "gpg\s+--export-secret"
  "gpg\s+--list-secret"
  "gpg\s+-K"
  "gpg\s+--armor.*secret"
  # Direct secret file access
  "cat.*\.enc\.yaml"
  "cat.*secret.*\.yaml"
  "head.*\.enc\.yaml"
  "tail.*\.enc\.yaml"
  "less.*\.enc\.yaml"
  "more.*\.enc\.yaml"
  # Base64 decoding of secrets
  "base64.*-d.*secret"
  # Kubectl secret extraction
  "kubectl.*get.*secret.*-o"
  "kubectl.*describe.*secret"
)

# Check if command matches any blocked pattern
for pattern in "${BLOCKED_PATTERNS[@]}"; do
  if echo "$COMMAND" | grep -qiE "$pattern"; then
    echo "BLOCKED: Command matches restricted pattern" >&2
    echo "Pattern: $pattern" >&2
    echo "This command could expose secrets and has been blocked." >&2
    exit 2
  fi
done

# Command is allowed
exit 0
