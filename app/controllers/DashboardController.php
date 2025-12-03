<?php

class DashboardController {
    
    public function index() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $response = apiRequest('/dashboard', 'GET', null, getToken());
        $dashboard = $response['data']['data'] ?? $response['data'] ?? [];
        
        // Get search/filter parameters
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $location = $_GET['location'] ?? '';
        $tab = $_GET['tab'] ?? 'lost';
        
        // Build query params for items
        $queryParams = ['page=1', 'limit=12', 'status=approved'];
        if ($search) $queryParams[] = "search=" . urlencode($search);
        if ($category) $queryParams[] = "category_id=" . urlencode($category);
        if ($location) $queryParams[] = "location_id=" . urlencode($location);
        
        // Fetch lost items using correct endpoint
        $lostResponse = apiRequest('/lost-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $lostData = $lostResponse['data']['data'] ?? $lostResponse['data'] ?? [];
        $lostItems = $lostData['items'] ?? $lostData ?? [];
        
        // Fetch found items using correct endpoint
        $foundResponse = apiRequest('/found-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $foundData = $foundResponse['data']['data'] ?? $foundResponse['data'] ?? [];
        $foundItems = $foundData['items'] ?? $foundData ?? [];
        
        // Debug: Uncomment to see API responses
        // echo '<pre>Lost Response: '; print_r($lostResponse); echo '</pre>';
        // echo '<pre>Found Response: '; print_r($foundResponse); echo '</pre>'; exit;
        
        // Fetch categories and locations for filters
        $categoriesResponse = apiRequest('/categories?active_only=true', 'GET');
        $categories = $categoriesResponse['data']['data'] ?? $categoriesResponse['data'] ?? [];
        if (isset($categories['success']) || !is_array($categories)) $categories = [];
        
        $locationsResponse = apiRequest('/locations?active_only=true', 'GET');
        $locations = $locationsResponse['data']['data'] ?? $locationsResponse['data'] ?? [];
        if (isset($locations['success']) || !is_array($locations)) $locations = [];
        
        $filters = [
            'search' => $search,
            'category' => $category,
            'location' => $location,
            'tab' => $tab
        ];
        
        include __DIR__ . '/../../views/dashboard/index.php';
    }
    
    public function profile() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        // Fetch full profile from API
        $response = apiRequest('/auth/me', 'GET', null, getToken());
        
        // Debug: Uncomment to see raw API response
        // echo '<pre>'; print_r($response); echo '</pre>'; exit;
        
        $user = [];
        
        // Handle different response structures
        if ($response['status'] === 200) {
            $data = $response['data'] ?? [];
            
            // Try various nested structures
            if (isset($data['data']['user'])) {
                $user = $data['data']['user'];
            } elseif (isset($data['user'])) {
                $user = $data['user'];
            } elseif (isset($data['data']) && is_array($data['data'])) {
                $user = $data['data'];
            } elseif (isset($data['id']) || isset($data['first_name'])) {
                // Data is directly the user object
                $user = $data;
            }
            
            // Update session with fresh data if we got valid user data
            if (!empty($user) && isset($user['first_name'])) {
                $_SESSION['user'] = array_merge($_SESSION['user'] ?? [], $user);
            }
        }
        
        // Fall back to session if API didn't return user data
        if (empty($user) || !isset($user['first_name'])) {
            $user = getCurrentUser() ?? [];
        }
        
        // Get notification preferences
        $prefResponse = apiRequest('/notifications/preferences', 'GET', null, getToken());
        $preferences = $prefResponse['data']['data'] ?? $prefResponse['data'] ?? [];
        
        include __DIR__ . '/../../views/dashboard/profile.php';
    }
    
