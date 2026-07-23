<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$message = null;
$error = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    $error = 'Delete requests must use POST.';
} else {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id === false || $id === null) {
        http_response_code(400);
        $error = 'A valid numeric id is required.';
    } else {
        try {
            $statement = db()->prepare('DELETE FROM {{TABLE_SQL}} WHERE `id` = ? LIMIT 1');
            $statement->execute([$id]);
            $message = $statement->rowCount() > 0 ? 'Deleted row.' : 'The row did not exist.';
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Delete a {{TABLE_HTML}} row">
    <title>Delete {{TABLE_HTML}}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <?php if ($message !== null): ?>
                <h1><?= e($message) ?></h1>
            <?php else: ?>
                <div class="alert alert-danger">Could not delete the row: <?= e($error) ?></div>
            <?php endif; ?>
            <a class="btn btn-primary" href="list.php">Back to listing</a>
        </div>
    </div>
</div>
</body>
</html>
