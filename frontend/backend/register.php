<?php
//registrazione utente

include 'connection.php';

$conn = connection();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $check_email = "SELECT * FROM users WHERE email = ?";
    $check_username = "SELECT * FROM users WHERE username = ?";

    $stmt = $conn->prepare($check_email);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result_email = $stmt->get_result();

    $stmt = $conn->prepare($check_username);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result_username = $stmt->get_result();

    //sha256 password
    $password = hash('sha256', $password);

    //inserimento dati
    if(mysqli_num_rows($result_email) > 0){
        echo json_encode(['error' => 'Email già esistente', 'code' => 1]); //codice 1 per email già esistente
    }else if(mysqli_num_rows($result_username) > 0){
        echo json_encode(['error' => 'Username già esistente', 'code' => 2]); //codice 2 per username già esistente
    }else{
        $sql = "INSERT INTO users (nome, cognome, email, password) VALUES ('$nome', '$cognome', '$email', '$password')";
        $result = mysqli_query($conn, $sql);
    }
}


?>
