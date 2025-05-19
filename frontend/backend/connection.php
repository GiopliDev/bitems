<?php

function connection(){
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'bitems';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}else{
    return $conn;
}
}
