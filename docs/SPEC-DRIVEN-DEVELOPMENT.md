# Spec-driven development

**PhoneInputBundle** documents product behaviour here and proves it with PHPUnit and PHPStan.

## User stories

| ID | Story |
| --- | --- |
| US-01 | As an integrator, I want a phone form type with optional country prefix selector so users pick dial codes with flags. |
| US-02 | As an integrator, I want configurable value formats (E.164 string, array, VO) so my domain layer receives the shape it expects. |
| US-03 | As an integrator, I want country/prefix validation so invalid national numbers are rejected before submit. |
| US-04 | As a maintainer, I want automated tests and CI so regressions are caught on every change. |

## In scope

- `PhoneType`, configuration, Twig widget, bundled country catalog and CSS assets
- Documented integration in `docs/INSTALLATION.md`, `docs/CONFIGURATION.md`, `docs/USAGE.md`

## Out of scope

- `demo/` applications (illustrative only)
- Guarantees outside documented public API

## Validation

- `make release-check` or `composer qa` + `composer test-coverage`
- GitHub Actions CI (`.github/workflows/ci.yml`)

## Requirement identifiers

| ID | Where | What |
| --- | --- | --- |
| REQ-DOC-001 | Root `Dockerfile`, `docker-compose.yml` | Dev container for PHPUnit and QA |
| REQ-DEMO-002 | `docs/DEMO-FRANKENPHP.md` | FrankenPHP demo documentation |
| REQ-TEST-003 | CI coverage job | PHP coverage >= 95% |

See [docs/ENGRAM.md](ENGRAM.md) for Engram / MCP integration.
