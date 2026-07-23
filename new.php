<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$columns = json_decode('{{EDITABLE_COLUMNS_JSON}}', true);
$message = null;
$error = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    try {
        if ($columns === []) {
            db()->exec('INSERT INTO {{TABLE_SQL}} () VALUES ()');
        } else {
            $quotedColumns = array_map(static fn (string $column): string => '`' . $column . '`', $columns);
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $sql = 'INSERT INTO {{TABLE_SQL}} (' . implode(', ', $quotedColumns) . ') VALUES (' . $placeholders . ')';
            $values = array_map(static fn (string $column): string => (string) ($_POST[$column] ?? ''), $columns);
            db()->prepare($sql)->execute($values);
        }

        $message = 'Added row!';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Create a {{TABLE_HTML}} row">
    <title>New {{TABLE_HTML}} row</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>New {{TABLE_HTML}} row</h1>

            <?php if ($message !== null): ?>
                <div class="alert alert-success"><?= e($message) ?></div>
            <?php elseif ($error !== null): ?>
                <div class="alert alert-danger">Could not add the row: <?= e($error) ?></div>
            <?php endif; ?>

            <form class="form-horizontal" method="post" accept-charset="UTF-8">
                <?php foreach ($columns as $column): ?>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="<?= e($column) ?>"><?= e($column) ?></label>
                        <div class="col-md-4">
                            <input class="form-control input-md" id="<?= e($column) ?>" name="<?= e($column) ?>"
                                   type="text" value="<?= e($_POST[$column] ?? '') ?>">
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="form-group">
                    <div class="col-md-offset-4 col-md-4">
                        <button class="btn btn-primary" type="submit">Add row</button>
                        <a class="btn btn-default" href="list.php">Back to listing</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
