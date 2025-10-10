# Better BOSS README

This repo houses a Nuxt 4 frontend and a Laravel backend, developed and shipped with Docker. This document explains how to get set up, how we branch and release, and what “done” looks like for a feature.

## Table of Contents

1. [Architecture](#architecture)
2. [Local Setup](#setup)
3. [GIT](#git)
   - [Environments to Branches](#environments-to-branches) 
   - [High-Level Workflow](#high-level-workflow)
   - [Commit Messages](#commit-messages)
   - [Branching](#branching)
   - [Pull Requests](#pull-requests)
   - [Issues](#issues)
   - [Hot Fix Branches](#hot-fix-branches)
   - [Merge Strategy](#merge-strategy)
   - [Branch Deletions](#branch-deletions)
   - [Security Considerations](#security-considerations)
4. [Repo Layout](#repo-layout)

## Architecture

- **Frontend**: Nuxt 4 + TypeScript + Pinia + PrimeVue + Tailwind
- **Backend**: Laravel (PHP 8.2), Postgres, Redis, Queues
- **Runtime**: Docker Compose dev stack
- **CI/CD**: GitHub Actions (lint, typecheck, tests, build). Protected branches: `production`, `dev`.

---

## Local Setup

Copy `.env.example` in the root as `.env`. These are environment variables for the docker container. Copy `backend/.env.example` as `backend/.env` as laravel requires environment variables in its root. I am still working on a workaround for this.

Run `docker-compose up -d` in the project root to build your docker containers. This process will fail without `.env`.

## GIT
This section outlines the Git workflow and coding standards used. These rules are in place to ensure code quality, traceability, and team collaboration.

### Environments to Branches

* Production ⇄ `production`
    * `production` is perpetually green and deployable.
    * Only approved changes land in `production`.
* UAT ⇄ `dev`
    * `dev` is a staging area for integration and user acceptance testing.
    * `dev` is deployable to UAT at any time.

Rule of thumb: If it’s on `production`, it’s production‑ready. If it’s on `dev`, it’s UAT‑ready.

### High-Level Workflow
1. Branch from `production` for all work (featuers, bugfixes, chores).
2. Open a PR to `dev` and complete UAT.
3. After UAT approval, open a PR form `dev` (or the specific branch) to `production` at our convenience (release when ready).
4. Deploy:
* `dev` → UAT
* `production` → Production

This ensures we can ship to production without dragging along unapproved work.

### Commit Messages
All commit messages must be:

* Clear, concise, and written in imperative tense (e.g., "Fix bug", not "Fixed" or "Fixes").
* Descriptive enough to explain *why* a change was made, not just *what* was changed.
* Structured as:

```
#[ISSUE_NUMBER] Short summary of the change

Longer explanation if needed:
- What was the problem?
- What was the solution?
- Any implications (e.g., breaking changes, refactoring)?
```

**Examples:**

```bash
#123 Fix incorrect total in invoice calculation

Corrected rounding logic when summing item subtotals. 
Impacts payment gateway integration.
```

### Branching
* All feature and bugfix branches must branch **from `production`**.
* The naming convention for branches:

  ```
  [ISSUE_NUMBER]-short-descriptive-name
  ```

  **Examples:**

    * `Issue456-add-user-profile`
    * `Issue789-fix-invoice-calculation`

* Every branch **must be linked to a tracked issue** in the project board.
* **Hotfix branches** are the exception and are based on `production` ([see below](#hot-fix-branches)).

### Pull Requests
* **Target**: All development branches must be merged into `dev` via PR.
* **Restrictions**:

    * PRs into `dev` or `production` must be **reviewed and approved** by someone other than the branch author.
    * Direct commits to `dev` or `production` are prohibited.
* **PR Naming Convention**:

  ```
  #[ISSUE_NUMBER] – Summary of the feature or fix
  ```
* **PR Description Template**:

  ```markdown
  ## Issue
  Closes #[ISSUE_NUMBER]

  ## Description
  Brief overview of the changes and reasoning.

  ## Impact
  List affected areas or modules.

  ## Testing
  Describe testing steps or link to automated test results.
  ```

#### Why Branch from Master, PR to Dev

* Clean start: Branching from `production` means no unapproved code.
* UAT separation: `dev` holds only reviewed work ready for testing.
* Controlled releases: We choose when to promote `dev → production`.
* Hotfix safety: Urgent fixes land in `production` first, then flow to `dev` without conflict.
* Minimizes cherry picking.

### Issues
* **Every branch must relate to a GitHub/GitLab issue**.
* Issues should:

    * Clearly describe the expected outcome or problem.
    * Include acceptance criteria.
    * Be labeled (e.g., `bug`, `enhancement`, `hotfix`, `documentation`).

### Hot Fix Branches

* Created **from `production`** only.
* Used only for **critical production issues** affecting primary business operations.
* Must be prefixed with `HTF-`:

  ```
  HTF-#[ISSUE_NUMBER]-short-description
  ```

  **Example:** `HTF-#1001-fix-auth-crash`
* PR flow:

    1. Branch from `production`
    2. Fix and PR back into `production`
    3. Merge `production` back into `dev` to synchronize changes

#### Pull Request Expectations
For PRs into `dev` (UAT):
* CI green (build, unit/integration tests, lint, type checks)
* Linked issue/ticket
* Release notes entry (brief)
* DB migration plan + rollback plan
* Feature flag noted (if applicable)
* Screenshots or curl/Postman examples for API changes
* Associate the Issue with the PR

For PRs into `production` (Production):
* UAT sign‑off recorded
* No open criticals
* Changelog/Release notes ready
* Associate the Issue with the PR

Review etiquette:
* Review within one business day.
* Use suggestion mode for minor fixes; request changes for correctness.
* Author merges after approvals.

### Merge Strategy
* **Squash & Merge** for PRs into `dev` and `production`, to maintain a clean and linear history.
* **No merge commits** in history unless resolving long-lived branches (e.g., hotfix sync).

### Branch Deletions
Branches should be deleted after the associated issue is closed and the change has been merged into `production` (Production). This keeps the repository clean and avoids confusion.

Why this timing?
* Ensures the final, shipped code is traceable to the branch until production is live.
* Avoids losing context if a rollback/revert is needed before or during release.

Guidelines:
* Delete feature/bugfix branches once merged to `dev → production` and deployed.
* Delete hotfix branches after merge and back-sync.
* Release branches may be deleted after production promotion.
* Long-lived branches are discouraged; keep branches short-lived.

#### Restoring a deleted branch
* If tagged on release, recreate from the tag: `git checkout -b Issue123-fix v2.4.0`.
* Otherwise, recreate from the merge base or last commit hash (available in PR history): `git checkout -b Issue123 <commit-sha>`.

### Security Considerations
* **Secrets and credentials must never be committed** (use `.env` or secret managers).
* Use `.gitignore` rigorously.
* Sensitive branches (e.g., `production`, `production`) should be protected in the repository settings:
    * Require PR reviews
    * Block force pushes
    * Enforce status checks before merging

## Repo Layout

```
/
├─ backend/ # Laravel app
├─ frontend/ # Nuxt app
├─ nginx/ # Web app
├─ docs/ # architecture, decisions (ADR), API contracts
├─ docker-compose.yaml/ # Single entrypoint for docker containers
└─ README.md
```
