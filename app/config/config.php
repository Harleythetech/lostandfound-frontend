<?php
// Load .env file if it exists (for local development)
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) continue;
        
        // Parse KEY=value format
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if (preg_match('/^["\'](.*)["\']\s*$/', $value, $matches)) {
                $value = $matches[1];
            }
            
            // Set as environment variable if not already set
            if (!getenv($key)) {
                putenv("$key=$value");
            }
        }
    }
}

// Application Configuration
define('APP_NAME', 'Lost and Found');
define('APP_URL', '/lostandfound');  // Base URL for subdirectory installation
define('API_BASE_URL', 'http://localhost:8080/api');

// Firebase Configuration (loaded from environment variables or .env file)
define('FIREBASE_API_KEY', getenv('FIREBASE_API_KEY') ?: '');
define('FIREBASE_AUTH_DOMAIN', getenv('FIREBASE_AUTH_DOMAIN') ?: '');
define('FIREBASE_PROJECT_ID', getenv('FIREBASE_PROJECT_ID') ?: '');
define('FIREBASE_STORAGE_BUCKET', getenv('FIREBASE_STORAGE_BUCKET') ?: '');
define('FIREBASE_MESSAGING_SENDER_ID', getenv('FIREBASE_MESSAGING_SENDER_ID') ?: '');
define('FIREBASE_APP_ID', getenv('FIREBASE_APP_ID') ?: '');

// Session Configuration
session_start();

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper function to make API requests
function apiRequest($endpoint, $method = 'GET', $data = null, $token = null, $isMultipart = false) {
    $url = API_BASE_URL . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $headers = [];
    
    if (!$isMultipart) {
        $headers[] = 'Content-Type: application/json';
    }
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        if ($isMultipart) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'status' => 500,
            'data' => ['success' => false, 'message' => 'Connection error: ' . $error]
        ];
    }
    
    // Handle rate limiting
    if ($httpCode === 429) {
        return [
            'status' => 429,
            'data' => ['success' => false, 'message' => 'Too many requests. Please wait a few minutes and try again.']
        ];
    }
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true) ?? ['message' => $response]
    ];
}

/**
 * API request with multipart form data that properly handles multiple files with same field name
 * This is needed for Multer .array('images', 5) which expects repeated 'images' fields
 */
function apiRequestMultipart($endpoint, $data, $token = null) {
    $url = API_BASE_URL . $endpoint;
    
    // Separate files from regular data
    $files = [];
    $fields = [];
    
    foreach ($data as $key => $value) {
        if ($value instanceof CURLFile) {
            // Store file with original key for now
            $files[$key] = $value;
        } else {
            $fields[$key] = $value;
        }
    }
    
    // Build multipart body manually to allow duplicate field names for files
    $boundary = '----WebKitFormBoundary' . uniqid();
    $body = '';
    
    // Add regular fields first
    foreach ($fields as $key => $value) {
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"{$key}\"\r\n\r\n";
        $body .= "{$value}\r\n";
    }
    
    // Add files - all with field name "images" (not images[0], images[1])
    foreach ($files as $key => $curlFile) {
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"images\"; filename=\"{$curlFile->getPostFilename()}\"\r\n";
        $body .= "Content-Type: {$curlFile->getMimeType()}\r\n\r\n";
        $body .= file_get_contents($curlFile->getFilename()) . "\r\n";
    }
    
    $body .= "--{$boundary}--\r\n";
    
    // Log for debugging
    error_log("apiRequestMultipart to {$endpoint}: " . count($fields) . " fields, " . count($files) . " files");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    
    $headers = [
        'Content-Type: multipart/form-data; boundary=' . $boundary,
        'Content-Length: ' . strlen($body)
    ];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'status' => 500,
            'data' => ['success' => false, 'message' => 'Connection error: ' . $error]
        ];
    }
    
    if ($httpCode === 429) {
        return [
            'status' => 429,
            'data' => ['success' => false, 'message' => 'Too many requests. Please wait a few minutes and try again.']
        ];
    }
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true) ?? ['message' => $response]
    ];
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user']) && isset($_SESSION['accessToken']);
}

// Get current user
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

// Get auth token
function getToken() {
    return $_SESSION['accessToken'] ?? null;
}

// Get refresh token
function getRefreshToken() {
    return $_SESSION['refreshToken'] ?? null;
}

// Check if user is admin
function isAdmin() {
    $user = getCurrentUser();
    return $user && ($user['role'] ?? '') === 'admin';
}

// Check if user is security
function isSecurity() {
    $user = getCurrentUser();
    return $user && in_array($user['role'] ?? '', ['admin', 'security']);
}

// Redirect helper
function redirect($path) {
    header('Location: ' . APP_URL . $path);
    exit;
}

// Flash message helper
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// Display flash messages (used in views)
function displayFlash() {
    $flash = getFlash();
    if ($flash) {
        echo '<div class="alert alert-' . htmlspecialchars($flash['type']) . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}

// Format date helper
function formatDate($date, $format = 'M d, Y') {
    if (empty($date)) return 'N/A';
    return date($format, strtotime($date));
}

// Truncate text helper
function truncate($text, $length = 100) {
    if (empty($text)) return '';
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

// Get status badge class
function getStatusBadgeClass($status) {
    $classes = [
        'pending' => 'bg-warning text-dark',
        'approved' => 'bg-success',
        'rejected' => 'bg-danger',
        'matched' => 'bg-info',
        'claimed' => 'bg-primary',
        'resolved' => 'bg-success',
        'archived' => 'bg-secondary',
        'active' => 'bg-success',
        'suspended' => 'bg-danger',
        'cancelled' => 'bg-secondary'
    ];
    return $classes[$status] ?? 'bg-secondary';
}

// Get unread notification count (cached per request)
function getUnreadNotificationCount() {
    static $count = null;
    
    if ($count !== null) {
        return $count;
    }
    
    if (!isLoggedIn()) {
        $count = 0;
        return $count;
    }
    
    try {
        $response = apiRequest('/notifications/unread-count', 'GET', null, getToken());
        $count = $response['data']['count'] ?? $response['count'] ?? 0;
    } catch (Exception $e) {
        $count = 0;
    }
    
    return $count;
}
