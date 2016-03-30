<?php

namespace PHPSQLParser; // Google MySQL Parser
require_once dirname(__FILE__) . '/php/PHP-SQL-Parser/src/PHPSQLParser/PHPSQLParser.php';

$sql = $_REQUEST['textarea'];

$parsed = parsePOSTSql($sql);

if (!empty($parsed['table']) && !empty($parsed['columns'])) {
    download_ListPHP($parsed['table'], $parsed['columns']);
}


function download_ListPHP($tableName, $arrayOfColumns)
{
    $randomAsset = rand(0, 9999);

    // Make a temporary directory to store the files
    exec('mkdir make/' . $randomAsset);

    // Copy Readme
    $file = 'README.md';
    $data = file_get_contents($file);
    file_put_contents("make/" . $randomAsset . "/README.md", $data);

    // Copy Config
    $file = 'config.php';
    $data = file_get_contents($file);
    file_put_contents("make/" . $randomAsset . "/config.php", $data);

    // Update New
    $file = 'new.php';
    $data = file_get_contents($file);
    $data = str_replace("{{TABLE}}", $tableName, $data);
    $data = str_replace("{{COLUMNS}}", join(',', $arrayOfColumns), $data);
    // Save it back:
    file_put_contents("make/" . $randomAsset . "/new.php", $data);

    // Update Delete
    $file = 'delete.php';
    $data = file_get_contents($file);
    $data = str_replace("{{TABLE}}", $tableName, $data);
    // Save it back:
    file_put_contents("make/" . $randomAsset . "/delete.php", $data);

    // Update List
    $file = 'list.php';
    $data = file_get_contents($file);
    $data = str_replace("{{TABLE}}", $tableName, $data);
    $data = str_replace("{{COLUMNS}}", join(',', $arrayOfColumns), $data);
    // Save it back:
    file_put_contents("make/" . $randomAsset . "/list.php", $data);

    // Update Edit
    $file = 'edit.php';
    $data = file_get_contents($file);
    $data = str_replace("{{TABLE}}", $tableName, $data);
    $data = str_replace("{{COLUMNS}}", join(',', $arrayOfColumns), $data);
    // Save it back:
    file_put_contents("make/" . $randomAsset . "/edit.php", $data);

    // Create a semi-random filename
    $zipname = "make/" . $randomAsset . "/" . $randomAsset . "_bootstrap-crud-generator.zip";

    // Run a shell command to zip the files for us
    exec('zip -r ' . $zipname . ' make/' . $randomAsset);

    if (file_exists($zipname)) {
        ignore_user_abort(true);    // Delete the file even if the user aborts
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($zipname) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname); // Send the file to the user
        unlink($zipname);   // Delete the zip file once sent

        $dirName = 'make/' . $randomAsset;
        array_map('unlink', glob($dirName . '/*.*'));   //Remove the newly created .php files
        rmdir($dirName);    // Remove the directory
        exit;
    }
}


function runGoogleParserOnMySQL($sql)
{
    $parser = new PHPSQLParser($sql, true);
    $parsed = $parser->parsed;
    return $parsed;
}

function parsePOSTSql($sql)
{
    $response = array();
    if (!empty($sql)) {

        $parsed = runGoogleParserOnMySQL($sql);

        if (!empty($parsed)) {

            if (!empty($parsed['SELECT']) && is_array($parsed['SELECT'])) {
                $columnNames = parse_select($parsed['SELECT']);
            }

            if (!empty($parsed['UPDATE']) && is_array($parsed['UPDATE'])) {
                $tableName = parse_update_tableName($parsed['UPDATE']);
            }

            if (!empty($parsed['SET']) && is_array($parsed['SET'])) {
                $columnNames = parse_set_columnNames($parsed['SET']);
            }


            if (!empty($parsed['TABLE']) && is_array($parsed['TABLE'])) {

                if (!empty($parsed['TABLE']['create-def'])) {
                    $columnNames = parse_createdef($parsed['TABLE']['create-def']);
                }

                if (!empty($parsed['TABLE']['name'])) {
                    $tableName = $parsed['TABLE']['name'];
                }
            } else if (!empty($parsed['FROM'])) {
                if (!empty($parsed['FROM'][0])) {
                    if (!empty($parsed['FROM'][0]['table'])) {
                        $tableName = $parsed['FROM'][0]['table'];
                    }
                }
            }


            if (!empty($tableName)) {
                $response['table'] = $tableName;
//                echo $tableName;
            }
            if (!empty($columnNames)) {
                $response['columns'] = $columnNames;
//                echo '<pre>';
//                var_dump($columnNames);
//                echo '</pre>';
            }

        }

    }
    return $response;
}

/* Returns an array of column names */
function parse_update_tableName($updateArray)
{
    $response = '';
    if (!empty($updateArray)) {
        if (!empty($updateArray[0])) {
            if (!empty($updateArray[0]['expr_type']) && !empty($updateArray[0]['table'])) {
                if ($updateArray[0]['expr_type'] === 'table') {
                    $response = $updateArray[0]['table'];
                }
            }
        }
    }
    return $response;
}

/* Returns an array of column names */
function parse_set_columnNames($setArray)
{
    $response = array();
    if (!empty($setArray)) {
        for ($i = 0; $i < count($setArray); $i++) {
            $thisEntry = $setArray[$i];
            if (!empty($thisEntry['sub_tree'])) {
                for ($k = 0; $k < count($thisEntry['sub_tree']); $k++) {
                    $thisSubtree = $thisEntry['sub_tree'][$k];
                    if (is_array($thisSubtree) && !empty($thisSubtree['expr_type']) && !empty($thisSubtree['base_expr'])) {
                        if ($thisSubtree['expr_type'] === 'colref') {
                            $response[] = $thisSubtree['base_expr'];
                        }
                    }
                }
            }
        }
    }
    return $response;
}


/* Returns an array of column names */
function parse_select($selectArray)
{
    $response = array();
    if (!empty($selectArray)) {
        for ($i = 0; $i < count($selectArray); $i++) {
            $thisEntry = $selectArray[$i];
            if ($thisEntry['expr_type'] === 'colref') {
                $response[] = $thisEntry['base_expr'];
            }
        }
    }
    return $response;
}

/* Returns an array of column names */
function parse_createdef($createdef)
{
    $response = array();
    if (!empty($createdef['sub_tree'])) {
        for ($i = 0; $i < count($createdef['sub_tree']); $i++) {
            $thisSubtree = $createdef['sub_tree'][$i];
            if (is_array($thisSubtree['sub_tree'])) {
                if ($thisSubtree['sub_tree'][0]['expr_type'] === 'colref') {
                    $response[] = $thisSubtree['sub_tree'][0]['base_expr'];
                }
            }
        }
    }
    return $response;
}