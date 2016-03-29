<?php
//namespace PHPSQL;

namespace PHPSQLParser;
require_once dirname(__FILE__) . '/php/PHP-SQL-Parser/src/PHPSQLParser/PHPSQLParser.php';

$sql = $_REQUEST['textarea'];

$parsed = parsePOSTSql($sql);

if (!empty($parsed['table']) && !empty($parsed['columns'])) {
    download_ListPHP($parsed['table'], $parsed['columns']);
}


function download_ListPHP($tableName, $arrayOfColumns)
{
    $file = 'new.php';

    //open file and get data
    $data = file_get_contents($file);

    // do replacements
    $data = str_replace("{{TABLE}}", $tableName, $data);
    $data = str_replace("{{COLUMNS}}", join(',', $arrayOfColumns), $data);


    //save it back:
    file_put_contents("make/new.php", $data);
    $newFile = "make/new.php";
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