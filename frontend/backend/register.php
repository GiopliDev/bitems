<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

//registrazione utente

include 'connection.php';

$conn = connection();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];
    $check_email = "SELECT * FROM utenti WHERE email = ?";
    $check_username = "SELECT * FROM utenti WHERE username = ?";

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
        $sql = "INSERT INTO utenti (ute_nome, ute_cognome, ute_email, ute_password, ute_username) VALUES ('$nome', '$cognome', '$email', '$password', '$username')";
        $result = mysqli_query($conn, $sql);
        return json_encode(['success' => 'Utente registrato con successo', 'code' => 0]); //codice 0 per utente registrato con successo
    }
}


?>
