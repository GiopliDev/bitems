<?php
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $name = $data['name'];
        //password hashata con sha256
        $password = hash('sha256', $data['password']);

        $sql = "SELECT * FROM users WHERE name = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $name, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if(mysqli_num_rows($result) > 0){
            echo json_encode(['success' => 'Login effettuato con successo']);
        }else{
            echo json_encode(['error' => 'Credenziali non valide', 'code' => 3]); //codice 3 per credenziali non valide
        }
    }
?>