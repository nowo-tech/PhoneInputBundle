# Release process

1. Update [CHANGELOG.md](CHANGELOG.md): move entries from `[Unreleased]` to a new `[X.Y.Z] - YYYY-MM-DD` section. (This project does not store version in `composer.json`; Packagist uses the git tag.)
2. Update [UPGRADING.md](UPGRADING.md) if the release has upgrade notes.
3. Run pre-release checks: `make release-check` (cs-fix, cs-check, rector-dry, phpstan, test-coverage, demo healthchecks).
4. Commit all changes, create an annotated tag (e.g. `v1.0.0`), and push branch and tag. The release workflow creates the GitHub Release from the tag and changelog.
5. Publish on Packagist (usually automatic when the tag is pushed and the package is registered).

## Example for v1.0.0 (first release)

```bash
git add -A
git status   # review
git commit -m "Release 1.0.0: first stable Phone Input Bundle"
git tag -a v1.0.0 -m "Release 1.0.0

First stable release: PhoneType with country prefix selector, validation, and Symfony 6/7/8 demos."
git push origin main
git push origin v1.0.0
```

## Security checklist

Before tagging, complete [SECURITY.md](SECURITY.md#release-security-checklist-1241) (no secrets, dependencies reviewed).
