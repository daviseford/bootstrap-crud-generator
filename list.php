<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Page Description">
    <meta name="author" content="Author">
    <title>List</title>

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
            <h1>{{TABLE}}   <a role="button" class="btn btn-md btn-primary" href='new.php'>Create New Row</a></h1>
        </div>
    </div>

    <div class="row clearfix"></div>

    <div class="row">
        <div class="col-md-12 table-responsive">

            <table class="table table-condensed">
                <thead>
                <tr>
                    <?
                    require_once(__DIR__ . '/config.php');
                    function getHeadersForList()
                    {
                        $sql = "SELECT * FROM {{TABLE}} LIMIT 1;";
                        $result = mySqlQuery($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $headers = array_keys($row);
                            }
                        }
                        if (!empty($headers)) {
                            echo '<th></th>';   // These two are blank for the Edit/Delete buttons
                            echo '<th></th>';
                            for ($i = 0; $i < count($headers); $i++) {
                                echo '<th>' . $headers[$i] . '</th>';
                            }
                        }
                    }

                    getHeadersForList();
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php
                function getTD()
                {
                    $sql = "SELECT * FROM {{TABLE}};";
                    $result = mySqlQuery($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $id = $row['id'];
                            echo '<tr>';
                            echo '<td><a role="button" class="btn btn-sm btn-primary" href="edit.php?id=' . $id . '">Edit</a></td>';
                            echo '<td><a role="button" class="btn btn-sm btn-danger" href="delete.php?id=' . $id . '">Delete</a></td>';
                            $keys = array_keys($row);
                            $keys[0];
                            for ($i = 0; $i < count($keys); $i++) {
                                echo '<td>' . htmlspecialchars(stripslashes($row[$keys[$i]])) . '</td>';
                            }
                            echo '</tr>';
                        }
                    }
                }

                getTD();

                ?>

                </tbody>
            </table>
        </div>

    </div>

</div>
</body>
</html>