    public function updateProfile() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'contact_number' => $_POST['contact_number'] ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? '',
            'gender' => $_POST['gender'] ?? '',
            'address_line1' => $_POST['address_line1'] ?? '',
            'address_line2' => $_POST['address_line2'] ?? '',
            'city' => $_POST['city'] ?? '',
            'province' => $_POST['province'] ?? '',
            'postal_code' => $_POST['postal_code'] ?? '',
            'emergency_contact_name' => $_POST['emergency_contact_name'] ?? '',
            'emergency_contact_number' => $_POST['emergency_contact_number'] ?? '',
            'department' => $_POST['department'] ?? '',
            'year_level' => $_POST['year_level'] ?? ''
        ];
        
        $response = apiRequest('/dashboard/profile', 'PUT', $data, getToken());
        
        if ($response['status'] === 200) {
            // Update session user data
            $_SESSION['user'] = array_merge($_SESSION['user'], $data);
            setFlash('success', 'Profile updated successfully!');
        } else {
            $message = $response['data']['message'] ?? 'Failed to update profile';
            setFlash('danger', $message);
        }
        
        redirect('/dashboard/profile');
    }
    
    public function changePassword() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword)) {
            setFlash('danger', 'Please fill in all fields');
            redirect('/dashboard/profile');
        }
        
        if ($newPassword !== $confirmPassword) {
            setFlash('danger', 'New passwords do not match');
            redirect('/dashboard/profile');
        }
        
        $response = apiRequest('/dashboard/profile/password', 'PUT', [
            'current_password' => $currentPassword,
            'new_password' => $newPassword
        ], getToken());
        
        if ($response['status'] === 200) {
            setFlash('success', 'Password changed successfully!');
        } else {
            $message = $response['data']['message'] ?? 'Failed to change password';
            setFlash('danger', $message);
        }
        
        redirect('/dashboard/profile');
    }
    
    public function activity() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $response = apiRequest('/dashboard/activity', 'GET', null, getToken());
        $activities = $response['data']['data'] ?? [];
        
        include __DIR__ . '/../../views/dashboard/activity.php';
    }
    
    public function myLostItems() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $queryParams = ["page={$page}"];
        if ($status) $queryParams[] = "status=" . urlencode($status);
        if ($search) $queryParams[] = "search=" . urlencode($search);
        
        $response = apiRequest('/dashboard/my-lost-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $items = $response['data'] ?? [];
        
        $filters = [
            'status' => $status,
            'search' => $search
        ];
        
        include __DIR__ . '/../../views/dashboard/my-lost-items.php';
    }
    
    public function myFoundItems() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $queryParams = ["page={$page}"];
        if ($status) $queryParams[] = "status=" . urlencode($status);
        if ($search) $queryParams[] = "search=" . urlencode($search);
        
        $response = apiRequest('/dashboard/my-found-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $items = $response['data'] ?? [];
        
        $filters = [
            'status' => $status,
            'search' => $search
        ];
        
        include __DIR__ . '/../../views/dashboard/my-found-items.php';
    }
    
    public function myClaims() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        
        $queryParams = ["page={$page}"];
        if ($status) $queryParams[] = "status=" . urlencode($status);
        
        $response = apiRequest('/dashboard/my-claims?' . implode('&', $queryParams), 'GET', null, getToken());
        $claims = $response['data'] ?? [];
        
        include __DIR__ . '/../../views/dashboard/my-claims.php';
    }
    
    public function claimsOnMyItems() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        
        $queryParams = ["page={$page}"];
        if ($status) $queryParams[] = "status=" . urlencode($status);
        
        $response = apiRequest('/dashboard/claims-on-my-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $claims = $response['data']['data'] ?? [];
        $pagination = $response['data']['pagination'] ?? null;
        
        include __DIR__ . '/../../views/dashboard/claims-on-items.php';
    }
    
    public function myMatches() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $page = $_GET['page'] ?? 1;
        $type = $_GET['type'] ?? '';
        
        $queryParams = ["page={$page}"];
        if ($type) $queryParams[] = "type=" . urlencode($type);
        
        $response = apiRequest('/dashboard/my-matches?' . implode('&', $queryParams), 'GET', null, getToken());
        $matches = $response['data'] ?? [];
        
        include __DIR__ . '/../../views/dashboard/my-matches.php';
    }
}
