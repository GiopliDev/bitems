<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

//registrazione utente

include 'connection.php';

$conn = connection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from POST
    $nome = $_POST['nome'] ?? '';
    $cognome = $_POST['cognome'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $username = $_POST['nickname'] ?? '';

    // Validate input
    if (empty($nome) || empty($cognome) || empty($email) || empty($password) || empty($username)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Check email
    $check_email = "SELECT * FROM utenti WHERE ute_email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result_email = $stmt->get_result();

    // Check username
    $check_username = "SELECT * FROM utenti WHERE ute_username = ?";
    $stmt = $conn->prepare($check_username);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result_username = $stmt->get_result();

    // Hash password
    $password = hash('sha256', $password);

    if (mysqli_num_rows($result_email) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email già esistente']);
    } else if (mysqli_num_rows($result_username) > 0) {
        echo json_encode(['success' => false, 'message' => 'Username già esistente']);
    } else {
        // Insert user with prepared statement
        $sql = "INSERT INTO utenti (ute_nome, ute_cognome, ute_email, ute_password, ute_username) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $nome, $cognome, $email, $password, $username);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Utente registrato con successo']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore durante la registrazione']);
        }
    }
}


?>
