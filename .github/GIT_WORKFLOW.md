# Git Workflow for Laravel Domo

## Branch Naming Convention

```
<type>/<description>

Examples:
- feat/web-dashboard
- fix/schema-null-pointer
- docs/readme-update
- test/ai-driver-tests
- refactor/service-extraction
```

## Commit Message Format

This project uses [Conventional Commits](https://www.conventionalcommits.org/).

### Structure
```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Types
- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks
- `ci`: CI/CD configuration
- `perf`: Performance improvements
- `build`: Build system changes

### Scope Examples
- `commands` - Artisan commands
- `services` - Service classes
- `http` - Controllers and routes
- `tests` - Test files
- `config` - Configuration files
- `docs` - Documentation
- `deps` - Dependencies

### Examples
```bash
# Feature
feat(commands): add domo:serve command for web dashboard
feat(mcp): implement schema listing endpoint

# Bug Fix
fix(schema): resolve null pointer in table analysis
fix(ai): handle empty API response gracefully

# Documentation
docs(readme): update installation instructions
docs(contributing): add testing guidelines

# Tests
test(unit): add tests for AI driver interface
test(feature): verify dashboard route registration

# Refactor
refactor(services): extract schema analysis to separate class
refactor(contracts): rename interface methods for clarity

# Chore
chore(deps): update laravel/prompts to ^0.3
chore(ci): add PHP 8.4 to test matrix
```

## Git Configuration

### Setup (run once)
```bash
# Configure commit template
git config commit.template .gitmessage

# Configure editor
git config core.editor "code --wait"

# Configure default branch
git config init.defaultBranch main

# Configure aliases
git config alias.st status
git config alias.br branch
git config alias.ci commit
git config alias.lg "log --oneline --graph --decorate"
```

### Verify Configuration
```bash
git config --local --list
```

## Workflow

### Starting Work
```bash
# Update main branch
git checkout main
git pull origin main

# Create feature branch
git checkout -b feat/your-feature-name
```

### During Work
```bash
# Check status
git st

# Stage changes
git add <files>

# Commit with template
git commit

# View recent commits
git lg
```

### Before Push
```bash
# Run tests
make test

# Check code style
make lint

# Run static analysis
make phpstan

# Interactive rebase to clean up commits
git rebase -i main
```

### Push and PR
```bash
# Push branch
git push -u origin feat/your-feature-name

# Create PR (use GitHub CLI or web interface)
gh pr create --title "feat: your feature" --body "Description"
```

### After Merge
```bash
# Delete local branch
git branch -d feat/your-feature-name

# Delete remote branch
git push origin --delete feat/your-feature-name

# Update main
git checkout main
git pull origin main
```

## Useful Aliases

```bash
# See all aliases
git config --get-regexp alias

# Custom aliases configured
git st          # status
git br          # branch
git lg          # log with graph
git ll          # log with detailed format
git last        # last commit
git undo        # undo last commit (soft reset)
git amend       # amend last commit
git unstage     # unstage file
git ignore      # add to .gitignore and commit
git who         # show author of last commit
```

## Release Process

```bash
# Update CHANGELOG.md
# Update version in composer.json

# Create tag
git tag -a v0.1.0 -m "Release v0.1.0"

# Push tag
git push origin v0.1.0

# GitHub Actions will create release automatically
```

## Troubleshooting

### Undo Last Commit
```bash
git undo  # Keeps changes staged
git reset --hard HEAD~1  # Discards changes
```

### Fix Commit Message
```bash
git commit --amend -m "correct message"
```

### Squash Commits
```bash
git rebase -i HEAD~3  # Squash last 3 commits
```

### Recover Lost Commit
```bash
git reflog  # Find commit hash
git checkout -b recovery-branch <hash>
```
