<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Page Description">
    <meta name="author" content="Author">
    <title>Delete</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <?php
            require_once(__DIR__ . '/config.php');
            if (isset($_REQUEST['id'])) {
                $id = (int)$_REQUEST['id'];
                $sql = "DELETE FROM {{TABLE}} WHERE `id` = $id LIMIT 1;";
                $result = mySqlQuery($sql);
                if ($result) {
                    echo '<h1>Deleted!</h1>';
                    echo '<a role="button" class="btn btn-primary" href="list.php">Back To Listing</a>';
                } else {
                    echo '<h1>Error!</h1>';
                    echo '<pre>';
                    var_dump($result);
                    var_dump($sql);
                    echo '</pre>';
                    echo '<a role="button" class="btn btn-primary" href="list.php">Back To Listing</a>';
                }
            }
            ?>

        </div>
    </div>

</div>
</body>
</html>