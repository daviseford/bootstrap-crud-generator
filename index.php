<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Bootstrap 3 CRUD Generator">
    <meta name="author" content="Davis E. Ford">
    <title>Bootstrap CRUD Generator</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!--        <script src="js/bootstrap.min.js"></script>-->

</head>


<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <form id="sqlEntry" action="parse.php">
                <!-- Textarea -->
                <div class="form-group">
                    <div class="col-md-5">
                        <label class="control-label" for="textarea">Submit sample "SELECT" or "CREATE" SQL here. It will
                            be
                            parsed into a basic bootstrap CRUD application.</label>
                        <h4 class="text-center">Sample Submission</h4>
                <pre>----- Your PRIMARY Key must be named `id` -----
    ----- CREATE Example -----
    CREATE TABLE `sampleTable` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `sampleName` text NOT NULL,
        `sampleGroup` varchar(45) NOT NULL DEFAULT '',
        `sampleURL` text NOT NULL
        PRIMARY KEY (`id`),
        UNIQUE KEY `id_UNIQUE` (`id`)
    )   ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ----- UPDATE Example -----
    UPDATE `sampleSchema`.`sampleTable`
    SET
    `id` = 1,
    `adName` = 'Sample',
    `adGroup` = 'Sample',
    `adURL` = 'Sample',
    WHERE `id` = 1;
                </pre>
                    </div>
                    <div class="col-md-5">
                        <textarea class="form-control" id="textarea" name="textarea" rows="15"></textarea>
                    </div>
                </div>

                <input type="submit" class="btn btn-info" value="Create CRUD">
            </form>
        </div>
    </div>

</div>

</body>
</html>