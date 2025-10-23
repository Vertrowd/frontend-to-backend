<?php
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$dbname = 'profiledb';
$username = 'root';
$password = '';


// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'upload_errors.log');

// Debug function
function debug_log($data) {
    file_put_contents('debug.log', print_r($data, true) . PHP_EOL, FILE_APPEND);
}

// Log the request
debug_log('=== NEW REQUEST ===');
debug_log('POST data: ' . print_r($_POST, true));
debug_log('FILES data: ' . print_r($_FILES, true));
debug_log('REQUEST method: ' . $_SERVER['REQUEST_METHOD']);

try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    // Check if form data is received
    if (empty($_POST)) {
        throw new Exception('No POST data received. Check form enctype and method.');
    }

    // Validate required fields with null coalescing operator
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    debug_log("Extracted values - Name: $name, Email: $email, Phone: $phone");

    if (empty($name) || empty($email) || empty($phone)) {
        throw new Exception('All required fields must be filled. Received - Name: ' . ($name ?: 'empty') . ', Email: ' . ($email ?: 'empty') . ', Phone: ' . ($phone ?: 'empty'));
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format: ' . $email);
    }

    // Validate file upload
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $fileError = $_FILES['file']['error'] ?? 'No file uploaded';
        throw new Exception('File upload failed. Error code: ' . $fileError);
    }

    $file = $_FILES['file'];
    
    // Upload configuration
    $uploadDir = 'uploads/';
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    $allowedTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    // Create uploads directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception('Failed to create upload directory.');
        }
    }

    // Check file size
    if ($file['size'] > $maxFileSize) {
        throw new Exception('File size exceeds the 5MB limit.');
    }

    // Check file type
    $fileType = mime_content_type($file['tmp_name']);
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileType, $allowedTypes) || !array_key_exists($fileExtension, $allowedTypes)) {
        throw new Exception('Invalid file type. Allowed types: JPG, PNG, PDF, DOC. Received: ' . $fileType);
    }

    // Generate unique filename
    $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file['name']);
    $filePath = $uploadDir . $fileName;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Failed to save uploaded file.');
    }

    // For now, just return success without database to test
    $response = [
        'success' => true,
        'message' => 'File uploaded successfully! (Database connection skipped for testing)',
        'data' => [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'file' => $fileName
        ]
    ];

    debug_log('SUCCESS: ' . print_r($response, true));
    echo json_encode($response);

} catch (Exception $e) {
    $errorResponse = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    
    debug_log('ERROR: ' . $e->getMessage());
    echo json_encode($errorResponse);
}
?>