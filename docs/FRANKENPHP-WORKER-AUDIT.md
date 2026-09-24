# FrankenPHP worker mode audit (kernel not reset between requests)

| Field | Value |
|-------|-------|
| Package | `nowo-tech/phone-input-bundle` (`symfony-bundle`) |
| Audited revision | `v1.4.1` / 2026-09-24 |
| Audit date | 2026-09-24 |
| Target runtime | FrankenPHP **worker** with **`FRANKENPHP_RESET_KERNEL=false`** (sticky Kernel / “Friendly Worker”) |
| Method | Manual review of every PHP file under `src/` (form type, data transformer, validator, Twig extension/renderer, country and pattern catalogs, DI extension, compiler pass, `Resources/config/services.yaml`) + PHPStan classic + worker-strict |
| **Verdict** | ✅ **100% compatible** under Scenario B (`reset_kernel: false`) |

## Execution model assumed

FrankenPHP worker mode boots the Symfony kernel once per worker and serves many requests with the same container. This audit assumes the **strict** host contract used by Nowo “Friendly Worker” / kernel-isolation E2E:

| Host flag | Meaning |
|-----------|---------|
| `FRANKENPHP_MODE=worker` | Worker keeps the app in memory |
| **`FRANKENPHP_RESET_KERNEL=false`** | Kernel is **not** rebooted; Scenario **B** below |
| `FRANKENPHP_WORKER_NUM=1` | Single worker (isolation tests) |

Two scenarios are evaluated:

- **A — kernel not rebooted, `services_resetter` still runs:** services tagged `kernel.reset` (or implementing `ResetInterface`) are reset between requests.
- **B — no reset at all (`FRANKENPHP_RESET_KERNEL=false`):** nothing is reset; any per-request state kept in a shared service leaks into the next request.

A bundle that is safe under **B** is safe under **A** and under classic mode / PHP-FPM.

## Summary

| Area | Status | Notes |
|------|--------|-------|
| Mutable state in shared services | ✅ | Catalog caches on `CountryProvider` / `PhonePatternCatalog` are request-independent (bundled JSON); config and form defaults are `readonly` |
| Static properties / `static` locals | ✅ | None; only static closures and `Country::fromArray()` |
| `ResetInterface` / `kernel.reset` coverage | ✅ N/A | Nothing request-scoped to reset |
| Request / user / locale captured in services | ✅ | Per-form options passed as arguments; never stored on shared services |
| Superglobals, `$_ENV`, `putenv`, `ini_set`, `setlocale`, timezone | ✅ | None used; config compiled into `nowo_phone_input.*` parameters |
| Doctrine / EntityManager | ✅ N/A | No persistence |
| Output, headers, `exit`, shutdown functions | ✅ | None |
| Resources (files, sockets, cURL) held open | ✅ | JSON files read once with `file_get_contents()`; no handles kept |
| Memory growth across requests | ✅ | Catalog caches fill once and stay bounded |
| Blocking I/O and timeouts | ⚠️ Low | Optional `UX_ICON` may fetch missing icons via Symfony UX Icons (see W-01) |
| Third-party static state | ✅ | Optional `libphonenumber\PhoneNumberUtil::getInstance()` metadata cache only |
| Array default aliasing on shared form type | ✅ | CSS class defaults are copied (`[...$arr]`) so OptionsResolver never shares mutable arrays with the service |
| PHPStan FrankenPHP rulesets | ✅ | `ruleset-classic.neon` + `ruleset-worker-strict.neon` in `phpstan.neon.dist` |

Worker demo: `demo/symfony8/docker/frankenphp/Caddyfile` declares a `worker` block (`file /app/public/index.php`, `watch`).

## Services reviewed

| Service | Shared | Mutable state | Scenario A | Scenario B |
|---------|--------|---------------|------------|------------|
| `Country\CountryProvider` | yes | Lazy caches of `countries.json` (immutable, request-independent); `readonly` config lists | ✅ | ✅ |
| `Phone\PhonePatternCatalog` | yes | Lazy caches of `phone_patterns.json` (immutable, request-independent) | ✅ | ✅ |
| `Phone\E164Parser` | yes | none (`readonly` provider) | ✅ | ✅ |
| `Phone\PhoneValidator` | yes | none (`readonly` dependencies) | ✅ | ✅ |
| `Phone\LibPhoneNumberChecker` (optional) | yes | none (`readonly` util) | ✅ | ✅ |
| `libphonenumber\PhoneNumberUtil` (optional DI) | yes | library metadata cache only | ✅ | ✅ |
| `IconSupport\IconSupportChecker` | yes | none (`readonly` flags) | ✅ | ✅ |
| `Twig\CountryFlagExtension` / `CountryFlagRenderer` | yes | none (`readonly` dependencies) | ✅ | ✅ |
| `Form\Type\PhoneType` | yes (`form.type`) | `readonly` `$defaults`; array class defaults copied per `configureOptions()` | ✅ | ✅ |
| `Validator\Constraints\ValidPhoneNumberValidator` | yes | only Symfony `$context`, re-initialized per validation | ✅ | ✅ |

`PhoneNumberTransformer` is created per form build and is `readonly`. `Country`, `PhoneNumber` and `PhonePattern` are `final readonly` value objects.

## Findings

### W-01 — `UX_ICON` flag rendering may trigger HTTP fetches through Symfony UX Icons (Low)

- **Where:** `CountryFlagRenderer` → `IconRendererInterface::renderIcon()` when `flag_display` is `UX_ICON`.
- **Worker impact:** a missing Iconify icon may block the worker on HTTP client timeout; exceptions fall back to CSS flags (no state leak). Default `CSS_ICON` does no I/O.
- **Recommendation:** lock icons locally (`bin/console ux:icons:lock`) or keep `flag_display: CSS_ICON` in production.

### W-02 — Lazily-loaded catalog caches are intentional and safe (Info)

- **Where:** `CountryProvider::getRawCountries()`, `PhonePatternCatalog::load()`.
- **Worker impact:** first request per worker decodes bundled JSON; later requests reuse in-memory objects. Data never depends on request/user/locale.
- **Recommendation:** none. Deploying a new bundle version requires restarting workers (normal FrankenPHP deploy behaviour).

### Closed / hardening notes (2026-09-24)

| ID | Topic | Action |
|----|--------|--------|
| H-01 | Shared array CSS class defaults could be aliased into OptionsResolver | Defensive copy in `PhoneType::configureOptions()` |
| H-02 | `PhoneType::$defaults` / `CountryProvider` config lists were mutable properties | Marked `readonly` |
| H-03 | PHPStan worker hygiene | Use `ruleset-worker-strict.neon` |
| H-04 | Regression for sticky Kernel | `PhoneTypeTest::testSharedInstanceDoesNotLeakOptionsAcrossConsecutiveBuilds` |

## Usage recommendations in worker mode

- No reset hook or special bundle YAML is needed when `FRANKENPHP_RESET_KERNEL=false`.
- Prefer `flag_display: CSS_ICON` or locally locked UX icons (W-01).
- Per-form options (`allowed_countries`, `preferred_countries`, `default_country`) are safe to vary per request; do not move them into a shared service property in custom subclasses.
- Custom `NationalPhoneNumberChecker` implementations must stay stateless (or implement `ResetInterface`).
- Keeping Symfony’s `services_resetter` enabled in the **application** remains recommended for framework-owned state; this bundle does not depend on it.

## Re-audit triggers

Re-run this audit when a change adds: request/locale-dependent properties to shared services; translated country names cached per locale; runtime loading of country/pattern data from a URL or database; an event listener; or any use of `$_SERVER` / `$_ENV` at runtime.
