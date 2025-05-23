<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'connection.php';

function getImagesForArticle($conn, $articleId) {
    $sql = "SELECT i.img_url 
            FROM images i 
            JOIN images_articoli ia ON i.img_id = ia.img_id 
            WHERE ia.art_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $articleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while($row = $result->fetch_assoc()) {
        $images[] = $row['img_url'];
    }
    return $images;
}

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    session_start();
    if(!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $conn = connection();
    $userId = $_SESSION['user_id'];

    // Query per ottenere la cronologia acquisti con i dettagli dell'articolo
    $sql = "SELECT 
                c.cro_id,
                c.cro_timestamp,
                c.cro_qta,
                c.cro_prezzofinale,
                a.art_id,
                a.art_titolo,
                a.art_prezzoUnitario,
                u.ute_username as seller_name,
                r.rec_voto,
                r.rec_dex
            FROM cronologiaacquisti c
            JOIN articoli a ON c.cro_art_id = a.art_id
            JOIN utenti u ON a.art_ute_id = u.ute_id
            LEFT JOIN recensioni r ON c.cro_rec_id = r.rec_id
            WHERE c.cro_ute_id = ?
            ORDER BY c.cro_timestamp DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while($row = $result->fetch_assoc()) {
        // Formatta la data
        $date = new DateTime($row['cro_timestamp']);
        $formattedDate = $date->format('d/m/Y H:i');
        
        // Determina lo stato dell'ordine
        $status = 'completed';
        if($row['rec_voto'] === null) {
            $status = 'pending_review';
        }
        
        $history[] = [
            'id' => $row['cro_id'],
            'art_id' => $row['art_id'],
            'title' => $row['art_titolo'],
            'date' => $formattedDate,
            'quantity' => $row['cro_qta'],
            'unitPrice' => $row['art_prezzoUnitario'],
            'totalPrice' => $row['cro_prezzofinale'],
            'seller' => $row['seller_name'],
            'status' => $status,
            'review' => $row['rec_voto'] !== null ? [
                'rating' => $row['rec_voto'],
                'description' => $row['rec_dex']
            ] : null,
            'images' => getImagesForArticle($conn, $row['art_id'])
        ];
    }

    echo json_encode([
        'success' => true,
        'history' => $history
    ]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 