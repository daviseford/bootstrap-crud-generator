<?php

declare(strict_types=1);

namespace BootstrapCrudGenerator;

use InvalidArgumentException;
use PHPSQLParser\PHPSQLParser;

final class SchemaParser
{
    /**
     * @return array{table: string, columns: string[]}
     */
    public function parse(string $sql): array
    {
        $sql = trim($sql);
        if ($sql === '') {
            throw new InvalidArgumentException('The MySQL statement is empty.');
        }

        $this->assertSingleStatement($sql);
        $parsed = (new PHPSQLParser($sql, true))->parsed;
        $table = '';
        $columns = [];

        if (!empty($parsed['TABLE']) && is_array($parsed['TABLE'])) {
            $table = $this->identifierFromNode($parsed['TABLE'], true);
            $columns = $this->createColumns($parsed['TABLE']['create-def'] ?? []);
        } elseif (!empty($parsed['UPDATE'][0]) && is_array($parsed['UPDATE'][0])) {
            $table = $this->identifierFromNode($parsed['UPDATE'][0], true);
            $columns = $this->updateColumns($parsed['SET'] ?? []);
        } elseif (!empty($parsed['FROM'][0]) && is_array($parsed['FROM'][0])) {
            $table = $this->identifierFromNode($parsed['FROM'][0], true);
            foreach ($parsed['SELECT'] ?? [] as $entry) {
                if (!is_array($entry) || ($entry['expr_type'] ?? '') !== 'colref') {
                    throw new InvalidArgumentException('SELECT statements must use explicit column names.');
                }

                $columns[] = $this->identifierFromNode($entry, false);
            }
        }

        $columns = array_values(array_unique(array_filter($columns)));
        if ($table === '' || $columns === []) {
            throw new InvalidArgumentException(
                'Could not find a table and explicit columns in that statement.'
            );
        }

        if (!in_array('id', $columns, true)) {
            throw new InvalidArgumentException('The statement must include an `id` column.');
        }

        return ['table' => $table, 'columns' => $columns];
    }

    /**
     * @param mixed[] $createDefinition
     * @return string[]
     */
    private function createColumns(array $createDefinition): array
    {
        $columns = [];
        foreach ($createDefinition['sub_tree'] ?? [] as $definition) {
            if (!is_array($definition) || ($definition['expr_type'] ?? '') !== 'column-def') {
                continue;
            }

            foreach ($definition['sub_tree'] ?? [] as $part) {
                if (is_array($part) && ($part['expr_type'] ?? '') === 'colref') {
                    $columns[] = $this->identifierFromNode($part, false);
                    break;
                }
            }
        }

        return $columns;
    }

    /**
     * @param mixed[] $nodes
     * @return string[]
     */
    private function updateColumns(array $nodes): array
    {
        $columns = [];
        foreach ($nodes as $node) {
            if (!is_array($node) || empty($node['sub_tree']) || !is_array($node['sub_tree'])) {
                continue;
            }

            foreach ($node['sub_tree'] as $part) {
                if (is_array($part) && ($part['expr_type'] ?? '') === 'colref') {
                    $columns[] = $this->identifierFromNode($part, false);
                    break;
                }
            }
        }

        return $columns;
    }

    private function assertSingleStatement(string $sql): void
    {
        $quote = null;
        $inLineComment = false;
        $inBlockComment = false;
        $terminated = false;
        $length = strlen($sql);

        for ($index = 0; $index < $length; $index++) {
            $character = $sql[$index];
            $next = $index + 1 < $length ? $sql[$index + 1] : '';

            if ($inLineComment) {
                if ($character === "\n" || $character === "\r") {
                    $inLineComment = false;
                }
                continue;
            }

            if ($inBlockComment) {
                if ($character === '*' && $next === '/') {
                    $inBlockComment = false;
                    $index++;
                }
                continue;
            }

            if ($quote !== null) {
                if ($character === '\\') {
                    $index++;
                    continue;
                }
                if ($character === $quote) {
                    if ($next === $quote) {
                        $index++;
                    } else {
                        $quote = null;
                    }
                }
                continue;
            }

            if ($character === '#') {
                $inLineComment = true;
                continue;
            }
            if ($character === '-' && $next === '-' && ($index + 2 >= $length || ctype_space($sql[$index + 2]))) {
                $inLineComment = true;
                $index++;
                continue;
            }
            if ($character === '/' && $next === '*') {
                $inBlockComment = true;
                $index++;
                continue;
            }
            if (ctype_space($character)) {
                continue;
            }
            if ($terminated) {
                throw new InvalidArgumentException('Only one MySQL statement may be submitted.');
            }
            if ($character === "'" || $character === '"' || $character === '`') {
                $quote = $character;
                continue;
            }
            if ($character === ';') {
                $terminated = true;
            }
        }
    }

    /**
     * @param mixed[] $node
     */
    private function identifierFromNode(array $node, bool $allowQualified): string
    {
        if (!empty($node['no_quotes']['parts']) && is_array($node['no_quotes']['parts'])) {
            $parts = $node['no_quotes']['parts'];
        } else {
            $raw = (string) ($node['table'] ?? $node['name'] ?? $node['base_expr'] ?? '');
            $parts = preg_split('/\s*\.\s*/', trim($raw)) ?: [];
            $parts = array_map(static function (string $part): string {
                return trim($part, "` \t\n\r\0\x0B");
            }, $parts);
        }

        if (!$allowQualified && count($parts) > 1) {
            $parts = [end($parts)];
        }

        if ($parts === [] || (!$allowQualified && count($parts) !== 1)) {
            throw new InvalidArgumentException('Unsupported SQL identifier.');
        }

        foreach ($parts as $part) {
            if (!is_string($part) || !preg_match('/^[A-Za-z_][A-Za-z0-9_$]*$/', $part)) {
                throw new InvalidArgumentException('Unsupported SQL identifier: ' . (string) $part);
            }
        }

        return implode('.', $parts);
    }
}
