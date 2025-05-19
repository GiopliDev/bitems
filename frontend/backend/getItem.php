<?php
// Abilita CORS
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Gestisci le richieste OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

//get oggetto in base all id

include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $id = $_GET['id'];
    $response = getItem($id);
    echo $response;
}

function getItem($id){
    $conn = connection();

    $sql = "SELECT articoli.art_id,
    articoli.art_titolo,
    articoli.art_qtaDisp,
    articoli.art_prezzoUnitario,
    articoli.art_descrizione,
    articoli.art_timestamp, 
    articoli.art_status, 
    utenti.ute_id, 
    utenti.ute_username, 
    utenti.ute_rep 
    FROM articoli 
    INNER JOIN utenti ON articoli.art_ute_id = utenti.ute_id 
    WHERE articoli.art_id = ? AND articoli.art_status != 'N'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        $recensioni = getItemRecensioni($id);
        $item['recensioni'] = json_decode($recensioni, true);
        return json_encode($item);
    } else {
        return json_encode(['error' => 'Item not found']);
    }
}

function getItemRecensioni($id){
    $conn = connection();

    $sql = "SELECT utenti.ute_id,
    utenti.ute_username,
    recensioni.* 
    FROM recensioni 
    INNER JOIN utenti on recensioni.rec_ute_id = utenti.ute_id 
    WHERE recensioni.rec_art_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $recensioni = [];
    while($row = $result->fetch_assoc()) {
        $recensioni[] = $row;
    }
    
    return json_encode($recensioni);
}
?>