<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$columns = json_decode('{{ALL_COLUMNS_JSON}}', true);
$rows = db()->query('SELECT * FROM {{TABLE_SQL}}');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="List {{TABLE_HTML}} rows">
    <title>{{TABLE_HTML}}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>{{TABLE_HTML}} <a class="btn btn-primary" href="new.php">Create new row</a></h1>

            <div class="table-responsive">
                <table class="table table-condensed table-striped">
                    <thead>
                    <tr>
                        <th><span class="sr-only">Actions</span></th>
                        <?php foreach ($columns as $column): ?>
                            <th><?= e($column) ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="text-nowrap">
                                <a class="btn btn-sm btn-primary" href="edit.php?id=<?= urlencode((string) $row['id']) ?>">Edit</a>
                                <form action="delete.php" method="post" style="display: inline"
                                      onsubmit="return confirm('Delete this row?');">
                                    <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                    <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                            <?php foreach ($columns as $column): ?>
                                <td><?= e($row[$column] ?? '') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
