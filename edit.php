<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Page Description">
    <meta name="author" content="Author">
    <title>New</title>

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
            <h1>Edit</h1>
        </div>
    </div>

    <div class="row clearfix"></div>

    <div class="row">
        <div class="col-md-12">
            <?php
            require_once(__DIR__ . '/config.php');

            if (isset($_REQUEST['id'])) {
                $id = (int)$_REQUEST['id'];
                if (isset($_POST['submitted'])) {
                    $insertHolder = array();
                    foreach ($_POST AS $key => $value) {
                        $_POST[$key] = mysql_escape_mimic($value);
                        if ($key === 'id') {
                            $id = (int)$value;
                            $insertHolder[] = "`" . $key . "` = '{$id}'";
                        }
                        if ($key !== 'submitted') {
                            $insertHolder[] = "`" . $key . "` = '{$value}'";
                        }
                    }


                    if (!empty($insertHolder)) {
                        $valueString = join(", ", $insertHolder);
                        $sql = "UPDATE {{TABLE}} SET $valueString WHERE `id` = $id LIMIT 1;";
                        $result = mySqlQuery($sql);
                        if ($result !== false) {
                            echo '<div class="col-md-12 text-center">';
                            echo "<h3>Edited row!</h3><br />";
                            echo '<a role="button" class="btn btn-primary" href="list.php">Back To Listing</a>';
                            echo '<br /></div>';
                        } else {
                            echo '<div class="col-md-12 text-center">';
                            echo "<h3>Failed!</h3><br />";
                            echo '<pre>';
                            var_dump($result);
                            var_dump($sql);
                            echo '</pre>';
                            echo '<br /></div>';
                        }

                    }
                }
            }
            ?>

            <form class="form-horizontal" action='' method='POST'>

                <?php
                if (!empty($id)) {
                    getHeaders($id);
                }
                function getHeaders($id)
                {
                    $sql = "SELECT * FROM {{TABLE}} WHERE `id` = '$id' LIMIT 1;";
                    $result = mySqlQuery($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $headers = array_keys($row);

                            if (!empty($headers)) {
                                for ($i = 0; $i < count($headers); $i++) {
                                    $label = $headers[$i];
                                    echo '<div class="form-group">';
                                    echo '<label class="col-md-4 control-label" for="' . $label . '">' . $label . '</label>';

                                    echo '<div class="col-md-4">';
                                    echo '<input id="' . $label . '" name="' . $label . '" type="text" class="form-control input-md" value="' . htmlspecialchars(stripslashes($row[$label])) . '">';
                                    echo '</div></div>';
                                }
                            }
                        }
                    }
                }


                ?>
                <div class="form-group text-center">
                    <div class="col-md-4"></div>
                    <div class="col-md-4 text-center">
                        <button role="button" class="btn btn-md btn-primary" type='submit'>Edit Row</button>
                        <input type='hidden' value='1' name='submitted'/>
                    </div>
                </div>
            </form>

        </div>
    </div>

</div>
</body>
</html>