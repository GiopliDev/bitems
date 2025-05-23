<?php
require_once 'cors.php';
require_once 'connection.php';

$conn = connection();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Get trending items (items with most views/orders)
    $sql = "SELECT a.*, u.ute_username, u.ute_rep 
            FROM articoli a 
            JOIN utenti u ON a.ute_id = u.ute_id 
            WHERE a.art_status = 'D' 
            ORDER BY a.art_views DESC, a.art_orders DESC 
            LIMIT 10";
            
    $result = $conn->query($sql);
    
    if ($result) {
        $items = array();
        while ($row = $result->fetch_assoc()) {
            // Format the data
            $item = array(
                'id' => $row['art_id'],
                'itemName' => $row['art_titolo'],
                'price' => $row['art_prezzoUnitario'],
                'gameName' => $row['art_gioco'],
                'category' => $row['art_categoria'],
                'status' => $row['art_status'],
                'qty' => $row['art_qtaDisp'],
                'user' => $row['ute_username'],
                'userRep' => $row['ute_rep'],
                'img' => $row['art_img'],
                'tags' => explode(',', $row['art_tags'])
            );
            $items[] = $item;
        }
        echo json_encode(['success' => true, 'items' => $items]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore nel recupero degli articoli']);
    }
}
?> 