<?php

/**
 * User Model
 * 
 * This is a placeholder model for User-related operations.
 * The actual data is fetched from the API.
 */
class User {
    public $id;
    public $name;
    public $email;
    public $created_at;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->created_at = $data['created_at'] ?? null;
    }
    
    /**
     * Get user by ID from API
     */
    public static function find($id) {
        $response = apiRequest('/users/' . $id, 'GET', null, getToken());
        if ($response['status'] === 200) {
            return new self($response['data']['data'] ?? $response['data']);
        }
        return null;
    }
    
    /**
     * Get current authenticated user
     */
    public static function current() {
        if (isLoggedIn()) {
            return new self(getCurrentUser());
        }
        return null;
    }
}
