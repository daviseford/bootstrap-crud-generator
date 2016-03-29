<?php
/**
 * Created by PhpStorm.
 * User: Davis
 * Date: 3/29/2016
 * Time: 5:53 PM
 * @param $tableName
 * @param $arrayOfColumns
 */

function download_ListPHP($tableName, $arrayOfColumns)
{
    $file = 'list.php';

    $file_contents = file_get_contents($file);

    //open file and get data
    $data = file_get_contents("list.php");

    // do replacements
    $data = str_replace("{{TABLE}}", $tableName, $data);
    $data = str_replace("{{COLUMNS}}", join(',', $arrayOfColumns), $data);


    //save it back:
    file_put_contents("make/list.php", $data);
    $newFile = "make/list.php";
    if (file_exists($newFile)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($newFile) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($newFile));
        readfile($newFile);
        exit;
    }
}