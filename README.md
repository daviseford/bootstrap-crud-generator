# Bootstrap CRUD Generator

Bootstrap CRUD Generator turns a MySQL `CREATE TABLE`, `SELECT`, or `UPDATE` statement into a downloadable,
standalone PHP CRUD application. The generated app contains Bootstrap 3 pages for creating, listing, editing, and
deleting rows.

This is a small scaffolding tool, not an ORM or a production admin framework. It assumes that the table has an
integer primary key named `id` and treats every other column as a text field.

## Requirements

- PHP 7.4 or newer
- Composer
- PHP extensions: PDO, PDO MySQL, and Zip

The generated app has the same PHP/PDO requirements but does not require Composer.

## Install and run

```sh
composer install
php -S 127.0.0.1:8000
```

Open <http://127.0.0.1:8000>, paste a supported MySQL statement, and select **Create CRUD Application**. The form
submits the SQL with `POST`, so large table definitions are not constrained by URL length limits.

If PHP and Composer are not installed locally, the test suite can run with Docker:

```sh
docker run --rm -v "$PWD:/app" -w /app composer:2 install
docker run --rm -v "$PWD:/app" -w /app composer:2 php tests/run.php
```

In PowerShell, use `${PWD}` in the volume argument.

## Supported input

The parser accepts:

- `CREATE TABLE` statements, including column and table `COMMENT` clauses
- `SELECT` statements with explicit columns
- `UPDATE` statements with explicit `SET` columns

All inputs must identify one table and include an `id` column. `SELECT *`, joins, expressions masquerading as
columns, multiple statements, and nonstandard identifiers are intentionally rejected because the generator needs a
safe, unambiguous list of writable columns.

Example:

```sql
CREATE TABLE `contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Clé primaire',
  `display_name` varchar(255) NOT NULL COMMENT 'Nom affiché',
  `city` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Use the generated application

1. Extract the downloaded ZIP into a web-accessible directory.
2. Edit `$host`, `$database`, `$username`, and `$password` in its `config.php`.
3. Ensure the PHP PDO MySQL extension is enabled.
4. Open `list.php`.

Connections use `utf8mb4`, values use prepared statements, HTML output is escaped as UTF-8, and deletion requires a
confirmed `POST`. For consistent accented characters and emoji, the source database/table/columns should also use
`utf8mb4`.

## Security and limitations

Generated code is starter scaffolding. Before exposing it outside a trusted development environment, add:

- authentication and per-table authorization;
- CSRF tokens for all mutations;
- application-specific validation and field types;
- pagination for large datasets;
- production-safe error logging instead of displaying database errors.

The generator validates SQL identifiers and never executes the submitted schema statement. It only parses that
statement and places validated table/column names into the templates.

## Development

Run the regression suite:

```sh
composer test
```

The suite covers large schemas, SQL comments, UTF-8 input, `SELECT`/`UPDATE` compatibility, template rendering, and
ZIP generation. It also fails if a PHP file outside the known entry points, templates, `src/`, and `tests/` appears,
because `php -S` would serve it. It does not need a database.

The two historical issue reports addressed by the current implementation are [large tables submitted through GET](https://github.com/daviseford/bootstrap-crud-generator/issues/3)
and [column comments/UTF-8 data](https://github.com/daviseford/bootstrap-crud-generator/issues/1).

## Project layout

- `index.php` and `parse.php` are the generator web entry points.
- `src/SchemaParser.php` extracts and validates the table/column contract.
- `src/ApplicationGenerator.php` renders templates and builds the ZIP.
- `new.php`, `list.php`, `edit.php`, `delete.php`, and `config.php` are source templates for generated apps.
- `templates/README.md` is included in each generated ZIP.
- `tests/run.php` is the dependency-light regression test runner.
