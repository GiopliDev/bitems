<?php
require_once 'cors.php';
include 'connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['action'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Action is required']);
        exit;
    }

    $conn = connection();

    switch ($data['action']) {
        case 'checkBookmark':
            if (!isset($_SESSION['user_id']) || !isset($data['id_articolo'])) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID and Article ID are required']);
                exit;
            }

            $sql = "SELECT * FROM segnalibri WHERE seg_ute_id = ? AND seg_art_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $_SESSION['user_id'], $data['id_articolo']);
            $stmt->execute();
            $result = $stmt->get_result();

            echo json_encode(['success' => true, 'isBookmarked' => $result->num_rows > 0]);
            break;

        case 'addBookmark':
            if (!isset($_SESSION['user_id']) || !isset($data['id_articolo'])) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID and Article ID are required']);
                exit;
            }

            // Check if bookmark already exists
            $checkSql = "SELECT * FROM segnalibri WHERE seg_ute_id = ? AND seg_art_id = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param('ii', $_SESSION['user_id'], $data['id_articolo']);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Article already bookmarked']);
                exit;
            }

            // Add new bookmark
            $sql = "INSERT INTO segnalibri (seg_ute_id, seg_art_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $_SESSION['user_id'], $data['id_articolo']);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Bookmark added successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to add bookmark']);
            }
            break;

        case 'removeBookmark':
            if (!isset($_SESSION['user_id']) || !isset($data['id_articolo'])) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID and Article ID are required']);
                exit;
            }

            $sql = "DELETE FROM segnalibri WHERE seg_ute_id = ? AND seg_art_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $_SESSION['user_id'], $data['id_articolo']);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Bookmark removed successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to remove bookmark']);
            }
            break;

        case 'getBookmarks':
            if (!isset($_SESSION['user_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID is required']);
                exit;
            }

            $sql = "SELECT a.*, 
                    g.gio_nome as game_name,
                    t.tip_nome as category_name,
                    u.ute_username as seller_name,
                    u.ute_rep as seller_rep,
                    GROUP_CONCAT(DISTINCT tg.tag_nome) as tags,
                    (SELECT img.img_url 
                     FROM images_articoli ia 
                     JOIN images img ON ia.img_id = img.img_id 
                     WHERE ia.art_id = a.art_id 
                     LIMIT 1) as image
                    FROM segnalibri s
                    JOIN articoli a ON s.seg_art_id = a.art_id
                    JOIN giochiaffiliati g ON a.art_gio_id = g.gio_id
                    JOIN tipologie t ON a.art_tip_id = t.tip_id
                    JOIN utenti u ON a.art_ute_id = u.ute_id
                    LEFT JOIN tags_articoli ta ON a.art_id = ta.art_id
                    LEFT JOIN tags tg ON ta.tag_id = tg.tag_id
                    WHERE s.seg_ute_id = ? AND a.art_isPrivato = 0
                    GROUP BY a.art_id";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            $bookmarks = [];
            while ($row = $result->fetch_assoc()) {
                $row['tags'] = $row['tags'] ? explode(',', $row['tags']) : [];
                $row['image'] = $row['image'] ?: 'default.jpg';
                $bookmarks[] = $row;
            }

            echo json_encode(['success' => true, 'data' => $bookmarks]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
