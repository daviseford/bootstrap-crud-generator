<?php

declare(strict_types=1);

use BootstrapCrudGenerator\ApplicationGenerator;
use BootstrapCrudGenerator\SchemaParser;

require __DIR__ . '/vendor/autoload.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('This endpoint accepts POST requests only.');
}

$sql = trim((string) ($_POST['textarea'] ?? ''));
if ($sql === '') {
    http_response_code(400);
    exit('The MySQL statement is empty.');
}

$archivePath = null;

try {
    $schema = (new SchemaParser())->parse($sql);
    $archivePath = (new ApplicationGenerator(__DIR__))->buildArchive($schema);

    header('Content-Description: File Transfer');
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="bootstrap-crud-' . $schema['table'] . '.zip"');
    header('Content-Length: ' . filesize($archivePath));
    header('Cache-Control: no-store');
    readfile($archivePath);
} catch (Throwable $error) {
    http_response_code(400);
    echo 'Could not generate the application: ';
    echo htmlspecialchars($error->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
} finally {
    if ($archivePath !== null && is_file($archivePath)) {
        unlink($archivePath);
    }
}
