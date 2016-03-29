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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12 table-responsive">

            <table class="table table-hover">
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
                            echo '<tr>';
                            $keys = array_keys($row);
                            $keys[0];
                            for ($i = 0; $i < count($keys); $i++) {
                                echo '<td>' . $row[$keys[$i]] . '</td>';
                            }
                            echo "<td><a role='button' class='btn btn-sm btn-primary' href=edit.php?" . $keys[0] . "={$row[$keys[0]]}>Edit</a></td>";
                            echo "<td><a role='button' class='btn btn-sm btn-danger' href=delete.php?" . $keys[0] . "={$row[$keys[0]]}>Delete</a></td>";
                            echo '</tr>';
                        }
                    }
                }

                getTD();

                ?>

                </tbody>
            </table>
        </div>
        <a href=new.php>New Row</a>
    </div>

</div>
</body>
</html>