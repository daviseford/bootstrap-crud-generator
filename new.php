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
        <div class="col-md-12">
            <?php
            require_once(__DIR__ . '/config.php');
            if (isset($_POST['submitted'])) {
                foreach ($_POST AS $key => $value) {
                    $_POST[$key] = mysql_real_escape_string($value);
                }
                $sql = "INSERT INTO `generator_Ads` 
              ( `adName` ,  `adGroup` ,  `adURL` ,  `adDescription` ,  `template` ,  `imageURL` ,  
              `imageWidth` ,  `imageHeight` ,  `enabled` ,  `highlight` ,  `isInline` ,  `inlineHeader` ,  
              `inlineText`  ) VALUES(  '{$_POST['adName']}' ,  '{$_POST['adGroup']}' ,  '{$_POST['adURL']}' ,  '{$_POST['adDescription']}' ,  '{$_POST['template']}' ,  '{$_POST['imageURL']}' ,  '{$_POST['imageWidth']}' ,  '{$_POST['imageHeight']}' ,  '{$_POST['enabled']}' ,  '{$_POST['highlight']}' ,  '{$_POST['isInline']}' ,  '{$_POST['inlineHeader']}' ,  '{$_POST['inlineText']}'  ) ";
                mysql_query($sql) or die(mysql_error());
                echo "Added row.<br />";
                echo "<a href='list.php'>Back To Listing</a>";
            }
            ?>

            <form class="form-control" action='' method='POST'>
                <p><b>AdName:</b><br/><textarea name='adName'></textarea>
                <p><b>AdGroup:</b><br/><input type='text' name='adGroup'/>
                <p><b>AdURL:</b><br/><textarea name='adURL'></textarea>
                <p><b>AdDescription:</b><br/><textarea name='adDescription'></textarea>
                <p><b>Template:</b><br/><input type='text' name='template'/>
                <p><b>ImageURL:</b><br/><input type='text' name='imageURL'/>
                <p><b>ImageWidth:</b><br/><input type='text' name='imageWidth'/>
                <p><b>ImageHeight:</b><br/><input type='text' name='imageHeight'/>
                <p><b>Enabled:</b><br/><input type='text' name='enabled'/>
                <p><b>Highlight:</b><br/><input type='text' name='highlight'/>
                <p><b>IsInline:</b><br/><input type='text' name='isInline'/>
                <p><b>InlineHeader:</b><br/><textarea name='inlineHeader'></textarea>
                <p><b>InlineText:</b><br/><textarea name='inlineText'></textarea>
                <p><input type='submit' value='Add Row'/><input type='hidden' value='1' name='submitted'/>
            </form>

        </div>
    </div>

</div>
</body>
</html>