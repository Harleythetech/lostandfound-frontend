<?php

class SearchController {
    
    public function index() {
        $q = $_GET['q'] ?? '';
        $type = $_GET['type'] ?? ''; // empty or 'all' = all, 'lost', 'found'
        $categoryId = $_GET['category_id'] ?? '';
        $locationId = $_GET['location_id'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $status = $_GET['status'] ?? 'approved';
        $page = $_GET['page'] ?? 1;
        
        // Build query parameters
        $queryParams = ["page={$page}", "limit=12"];
        if ($q) $queryParams[] = "q=" . urlencode($q);
        if ($categoryId) $queryParams[] = "category_id=" . urlencode($categoryId);
        if ($locationId) $queryParams[] = "location_id=" . urlencode($locationId);
        if ($status) $queryParams[] = "status=" . urlencode($status);
        if ($dateFrom) $queryParams[] = "date_from=" . urlencode($dateFrom);
        if ($dateTo) $queryParams[] = "date_to=" . urlencode($dateTo);
        
        $queryString = implode('&', $queryParams);
        
        // Determine which endpoint to use
        $endpoint = '/search/';
        switch ($type) {
            case 'lost':
                $endpoint .= 'lost';
                break;
            case 'found':
                $endpoint .= 'found';
                break;
            default:
                $endpoint .= 'all';
        }
        
        // Make the API request - search is now public (optionalAuth)
        $response = apiRequest($endpoint . '?' . $queryString, 'GET', null, getToken());
        
        // Extract results from response
        // API returns: {success, data: [...], pagination: {...}}
        $responseData = $response['data'] ?? [];
        
        // Get items array - it's directly in data, not nested
        $items = $responseData['data'] ?? [];
        $pagination = $responseData['pagination'] ?? null;
        
        // Items have 'item_type' field ('lost' or 'found'), map it to 'type' for the view
        foreach ($items as &$item) {
            $item['type'] = $item['item_type'] ?? 'found';
        }
        
        // Build results structure for the view
        $results = [
            'data' => $items,
            'total' => $pagination['total'] ?? count($items),
            'pagination' => $pagination
        ];
        
        // Store filters for the view
        $query = $q;
        $filters = [
            'type' => $type,
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];
        
        // Get categories and locations for filters
        $catResponse = apiRequest('/categories?active_only=true', 'GET');
        $categories = $catResponse['data']['data'] ?? $catResponse['data'] ?? [];
        
        $locResponse = apiRequest('/locations?active_only=true', 'GET');
        $locations = $locResponse['data']['data'] ?? $locResponse['data'] ?? [];
        
        include __DIR__ . '/../../views/search/index.php';
    }
    
    public function lost() {
        $_GET['type'] = 'lost';
        $this->index();
    }
    
    public function found() {
        $_GET['type'] = 'found';
        $this->index();
    }
}
