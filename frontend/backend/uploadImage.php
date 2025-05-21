<?php
require_once 'cors.php';
include 'connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!isset($_FILES['image'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No image uploaded']);
        exit;
    }

    $file = $_FILES['image'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    // Get file extension
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Allowed extensions
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if(!in_array($fileExt, $allowed)) {
        http_response_code(400);
        echo json_encode(['error' => 'File type not allowed']);
        exit;
    }

    if($fileError !== 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Error uploading file']);
        exit;
    }

    if($fileSize > 5000000) { // 5MB max
        http_response_code(400);
        echo json_encode(['error' => 'File too large']);
        exit;
    }

    // Generate unique filename
    $newFileName = uniqid('img_') . '.' . $fileExt;
    $uploadPath = '../uploadedimages/' . $newFileName;

    // Create directory if it doesn't exist
    if(!file_exists('../uploadedimages')) {
        mkdir('../uploadedimages', 0777, true);
    }

    if(move_uploaded_file($fileTmpName, $uploadPath)) {
        // Insert image record in database
        $conn = connection();
        $sql = "INSERT INTO images (img_url) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $newFileName);
        
        if($stmt->execute()) {
            $imgId = $conn->insert_id;
            echo json_encode([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'image_id' => $imgId,
                'image_url' => $newFileName
            ]);
        } else {
            // If database insert fails, delete the uploaded file
            unlink($uploadPath);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save image record']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload image']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 