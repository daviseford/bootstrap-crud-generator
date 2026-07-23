# Generated Bootstrap CRUD application

This application provides basic create, list, edit, and delete pages for `{{TABLE_HTML}}`.

## Requirements

- PHP 7.4 or newer
- The PDO MySQL extension
- A MySQL/MariaDB table with an integer primary key named `id`

## Setup

1. Copy all generated files into a web-accessible directory.
2. Edit the four database settings in `config.php`.
3. Confirm that the database and table use `utf8mb4` (recommended).
4. Open `list.php` in your browser.

The generated code uses prepared statements for values and configures the database connection for `utf8mb4`.
It is intentionally small scaffolding, not a complete production admin system. Add authentication, authorization,
CSRF protection, validation, and application-specific field types before exposing it to untrusted users.
