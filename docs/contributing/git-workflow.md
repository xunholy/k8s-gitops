# Git Workflow (Fork)

Simple steps to keep your fork in sync with the upstream repo and to work on changes safely.

## Daily sync
- Ensure remotes: `origin` (your fork), `upstream` (source). Verify with `git remote -v`.
- Update your local `master` from upstream’s `main`: `task git:sync` (or run the commands listed in the Task below manually).
- If conflicts appear during the rebase, resolve, `git add` the files, and continue with `git rebase --continue`. If you want to bail out, run `git rebase --abort`.

## Feature work
- Branch off the refreshed `master`: `git checkout -b feature/<short-name>`.
- Commit normally. Rebase the feature branch on `master` before pushing or opening a PR: `git fetch upstream && git checkout master && git rebase upstream/main && git checkout feature/<short-name> && git rebase master`.
- Push feature branches to your fork: `git push --force-with-lease origin feature/<short-name>` after rebasing.
- Open PRs from feature branches. Avoid committing directly to `master`.

## Quick commands
- Sync fork: `task git:sync`
- Start work: `git checkout master && git checkout -b feature/<short-name>`
- Refresh feature: `git checkout master && task git:sync && git checkout feature/<short-name> && git rebase master`
