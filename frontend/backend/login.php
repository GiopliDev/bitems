<?php
require_once 'cors.php';
require_once 'connection.php';

$conn = connection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from POST
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username e password sono obbligatori']);
        exit;
    }

    // Hash password
    $password = hash('sha256', $password);

    // Check credentials
    $sql = "SELECT * FROM utenti WHERE ute_username = ? AND ute_password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Remove sensitive data
        unset($user['ute_password']);
        echo json_encode([
            'success' => true,
            'message' => 'Login effettuato con successo',
            'user' => $user
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Username o password non validi']);
    }
}
?>