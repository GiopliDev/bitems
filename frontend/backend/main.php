<?php
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    if($_POST['action'] == 'login'){
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['name'];
        $email = $data['email'];
        $password = $data['password'];
    }
    
}