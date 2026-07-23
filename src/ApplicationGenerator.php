<?php

declare(strict_types=1);

namespace BootstrapCrudGenerator;

use RuntimeException;
use ZipArchive;

final class ApplicationGenerator
{
    private string $projectRoot;

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = rtrim($projectRoot, '/\\');
    }

    /**
     * @param array{table: string, columns: string[]} $schema
     * @return array<string, string>
     */
    public function render(array $schema): array
    {
        $columns = array_values($schema['columns']);
        $editableColumns = array_values(array_filter(
            $columns,
            static fn (string $column): bool => $column !== 'id'
        ));

        $replacements = [
            '{{TABLE_SQL}}' => $this->quoteQualifiedIdentifier($schema['table']),
            '{{TABLE_HTML}}' => htmlspecialchars($schema['table'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            '{{ALL_COLUMNS_JSON}}' => $this->json($columns),
            '{{EDITABLE_COLUMNS_JSON}}' => $this->json($editableColumns),
        ];

        $files = [];
        foreach (['config.php', 'new.php', 'list.php', 'edit.php', 'delete.php'] as $filename) {
            $files[$filename] = $this->renderFile($filename, $replacements);
        }
        $files['README.md'] = $this->renderFile('templates/README.md', $replacements);

        return $files;
    }

    /**
     * @param array{table: string, columns: string[]} $schema
     */
    public function buildArchive(array $schema): string
    {
        if (!class_exists(ZipArchive::class)) {
            throw new RuntimeException('The PHP zip extension is required to create downloads.');
        }

        $files = $this->render($schema);
        $archivePath = tempnam(sys_get_temp_dir(), 'bootstrap-crud-');
        if ($archivePath === false) {
            throw new RuntimeException('Could not allocate a temporary archive.');
        }

        $archive = new ZipArchive();
        $isOpen = false;

        try {
            if ($archive->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('Could not create the download archive.');
            }
            $isOpen = true;

            foreach ($files as $filename => $contents) {
                if (!$archive->addFromString($filename, $contents)) {
                    throw new RuntimeException('Could not add ' . $filename . ' to the download archive.');
                }
            }

            if (!$archive->close()) {
                throw new RuntimeException('Could not finish the download archive.');
            }
            $isOpen = false;

            return $archivePath;
        } catch (\Throwable $error) {
            if ($isOpen) {
                $archive->close();
            }
            if (is_file($archivePath)) {
                unlink($archivePath);
            }

            throw $error;
        }
    }

    /**
     * @param array<string, string> $replacements
     */
    private function renderFile(string $relativePath, array $replacements): string
    {
        $path = $this->projectRoot . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException('Could not read template: ' . $relativePath);
        }

        return str_replace(array_keys($replacements), array_values($replacements), $contents);
    }

    /**
     * @param string[] $values
     */
    private function json(array $values): string
    {
        $json = json_encode($values, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new RuntimeException('Could not encode the template columns.');
        }

        return $json;
    }

    private function quoteQualifiedIdentifier(string $identifier): string
    {
        return implode('.', array_map(static function (string $part): string {
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_$]*$/', $part)) {
                throw new RuntimeException('Unsafe SQL identifier: ' . $part);
            }

            return '`' . $part . '`';
        }, explode('.', $identifier)));
    }
}
