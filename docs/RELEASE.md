# Release process

> Current release target: **1.2.1** (`v1.2.1`).

1. Update [CHANGELOG.md](CHANGELOG.md): move entries from `[Unreleased]` to a new `[X.Y.Z] - YYYY-MM-DD` section. (This project does not store version in `composer.json`; Packagist uses the git tag.)
2. Update [UPGRADING.md](UPGRADING.md) if the release has upgrade notes.
3. Run pre-release checks: `make release-check` (includes `check-no-cursor-coauthor`, cs-fix, cs-check, rector-dry, phpstan, test-coverage, and optionally demo healthchecks).
4. Commit all changes, create an annotated tag (e.g. `v1.1.5`), and push branch and tag. The release workflow creates the GitHub Release from the tag and changelog.
5. Publish on Packagist (usually automatic when the tag is pushed and the package is registered).

## Example for v1.2.1

```bash
git add -A
git status   # review
git commit -m "Release 1.2.1: restore CI coverage gate to 100%"
git tag -a v1.2.1 -m "Release 1.2.1

Restore PHPUnit coverage to 100% (CI elements gate)."
git push origin main
git push origin v1.2.1
```

## Example for v1.1.5

```bash
git add -A
git status   # review
git commit -m "Release 1.1.5: named assets package, FrankenPHP demos, PHPStan rulesets"
git tag -a v1.1.5 -m "Release 1.1.5

Named asset package nowo_phone_input, FrankenPHP PHP 8.5 demos,
PHPStan FrankenPHP rulesets, and security threat model."
git push origin main
git push origin v1.1.5
```

## Security checklist

Before tagging, complete [SECURITY.md](SECURITY.md#release-security-checklist-1241) (no secrets, dependencies reviewed).

After creating the release commit and tag, run `make check-no-cursor-coauthor` again **before** `git push` (REQ-GIT-001).
