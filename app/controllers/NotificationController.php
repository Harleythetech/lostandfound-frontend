<?php

class NotificationController {
    
    public function index() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $page = $_GET['page'] ?? 1;
        $unreadOnly = isset($_GET['unread_only']) && $_GET['unread_only'] === 'true';
        
        $queryParams = ["page={$page}", "limit=20"];
        if ($unreadOnly) $queryParams[] = "unread_only=true";
        
        $response = apiRequest('/notifications?' . implode('&', $queryParams), 'GET', null, getToken());
        $notifications = $response['data']['data'] ?? $response['data']['notifications'] ?? [];
        $pagination = $response['data']['pagination'] ?? null;
        
        include __DIR__ . '/../../views/notifications/index.php';
    }
    
    // API endpoint for AJAX dropdown
    public function apiList() {
        header('Content-Type: application/json');
        
        if (!isLoggedIn()) {
            echo json_encode(['data' => []]);
            exit;
        }
        
        $limit = $_GET['limit'] ?? 5;
        $response = apiRequest('/notifications?limit=' . $limit, 'GET', null, getToken());
        
        // Return the response data directly
        echo json_encode([
            'data' => $response['data']['data'] ?? $response['data']['notifications'] ?? []
        ]);
        exit;
    }
    
    public function unreadCount() {
        if (!isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['count' => 0]);
            exit;
        }
        
        $response = apiRequest('/notifications/unread-count', 'GET', null, getToken());
        $count = $response['data']['data']['count'] ?? $response['data']['count'] ?? 0;
        
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }
    
    public function markAsRead($id) {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        apiRequest('/notifications/' . $id . '/read', 'PATCH', null, getToken());
        
        // Redirect back or to notifications
        $redirect = $_GET['redirect'] ?? '/notifications';
        redirect($redirect);
    }
    
    public function markAllAsRead() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $response = apiRequest('/notifications/read-all', 'PATCH', null, getToken());
        
        if ($response['status'] === 200) {
            setFlash('success', 'All notifications marked as read');
        }
        
        redirect('/notifications');
    }
    
    public function delete($id) {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $response = apiRequest('/notifications/' . $id, 'DELETE', null, getToken());
        
        if ($response['status'] === 200 || $response['status'] === 204) {
            setFlash('success', 'Notification deleted');
        }
        
        redirect('/notifications');
    }
    
    public function clearRead() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $response = apiRequest('/notifications/clear-read', 'DELETE', null, getToken());
        
        if ($response['status'] === 200 || $response['status'] === 204) {
            setFlash('success', 'Read notifications cleared');
        }
        
        redirect('/notifications');
    }
    
    public function preferences() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $response = apiRequest('/notifications/preferences', 'GET', null, getToken());
        $preferences = $response['data']['data'] ?? [];
        
        include __DIR__ . '/../../views/notifications/preferences.php';
    }
    
    public function updatePreferences() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $emailNotifications = isset($_POST['email_notifications']);
        
        $response = apiRequest('/notifications/preferences', 'PATCH', [
            'email_notifications' => $emailNotifications
        ], getToken());
        
        if ($response['status'] === 200) {
            setFlash('success', 'Notification preferences updated');
        } else {
            setFlash('danger', 'Failed to update preferences');
        }
        
        redirect('/notifications/preferences');
    }
}
