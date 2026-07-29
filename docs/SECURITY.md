# Security Policy

## Table of contents

- [Supported Versions](#supported-versions)
- [Reporting a Vulnerability](#reporting-a-vulnerability)
- [Scope and attack surface](#scope-and-attack-surface)
- [Threat model and mitigations](#threat-model-and-mitigations)
- [Preferred Languages](#preferred-languages)
- [Contact](#contact)
- [Release security checklist (12.4.1)](#release-security-checklist-1241)

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Reporting a Vulnerability

We take the security of Phone Input Bundle seriously. If you believe you have found a security vulnerability, please report it to us as described below.

### How to Report

**Please do not report security vulnerabilities through public GitHub issues.**

Instead, please send an email to: **hectorfranco@nowo.tech**

Include the following information in your report:

- Type of issue (e.g., buffer overflow, SQL injection, cross-site scripting, etc.)
- Full paths of source file(s) related to the issue
- The location of the affected source code (tag/branch/commit or direct URL)
- Any special configuration required to reproduce the issue
- Step-by-step instructions to reproduce the issue
- Proof-of-concept or exploit code (if possible)
- Impact of the issue, including how an attacker might exploit it

### Response Timeline

- **Initial Response**: Within 48 hours
- **Status Update**: Within 7 days
- **Resolution**: Varies depending on complexity

### Disclosure Policy

- We will confirm receipt of your vulnerability report
- We will work with you to understand and validate the issue
- We will develop and release a fix as quickly as possible
- We will publicly acknowledge your responsible disclosure (if desired)

## Scope and attack surface

This bundle provides:

- one Symfony form type (`PhoneType`) with optional country prefix selector
- data transformers / `PhoneNumber` value object
- phone validator (catalog patterns and optional `libphonenumber`)
- Twig form themes and a `CountryFlagRenderer` (CSS icons, emoji, or UX Icons)
- static CSS under `src/Resources/public/css/` (flag icons + widget styles)

There are **no HTTP controllers**, no API endpoints, and no persistence layer in this bundle.

## Threat model and mitigations

- **Input validation**
  - National numbers are normalized and validated server-side (`PhoneValidator`, optional `ValidPhoneNumber` constraint).
  - Empty values are treated as valid at the phone-validator layer (requiredness is a host-app / Symfony `NotBlank` concern).
- **XSS / HTML injection**
  - Country ISO codes and emoji flags rendered by `CountryFlagRenderer` are passed through `htmlspecialchars`.
  - Twig form themes render standard form widgets; untrusted HTML is not injected by the bundle.
- **Authentication / authorization**
  - Not handled by this bundle (must be enforced by the host application where needed).
- **Secrets**
  - No bundle feature requires hardcoded secrets.
  - Repository policy: keep `.env` and local credentials untracked.
- **Optional dependencies**
  - `giggsey/libphonenumber-for-php` and `symfony/ux-icons` are optional; absence is handled with catalog fallbacks / CSS flags.

## Preferred Languages

We prefer all communications to be in English or Spanish.

## Contact

- **Maintainer**: [Héctor Franco Aceituno](https://github.com/HecFranco)
- **Organization**: [nowo-tech](https://github.com/nowo-tech)

## Release security checklist (12.4.1)

Before tagging a release, confirm:

| Item | Notes |
|------|--------|
| **SECURITY.md** | This document is current and linked from the README where applicable. |
| **`.gitignore` and `.env`** | `.env` and local env files are ignored; no committed secrets. |
| **No secrets in repo** | No API keys, passwords, or tokens in tracked files. |
| **Recipe / Flex** | Default recipe or installer templates do not ship production secrets. |
| **Input / output** | Phone normalization/validation preserved; flag HTML escaped in `CountryFlagRenderer`. |
| **Dependencies** | `composer audit` run; issues triaged. |
| **Logging** | Logs do not print secrets, tokens, or session identifiers unnecessarily. |
| **Cryptography** | If used: keys from secure config; never hardcoded. |
| **Permissions / exposure** | No bundle routes; host app roles apply to forms that embed `PhoneType`. |
| **Limits / DoS** | Host app should rate-limit abusive form submissions where applicable. |

Record confirmation in the release PR or tag notes.
