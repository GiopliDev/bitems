<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Configurazione del database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'bitems';

// Connessione al database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verifica della connessione
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Gestione delle richieste
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Esempio di query per ottenere tutti gli items
        $sql = "SELECT * FROM items";
        $result = $conn->query($sql);
        
        if ($result) {
            $items = [];
            while($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $items]);
        } else {
            echo json_encode(['error' => 'Query failed: ' . $conn->error]);
        }
        break;

    case 'POST':
        // Ricevi i dati JSON
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Esempio di inserimento
        $sql = "INSERT INTO items (name, description) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $data['name'], $data['description']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item created successfully']);
        } else {
            echo json_encode(['error' => 'Insert failed: ' . $stmt->error]);
        }
        break;

    default:
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

$conn->close();
?> 