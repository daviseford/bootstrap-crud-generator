<?php

/* You will need to fill out the empty variables to connect to your MySQL instance. */
function mySqlQuery($query)
{

    $password = '';         // IMPORTANT: FILL THESE OUT
    $username = '';         // IMPORTANT: FILL THESE OUT
    $servername = '';       // IMPORTANT: FILL THESE OUT
    $dbName = '';           // IMPORTANT: FILL THESE OUT

    $conn = new mysqli($servername, $username, $password, $dbName);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result = $conn->query($query);

    $conn->close();

    return $result;
}

function mysql_escape_mimic($inp)
{
    if (is_array($inp))
        return array_map(__METHOD__, $inp);

    if (!empty($inp) && is_string($inp)) {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
    }

    return $inp;
}
