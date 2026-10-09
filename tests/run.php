<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use BootstrapCrudGenerator\ApplicationGenerator;
use BootstrapCrudGenerator\SchemaParser;

$tests = [];

$tests['parses CREATE TABLE column comments and UTF-8 text'] = function (): void {
    $sql = <<<'SQL'
CREATE TABLE `contacts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Clé primaire',
  `display_name` varchar(255) NOT NULL COMMENT 'Nom affiché – français',
  `city` varchar(100) DEFAULT NULL COMMENT 'Lieu préféré',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Répertoire';
SQL;

    $schema = (new SchemaParser())->parse($sql);

    assertSame('contacts', $schema['table']);
    assertSame(['id', 'display_name', 'city'], $schema['columns']);
};

$tests['parses a large CREATE TABLE statement'] = function (): void {
    $columns = ['`id` bigint unsigned NOT NULL AUTO_INCREMENT'];
    for ($i = 1; $i <= 300; $i++) {
        $columns[] = sprintf('`field_%03d` varchar(255) DEFAULT NULL', $i);
    }
    $columns[] = 'PRIMARY KEY (`id`)';

    $sql = "CREATE TABLE `large_table` (\n  " . implode(",\n  ", $columns)
        . "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $schema = (new SchemaParser())->parse($sql);

    assertSame('large_table', $schema['table']);
    assertSame(301, count($schema['columns']));
    assertSame('field_300', $schema['columns'][300]);
};

$tests['keeps SELECT and UPDATE input compatibility'] = function (): void {
    $parser = new SchemaParser();

    assertSame(
        ['table' => 'contacts', 'columns' => ['id', 'display_name']],
        $parser->parse('SELECT `id`, `display_name` FROM `contacts`;')
    );
    assertSame(
        ['table' => 'contacts', 'columns' => ['id', 'display_name']],
        $parser->parse("UPDATE `contacts` SET `id` = 1, `display_name` = 'Élodie' WHERE `id` = 2;")
    );
};

$tests['uses only UPDATE assignment targets as columns'] = function (): void {
    $schema = (new SchemaParser())->parse(
        "UPDATE contacts SET display_name = CONCAT(first_name, last_name), id = 1 WHERE id = 2;"
    );

    assertSame(['display_name', 'id'], $schema['columns']);
};

$tests['rejects multiple statements but allows quoted semicolons'] = function (): void {
    $parser = new SchemaParser();

    assertThrows(
        static fn () => $parser->parse('SELECT id FROM contacts; DROP TABLE users;'),
        InvalidArgumentException::class
    );

    $schema = $parser->parse(
        "CREATE TABLE notes (id int NOT NULL COMMENT 'primary; identifier', body text, PRIMARY KEY (id));"
    );
    assertSame(['id', 'body'], $schema['columns']);
};

$tests['rejects SELECT expressions instead of silently dropping them'] = function (): void {
    assertThrows(
        static fn () => (new SchemaParser())->parse(
            'SELECT id, CONCAT(first_name, last_name) AS display_name FROM contacts;'
        ),
        InvalidArgumentException::class
    );
};

$tests['renders generated CRUD files without legacy form bugs'] = function (): void {
    $files = (new ApplicationGenerator(dirname(__DIR__)))->render([
        'table' => 'contacts',
        'columns' => ['id', 'display_name', 'city'],
    ]);

    assertContains("$" . "columns = json_decode('[\"display_name\",\"city\"]', true);", $files['new.php']);
    assertNotContains('join("\', \'", $_POST)', $files['new.php']);
    assertNotContains('<?\n', str_replace("\r\n", "\n", $files['list.php']));
    assertContains('method="post"', strtolower($files['list.php']));
    assertNotContains('fetchAll()', $files['list.php']);
    assertContains('INPUT_POST', $files['delete.php']);
    assertNotContains('$' . '_REQUEST', $files['delete.php']);
    assertContains("$" . "columns = json_decode('[\"display_name\",\"city\"]', true);", $files['edit.php']);
    assertNotContains('{{', implode("\n", $files));
};

$tests['uses POST submission and utf8mb4 connections'] = function (): void {
    $root = dirname(__DIR__);
    $index = file_get_contents($root . '/index.php');
    $config = file_get_contents($root . '/config.php');
    $parse = file_get_contents($root . '/parse.php');

    assertContains('method="post"', strtolower($index));
    assertContains("charset=utf8mb4", $config);
    assertContains("REQUEST_METHOD", $parse);
    assertNotContains('$_REQUEST', $parse);
};

$tests['serves only known PHP files from the web root'] = function (): void {
    // `php -S` serves the whole checkout, so any stray PHP script becomes a public endpoint.
    $root = dirname(__DIR__);
    $files = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
        static fn (SplFileInfo $file): bool => !in_array($file->getFilename(), ['.git', 'vendor'], true)
    ));

    $found = [];
    foreach ($files as $file) {
        if ($file->getExtension() === 'php') {
            $found[] = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
        }
    }
    sort($found);

    assertSame([
        'config.php',
        'delete.php',
        'edit.php',
        'index.php',
        'list.php',
        'new.php',
        'parse.php',
        'src/ApplicationGenerator.php',
        'src/SchemaParser.php',
        'tests/run.php',
    ], $found);
};

$tests['builds a complete zip archive'] = function (): void {
    $archivePath = (new ApplicationGenerator(dirname(__DIR__)))->buildArchive([
        'table' => 'contacts',
        'columns' => ['id', 'display_name'],
    ]);

    try {
        $archive = new ZipArchive();
        assertSame(true, $archive->open($archivePath));
        assertSame(6, $archive->numFiles);
        assertContains('utf8mb4', (string) $archive->getFromName('config.php'));
        $archive->close();
    } finally {
        if (is_file($archivePath)) {
            unlink($archivePath);
        }
    }
};

$tests['removes a partial archive when rendering fails'] = function (): void {
    $before = glob(sys_get_temp_dir() . '/bootstrap-crud-*') ?: [];

    set_error_handler(static function (int $severity, string $message): void {
        throw new ErrorException($message, 0, $severity);
    });
    try {
        assertThrows(
            static fn () => (new ApplicationGenerator('/path/that/does/not/exist'))->buildArchive([
                'table' => 'contacts',
                'columns' => ['id'],
            ]),
            Throwable::class
        );
    } finally {
        restore_error_handler();
    }

    $after = glob(sys_get_temp_dir() . '/bootstrap-crud-*') ?: [];
    sort($before);
    sort($after);
    assertSame($before, $after);
};

$failures = 0;
foreach ($tests as $name => $test) {
    try {
        $test();
        echo "PASS: {$name}\n";
    } catch (Throwable $error) {
        $failures++;
        fwrite(STDERR, "FAIL: {$name}\n  {$error->getMessage()}\n");
    }
}

if ($failures > 0) {
    fwrite(STDERR, "\n{$failures} test(s) failed.\n");
    exit(1);
}

echo "\nAll tests passed.\n";

function assertSame($expected, $actual): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            'Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true)
        );
    }
}

function assertContains(string $needle, string $haystack): void
{
    if (strpos($haystack, $needle) === false) {
        throw new RuntimeException('Expected content to contain ' . var_export($needle, true));
    }
}

function assertNotContains(string $needle, string $haystack): void
{
    if (strpos($haystack, $needle) !== false) {
        throw new RuntimeException('Expected content not to contain ' . var_export($needle, true));
    }
}

function assertThrows(callable $callback, string $expectedClass): void
{
    try {
        $callback();
    } catch (Throwable $error) {
        if ($error instanceof $expectedClass) {
            return;
        }

        throw new RuntimeException(
            'Expected ' . $expectedClass . ', got ' . get_class($error) . ': ' . $error->getMessage()
        );
    }

    throw new RuntimeException('Expected ' . $expectedClass . ' to be thrown.');
}
