# AGENTS.md

## Repository purpose

This repository is a small PHP web tool that parses one MySQL statement and downloads a generated Bootstrap CRUD
application. Preserve that narrow purpose. Prefer focused fixes over introducing a framework or build system.

## Setup and verification

Install dependencies with `composer install` and run all tests with `composer test`.

When PHP is unavailable on the host, use the Composer Docker image:

```sh
docker run --rm -v "$PWD:/app" -w /app composer:2 install
docker run --rm -v "$PWD:/app" -w /app composer:2 php tests/run.php
docker run --rm -v "$PWD:/app" -w /app composer:2 sh -lc \
  "find . \( -path ./vendor -o -path ./php/PHP-SQL-Parser \) -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l"
```

Run the focused regression suite after every behavior change and lint every project PHP file before handing off.
Tests must not require a live MySQL server.

## Architecture and conventions

- `index.php` accepts input; `parse.php` owns HTTP validation and the ZIP response.
- `SchemaParser` is the only place that interprets parser output. Keep table/column identifier validation there.
- `ApplicationGenerator` renders files in memory and builds archives with `ZipArchive`. Do not restore shell-based
  `mkdir`, `zip`, or cleanup commands.
- The root CRUD PHP files are templates, not the running generator. A change to generated behavior usually belongs
  in those templates and needs a render assertion in `tests/run.php`.
- Every placeholder must use the `{{NAME}}` form and must be replaced by `ApplicationGenerator::render()`.
- Generated database values must use prepared statements. Generated HTML values must pass through `e()`.
- Maintain PHP 7.4 compatibility unless the documented minimum is intentionally changed in the same change set.
- Keep source and documentation encoded as UTF-8.

## Behavioral contracts

- Input is a single MySQL `CREATE TABLE`, `SELECT`, or `UPDATE` statement with explicit columns.
- Every generated table must have an `id` column; other columns are editable text fields.
- Generator submissions and generated mutations use `POST`.
- Database connections use `utf8mb4`.
- SQL identifiers are validated before being embedded in generated templates.
- Temporary archives are removed after responses and tests.

## Dependency and generated-file hygiene

- Manage `greenlion/php-sql-parser` through the root `composer.json` and `composer.lock`.
- Never edit `vendor/` or copy dependency source into `php/`.
- Do not commit generated ZIP files, temporary `make/` content, or credentials.
- Do not put real database credentials into `config.php`; it is shipped as a template.

## Documentation expectations

Update `README.md` when requirements, supported SQL, setup, or security boundaries change. Update
`templates/README.md` when generated-app setup or behavior changes. Keep the two documents distinct: the root README
is for this generator; the template README is for people receiving a generated application.
