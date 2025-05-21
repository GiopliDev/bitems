<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo 'Username and password are required';
        exit();
    }

    $conn = connection();

    // da aggiungere salt
    $password = hash('sha256', $password);  

    // Check credentials
    $sql = "SELECT ute_id, ute_username, ute_saldo FROM utenti WHERE ute_username = ? AND ute_password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Distruggi la sessione esistente se presente
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        
        // Crea una nuova sessione
        session_start();
        session_regenerate_id(true);
        
        // Imposta i dati della sessione
        $_SESSION['user_id'] = $user['ute_id'];
        $_SESSION['username'] = $user['ute_username'];
        $_SESSION['last_activity'] = time();
        
        echo 'success';
    } else {
        http_response_code(401);
        echo 'Invalid credentials';
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo 'Method not allowed';
}
?>