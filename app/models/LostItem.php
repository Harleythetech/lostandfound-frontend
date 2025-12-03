<?php

/**
 * LostItem Model
 * 
 * This is a placeholder model for Lost Item-related operations.
 * The actual data is fetched from the API.
 */
class LostItem {
    public $id;
    public $title;
    public $description;
    public $category;
    public $status;
    public $location;
    public $date_lost;
    public $date_found;
    public $contact_info;
    public $image;
    public $user_id;
    public $created_at;
    public $updated_at;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->category = $data['category'] ?? '';
        $this->status = $data['status'] ?? 'lost';
        $this->location = $data['location'] ?? '';
        $this->date_lost = $data['date_lost'] ?? null;
        $this->date_found = $data['date_found'] ?? null;
        $this->contact_info = $data['contact_info'] ?? '';
        $this->image = $data['image'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
    
    /**
     * Get all items from API
     */
    public static function all($filters = []) {
        $queryParams = [];
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $queryParams[] = $key . '=' . urlencode($value);
            }
        }
        
        $endpoint = '/items';
        if (!empty($queryParams)) {
            $endpoint .= '?' . implode('&', $queryParams);
        }
        
        $response = apiRequest($endpoint, 'GET', null, getToken());
        $items = $response['data']['data'] ?? $response['data'] ?? [];
        
        return array_map(function($item) {
            return new self($item);
        }, $items);
    }
    
    /**
     * Get item by ID from API
     */
    public static function find($id) {
        $response = apiRequest('/items/' . $id, 'GET', null, getToken());
        if ($response['status'] === 200) {
            return new self($response['data']['data'] ?? $response['data']);
        }
        return null;
    }
    
    /**
     * Check if item is lost
     */
    public function isLost() {
        return $this->status === 'lost';
    }
    
    /**
     * Check if item is found
     */
    public function isFound() {
        return $this->status === 'found';
    }
    
    /**
     * Check if item is returned
     */
    public function isReturned() {
        return $this->status === 'returned';
    }
    
    /**
     * Get status badge class
     */
    public function getStatusClass() {
        switch ($this->status) {
            case 'lost':
                return 'bg-danger';
            case 'found':
                return 'bg-success';
            case 'returned':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }
}
