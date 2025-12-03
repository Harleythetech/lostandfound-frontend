<?php

class PageController {
    
    public function home() {
        // Redirect logged-in users based on role
        if (isLoggedIn()) {
            if (isAdmin()) {
                redirect('/admin');
            } else {
                redirect('/dashboard');
            }
        }
        
        // Use the landing API endpoint (no auth required)
        $landingResponse = apiRequest('/landing', 'GET');
        $landingData = $landingResponse['data']['data'] ?? $landingResponse['data'] ?? [];
        
        // Extract data from landing response
        $recentLost = $landingData['lost_items'] ?? $landingData['lostItems'] ?? [];
        $recentFound = $landingData['found_items'] ?? $landingData['foundItems'] ?? [];
        
        // Build stats from landing response
        $statsData = $landingData['stats'] ?? $landingData['statistics'] ?? [];
        $stats = [
            'total_lost' => $statsData['total_lost'] ?? $statsData['totalLost'] ?? count($recentLost),
            'total_found' => $statsData['total_found'] ?? $statsData['totalFound'] ?? count($recentFound),
            'total_resolved' => $statsData['total_resolved'] ?? $statsData['totalResolved'] ?? 0
        ];
        
        include __DIR__ . '/../../views/home.php';
    }
    
    public function about() {
        include __DIR__ . '/../../views/about.php';
    }
    
    public function contact() {
        include __DIR__ . '/../../views/contact.php';
    }
    
    public function health() {
        $response = apiRequest('/health', 'GET');
        header('Content-Type: application/json');
        echo json_encode($response['data']);
        exit;
    }
}
