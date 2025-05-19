<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

function connection(){
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';  // Password vuota per XAMPP di default
    $db_name = 'bitems';

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
    } else {
        return $conn;
    }
}
