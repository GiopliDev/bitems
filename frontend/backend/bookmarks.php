<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');
require_once 'cors.php';
require_once 'connection.php';
require_once 'utils.php';

//i bookmarks sono privati,puo vederli solo l utente corrispondente all id della sessione
//<br />
//<b>Warning</b>:  Undefined array key "id_utente" in <b>C:\xampp\htdocs\bitems\frontend\backend\bookmarks.php</b> on line <b>17</b><br />
//<br />
//<b>Fatal error</b>:  Uncaught mysqli_sql_exception: Column 'seg_ute_id' cannot be null in C:\xampp\htdocs\bitems\frontend\backend\bookmarks.php:23
//Stack trace:
//#0 C:\xampp\htdocs\bitems\frontend\backend\bookmarks.php(23): mysqli_stmt-&gt;execute()
//#1 {main}
//thrown in <b>C:\xampp\htdocs\bitems\frontend\backend\bookmarks.php</b> on line <b>23</b><br />

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $conn = connection();
    $userId = $_SESSION['user_id'];
    $action = $_POST['action'] ?? '';

    if ($action === 'addBookmark') {
        $articleId = $_POST['id_articolo'] ?? null;
        
        if (!$articleId) {
            http_response_code(400);
            echo json_encode(['error' => 'Article ID is required']);
            exit;
        }

        $sql = "INSERT INTO segnalibri (seg_ute_id, seg_art_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $userId, $articleId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Bookmark added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add bookmark']);
        }
        $stmt->close();
    } 
    else if ($action === 'removeBookmark') {
        $articleId = $_POST['id_articolo'] ?? null;
        
        if (!$articleId) {
            http_response_code(400);
            echo json_encode(['error' => 'Article ID is required']);
            exit;
        }

        $sql = "DELETE FROM segnalibri WHERE seg_ute_id = ? AND seg_art_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $userId, $articleId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Bookmark removed successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to remove bookmark']);
        }
        $stmt->close();
    }
    else if ($action === 'getBookmarks') {
        $whereClause = "articoli.art_id IN (SELECT seg_art_id FROM segnalibri WHERE seg_ute_id = ?)";
        $bookmarks = getItemCardData($whereClause, [$userId], 'i');
        
        if (isset($bookmarks['error'])) {
            http_response_code(500);
            echo json_encode(['error' => $bookmarks['error']]);
            exit;
        }
        
        echo json_encode(['success' => true, 'bookmarks' => $bookmarks]);
    }
    else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
