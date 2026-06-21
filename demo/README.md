# Phone Input Bundle - Demo

Three demo projects (Symfony 6.4, 7.4, 8.1) showing **all `PhoneType` format combinations**:

| # | Field | `value_format` | `country_prefix_selector` |
|---|-------|----------------|---------------------------|
| 1 | `phone_concatenated_with_prefix` | `CONCATENATED` | `true` |
| 2 | `phone_concatenated_without_prefix` | `CONCATENATED` | `false` |
| 3 | `phone_separated_with_prefix` | `SEPARATED` | `true` |
| 4 | `phone_separated_without_prefix` | `SEPARATED` | `false` |
| 5 | `phone_object_with_prefix` | `OBJECT` | `true` |
| 6 | `phone_object_without_prefix` | `OBJECT` | `false` |

After submit, the page displays the **PHP type** and **serialized value** for each field.

On first load every field is **pre-filled** with sample phone numbers (see `App\Form\DemoFormData`).

Use the **CSS framework** dropdown at the top to switch between Bootstrap 5, Tailwind CSS 2, Foundation 6 and Symfony default. The choice is kept in the URL (`?framework=…`) and applies `container_classes` / input classes to every field.

### Prefix display modes (`PrefixDisplay`)

| Field | Constant |
|-------|----------|
| `phone_prefix_display_full` | `PrefixDisplay::FULL` |
| `phone_prefix_display_prefix_only` | `PrefixDisplay::PREFIX_ONLY` |
| `phone_prefix_display_flag_and_prefix` | `PrefixDisplay::FLAG_AND_PREFIX` |
| `phone_prefix_display_iso_and_prefix` | `PrefixDisplay::ISO_AND_PREFIX` |
| `phone_prefix_display_flag_only` | `PrefixDisplay::FLAG_ONLY` |

### Flag display modes (`FlagDisplay`)

| Field | Constant |
|-------|----------|
| `phone_flag_display_emoji` | `FlagDisplay::EMOJI` |
| `phone_flag_display_css_icon` | `FlagDisplay::CSS_ICON` |
| `phone_flag_display_ux_icon` | `FlagDisplay::UX_ICON` |
| `phone_flag_display_none` | `FlagDisplay::NONE` |

## Quick start

```bash
cd demo
make up-symfony8
make install-symfony8
# http://localhost:8003 (see demo/symfony8/.env PORT)
```

## Demos

- **symfony6** — PHP 8.1+, Symfony 6.4
- **symfony7** — PHP 8.2+, Symfony 7.4
- **symfony8** — PHP 8.2+, Symfony 8.1

Each demo is independent (own `docker-compose.yml`, FrankenPHP, tests).

## Commands

```bash
make up symfony8
make install symfony8
make test symfony8
make down symfony8
```

See `make help` for all targets.
