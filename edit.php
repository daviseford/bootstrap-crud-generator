<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$columns = json_decode('{{EDITABLE_COLUMNS_JSON}}', true);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$message = null;
$error = null;
$row = null;

if ($id !== false && $id !== null) {
    try {
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            if ($columns !== []) {
                $assignments = array_map(static fn (string $column): string => '`' . $column . '` = ?', $columns);
                $values = array_map(static fn (string $column): string => (string) ($_POST[$column] ?? ''), $columns);
                $values[] = $id;
                $sql = 'UPDATE {{TABLE_SQL}} SET ' . implode(', ', $assignments) . ' WHERE `id` = ?';
                db()->prepare($sql)->execute($values);
            }
            $message = 'Saved row!';
        }

        $statement = db()->prepare('SELECT * FROM {{TABLE_SQL}} WHERE `id` = ? LIMIT 1');
        $statement->execute([$id]);
        $row = $statement->fetch();
        if ($row === false) {
            $error = 'No row exists with that id.';
        }
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
} else {
    http_response_code(400);
    $error = 'A valid numeric id is required.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Edit a {{TABLE_HTML}} row">
    <title>Edit {{TABLE_HTML}}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Edit {{TABLE_HTML}} row</h1>

            <?php if ($message !== null): ?>
                <div class="alert alert-success"><?= e($message) ?></div>
            <?php endif; ?>
            <?php if ($error !== null): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <?php if (is_array($row)): ?>
                <form class="form-horizontal" method="post" accept-charset="UTF-8">
                    <?php foreach ($columns as $column): ?>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="<?= e($column) ?>"><?= e($column) ?></label>
                            <div class="col-md-4">
                                <input class="form-control input-md" id="<?= e($column) ?>" name="<?= e($column) ?>"
                                       type="text" value="<?= e($row[$column] ?? '') ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-4">
                            <button class="btn btn-primary" type="submit">Save row</button>
                            <a class="btn btn-default" href="list.php">Back to listing</a>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <a class="btn btn-default" href="list.php">Back to listing</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
