<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Verifica se l'utente è admin (ID = 1)
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    http_response_code(403);
    echo "false";
    exit;
}

require_once 'connection.php';
$conn = connection();

// Gestione delle richieste
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getUsers':
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Ottieni il conteggio totale degli utenti
        $countQuery = "SELECT COUNT(*) as total FROM utenti";
        $countResult = mysqli_query($conn, $countQuery);
        $totalUsers = mysqli_fetch_assoc($countResult)['total'];
        
        // Ottieni gli utenti per la pagina corrente
        $query = "SELECT u.*, i.img_url 
                  FROM utenti u 
                  LEFT JOIN images i ON u.ute_img_id = i.img_id
                  LIMIT $limit OFFSET $offset";
        $result = mysqli_query($conn, $query);
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        
        echo json_encode([
            'users' => $users,
            'total' => $totalUsers,
            'hasMore' => ($offset + $limit) < $totalUsers
        ]);
        break;

    case 'getItems':
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Ottieni il conteggio totale degli articoli
        $countQuery = "SELECT COUNT(*) as total FROM articoli";
        $countResult = mysqli_query($conn, $countQuery);
        $totalItems = mysqli_fetch_assoc($countResult)['total'];
        
        // Ottieni gli articoli per la pagina corrente
        $query = "SELECT a.*, 
                         u.ute_username as seller_name,
                         u.ute_rep as seller_rep,
                         GROUP_CONCAT(DISTINCT t.tag_nome) as tags,
                         GROUP_CONCAT(DISTINCT img.img_url) as images
                  FROM articoli a
                  LEFT JOIN utenti u ON a.art_ute_id = u.ute_id
                  LEFT JOIN tags_articoli ta ON a.art_id = ta.art_id
                  LEFT JOIN tags t ON ta.tag_id = t.tag_id
                  LEFT JOIN images_articoli ia ON a.art_id = ia.art_id
                  LEFT JOIN images img ON ia.img_id = img.img_id
                  GROUP BY a.art_id
                  LIMIT $limit OFFSET $offset";
        
        $result = mysqli_query($conn, $query);
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['tags'] = $row['tags'] ? explode(',', $row['tags']) : [];
            $row['images'] = $row['images'] ? explode(',', $row['images']) : [];
            $items[] = $row;
        }
        
        echo json_encode([
            'items' => $items,
            'total' => $totalItems,
            'hasMore' => ($offset + $limit) < $totalItems
        ]);
        break;

    case 'updateUser':
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $data['userId'] ?? 0;
        $updates = $data['updates'] ?? [];
        
        if (empty($updates)) {
            echo "false";
            exit;
        }
        
        $allowedFields = ['ute_username', 'ute_email', 'ute_saldo', 'ute_rep'];
        $setClauses = [];
        $types = '';
        $values = [];
        
        foreach ($updates as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $setClauses[] = "$field = ?";
                $types .= 's';
                $values[] = $value;
            }
        }
        
        if (empty($setClauses)) {
            echo "false";
            exit;
        }
        
        $values[] = $userId;
        $types .= 'i';
        
        $query = "UPDATE utenti SET " . implode(', ', $setClauses) . " WHERE ute_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "true";
        } else {
            echo "false";
        }
        break;

    case 'banUser':
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $data['userId'] ?? 0;

        if ($userId == 1) {
            echo "false";
            exit;
        }

        mysqli_begin_transaction($conn);

        try {
            // 1. Elimina i segnalibri
            $query = "DELETE FROM segnalibri WHERE seg_ute_id = $userId";
            mysqli_query($conn, $query);

            // 2. Elimina le immagini degli articoli
            $query = "SELECT art_id FROM articoli WHERE art_ute_id = $userId";
            $result = mysqli_query($conn, $query);
            $items = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $items[] = $row['art_id'];
            }

            if (!empty($items)) {
                $itemsStr = implode(',', $items);
                $query = "DELETE ia FROM images_articoli ia WHERE ia.art_id IN ($itemsStr)";
                mysqli_query($conn, $query);
            }

            // 3. Elimina i tag degli articoli
            if (!empty($items)) {
                $query = "DELETE ta FROM tags_articoli ta WHERE ta.art_id IN ($itemsStr)";
                mysqli_query($conn, $query);
            }

            // 4. Elimina gli articoli
            $query = "DELETE FROM articoli WHERE art_ute_id = $userId";
            mysqli_query($conn, $query);

            // 5. Elimina le recensioni
            $query = "DELETE FROM recensioni WHERE rec_ute_id = $userId";
            mysqli_query($conn, $query);

            // 6. Elimina l'utente
            $query = "DELETE FROM utenti WHERE ute_id = $userId";
            mysqli_query($conn, $query);

            mysqli_commit($conn);
            echo "true";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "false";
        }
        break;

    case 'deleteItem':
        $itemId = $_GET['id'] ?? 0;

        mysqli_begin_transaction($conn);

        try {
            // 1. Elimina le immagini dell'articolo
            $query = "DELETE ia FROM images_articoli ia WHERE ia.art_id = $itemId";
            mysqli_query($conn, $query);

            // 2. Elimina i tag dell'articolo
            $query = "DELETE ta FROM tags_articoli ta WHERE ta.art_id = $itemId";
            mysqli_query($conn, $query);

            // 3. Elimina le recensioni dell'articolo
            $query = "DELETE FROM recensioni WHERE rec_art_id = $itemId";
            mysqli_query($conn, $query);

            // 4. Elimina l'articolo
            $query = "DELETE FROM articoli WHERE art_id = $itemId";
            mysqli_query($conn, $query);

            mysqli_commit($conn);
            echo "true";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "false";
        }
        break;

    default:
        http_response_code(400);
        echo "false";
        break;
}
?> 