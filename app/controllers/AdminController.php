<?php

class AdminController
{

    private function checkAdmin()
    {
        if (!isLoggedIn() || !isAdmin()) {
            setFlash('danger', 'Unauthorized access');
            redirect('/dashboard');
        }
    }

    private function checkSecurity()
    {
        if (!isLoggedIn() || !isSecurity()) {
            setFlash('danger', 'Unauthorized access');
            redirect('/dashboard');
        }
    }

    private function checkSecurityOrAdmin()
    {
        if (!isLoggedIn() || (!isSecurity() && !isAdmin())) {
            setFlash('danger', 'Unauthorized access');
            redirect('/dashboard');
        }
    }

    public function dashboard()
    {
        $this->checkAdmin();

        // Get dashboard stats
        $response = apiRequest('/admin/dashboard', 'GET', null, getToken());
        $stats = $response['data']['data'] ?? $response['data'] ?? [];

        // Also fetch pending items to get accurate counts
        $pendingResponse = apiRequest('/admin/pending?limit=100', 'GET', null, getToken());
        $pendingData = $pendingResponse['data']['data'] ?? $pendingResponse['data'] ?? [];

        // Add pending counts to stats
        $stats['pending_lost_items'] = $pendingData['pending_lost_items'] ?? [];
        $stats['pending_found_items'] = $pendingData['pending_found_items'] ?? [];

        // Fetch pending claims count
        $claimsResponse = apiRequest('/claims?status=pending&limit=1', 'GET', null, getToken());
        $claimsData = $claimsResponse['data'] ?? [];
        $stats['pending_claims_count'] = $claimsData['pagination']['total'] ?? $claimsData['total'] ?? 0;

        include __DIR__ . '/../../views/admin/dashboard.php';
    }

    public function profile()
    {
        $this->checkAdmin();

        // Get dashboard stats to show admin widgets on the profile page
        $response = apiRequest('/admin/dashboard', 'GET', null, getToken());
        $stats = $response['data']['data'] ?? $response['data'] ?? [];

        // Current user profile
        $user = getCurrentUser();

        // Load preferences if available via notifications endpoint
        $prefResp = apiRequest('/notifications/preferences', 'GET', null, getToken());
        $preferences = $prefResp['data'] ?? [];

        include __DIR__ . '/../../views/admin/profile.php';
    }

    public function pending()
    {
        $this->checkSecurity();

        $type = $_GET['type'] ?? 'all';
        $limit = $_GET['limit'] ?? 20;

        $response = apiRequest("/admin/pending?limit={$limit}", 'GET', null, getToken());
        $data = $response['data']['data'] ?? $response['data'] ?? [];

        // Combine lost and found items into a single array with type markers
        $pending = [];

        // Add lost items (only if type is 'all' or 'lost')
        if ($type === 'all' || $type === 'lost') {
            $lostItems = $data['pending_lost_items'] ?? [];
            foreach ($lostItems as $item) {
                $item['type'] = 'lost';
                $pending[] = $item;
            }
        }

        // Add found items (only if type is 'all' or 'found')
        if ($type === 'all' || $type === 'found') {
            $foundItems = $data['pending_found_items'] ?? [];
            foreach ($foundItems as $item) {
                $item['type'] = 'found';
                $pending[] = $item;
            }
        }

        // Sort by created_at desc
        usort($pending, function ($a, $b) {
            return strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? '');
        });

        include __DIR__ . '/../../views/admin/pending.php';
    }

    // ============ USER MANAGEMENT ============

    public function users()
    {
        $this->checkAdmin();

        $status = $_GET['status'] ?? '';
        $role = $_GET['role'] ?? '';
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;

        $queryParams = ["page={$page}"];
        if ($status)
            $queryParams[] = "status=" . urlencode($status);
        if ($role)
            $queryParams[] = "role=" . urlencode($role);
        if ($search)
            $queryParams[] = "search=" . urlencode($search);

        $response = apiRequest('/admin/users?' . implode('&', $queryParams), 'GET', null, getToken());
        $users = $response['data']['data'] ?? [];
        $pagination = $response['data']['pagination'] ?? null;

        include __DIR__ . '/../../views/admin/users/index.php';
    }

    public function showUser($id)
    {
        $this->checkAdmin();

        $response = apiRequest('/admin/users/' . $id, 'GET', null, getToken());

        if ($response['status'] !== 200) {
            setFlash('danger', 'User not found');
            redirect('/admin/users');
        }

        $user = $response['data']['data'] ?? $response['data'];
        // Ensure counts for lost/found items and claims are available for the admin user view
        if (!isset($user['lost_items_count'])) {
            $lostResp = apiRequest('/admin/lost-items?user_id=' . $id . '&limit=1', 'GET', null, getToken());
            $lostData = $lostResp['data'] ?? [];
            $user['lost_items_count'] = $lostData['pagination']['total'] ?? $lostData['total'] ?? 0;
        }
        if (!isset($user['found_items_count'])) {
            $foundResp = apiRequest('/admin/found-items?user_id=' . $id . '&limit=1', 'GET', null, getToken());
            $foundData = $foundResp['data'] ?? [];
            $user['found_items_count'] = $foundData['pagination']['total'] ?? $foundData['total'] ?? 0;
        }
        if (!isset($user['claims_count'])) {
            $claimsResp = apiRequest('/claims?user_id=' . $id . '&limit=1', 'GET', null, getToken());
            $claimsData = $claimsResp['data'] ?? [];
            $user['claims_count'] = $claimsData['pagination']['total'] ?? $claimsData['total'] ?? 0;
        }
        include __DIR__ . '/../../views/admin/users/show.php';
    }

    public function manageUser($userId)
    {
        $this->checkAdmin();

        $action = $_POST['action'] ?? '';
        $reason = $_POST['reason'] ?? '';
        $durationDays = $_POST['duration_days'] ?? null;

        $data = ['action' => $action];
        if ($reason)
            $data['reason'] = $reason;
        if ($durationDays)
            $data['duration_days'] = (int) $durationDays;

        $response = apiRequest('/auth/users/' . $userId . '/manage', 'POST', $data, getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'User ' . $action . ' successfully');
        } else {
            $message = $response['data']['message'] ?? 'Action failed';
            setFlash('danger', $message);
        }

        redirect('/admin/users/' . $userId);
    }

    public function updateUserRole($userId)
    {
        $this->checkAdmin();

        $role = $_POST['role'] ?? '';

        if (!in_array($role, ['user', 'security', 'admin'])) {
            setFlash('danger', 'Invalid role');
            redirect('/admin/users/' . $userId);
        }

        $response = apiRequest('/admin/users/' . $userId . '/role', 'PATCH', [
            'role' => $role
        ], getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'User role updated successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to update role';
            setFlash('danger', $message);
        }

        redirect('/admin/users/' . $userId);
    }

    // ============ ITEM MANAGEMENT ============

    public function lostItems()
    {
        $this->checkSecurity();

        $status = $_GET['status'] ?? '';
        $categoryId = $_GET['category_id'] ?? '';
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;

        $queryParams = ["page={$page}"];
        if ($status)
            $queryParams[] = "status=" . urlencode($status);
        if ($categoryId)
            $queryParams[] = "category_id=" . urlencode($categoryId);
        if ($search)
            $queryParams[] = "search=" . urlencode($search);

        $response = apiRequest('/admin/lost-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $items = $response['data']['data'] ?? [];
        $pagination = $response['data']['pagination'] ?? null;
        $itemType = 'lost';

        // Get categories for filter
        $catResponse = apiRequest('/categories', 'GET');
        $categories = $catResponse['data']['data'] ?? [];

        include __DIR__ . '/../../views/admin/items/index.php';
    }

    public function foundItems()
    {
        $this->checkSecurity();

        $status = $_GET['status'] ?? '';
        $categoryId = $_GET['category_id'] ?? '';
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;

        $queryParams = ["page={$page}"];
        if ($status)
            $queryParams[] = "status=" . urlencode($status);
        if ($categoryId)
            $queryParams[] = "category_id=" . urlencode($categoryId);
        if ($search)
            $queryParams[] = "search=" . urlencode($search);

        $response = apiRequest('/admin/found-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $items = $response['data']['data'] ?? [];
        $pagination = $response['data']['pagination'] ?? null;
        $itemType = 'found';

        // Get categories for filter
        $catResponse = apiRequest('/categories', 'GET');
        $categories = $catResponse['data']['data'] ?? [];

        include __DIR__ . '/../../views/admin/items/index.php';
    }

    public function reviewLostItem($id)
    {
        $this->checkSecurity();

        $action = $_POST['action'] ?? '';
        $rejectionReason = $_POST['rejection_reason'] ?? '';

        // API expects "status": "approved" or "rejected"
        $data = ['status' => ($action === 'approve' ? 'approved' : 'rejected')];
        if ($action === 'reject' && $rejectionReason) {
            $data['rejection_reason'] = $rejectionReason;
        }

        $response = apiRequest('/lost-items/' . $id . '/review', 'PATCH', $data, getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Item ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully');
        } else {
            $message = $response['data']['message'] ?? 'Action failed';
            setFlash('danger', $message);
        }

        redirect('/admin/pending');
    }

    public function reviewFoundItem($id)
    {
        $this->checkSecurity();

        $action = $_POST['action'] ?? '';
        $rejectionReason = $_POST['rejection_reason'] ?? '';

        // API expects "status": "approved" or "rejected"
        $data = ['status' => ($action === 'approve' ? 'approved' : 'rejected')];
        if ($action === 'reject' && $rejectionReason) {
            $data['rejection_reason'] = $rejectionReason;
        }

        $response = apiRequest('/found-items/' . $id . '/review', 'PATCH', $data, getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Item ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully');
        } else {
            $message = $response['data']['message'] ?? 'Action failed';
            setFlash('danger', $message);
        }

        redirect('/admin/pending');
    }

    // ============ CLAIMS MANAGEMENT ============

    public function claims()
    {
        $this->checkSecurityOrAdmin();

        $status = $_GET['status'] ?? 'all';
        $page = $_GET['page'] ?? 1;

        $queryParams = ["page={$page}", "limit=20", "status={$status}"];

        $response = apiRequest('/claims?' . implode('&', $queryParams), 'GET', null, getToken());
        $claims = $response['data']['data'] ?? $response['data'] ?? [];
        $pagination = $response['data']['pagination'] ?? null;

        // Get locations for pickup location filter
        $locResponse = apiRequest('/locations?storage_only=true', 'GET');
        $storageLocations = $locResponse['data']['data'] ?? [];

        include __DIR__ . '/../../views/admin/claims/index.php';
    }

    public function showClaim($id)
    {
        $this->checkSecurityOrAdmin();

        $response = apiRequest('/claims/' . $id, 'GET', null, getToken());

        if ($response['status'] !== 200) {
            setFlash('danger', 'Claim not found');
            redirect('/admin/claims');
        }

        $claim = $response['data']['data'] ?? $response['data'];

        // Get storage locations for scheduling
        $locResponse = apiRequest('/locations?storage_only=true', 'GET');
        $storageLocations = $locResponse['data']['data'] ?? [];

        include __DIR__ . '/../../views/admin/claims/show.php';
    }

    public function approveClaim($id)
    {
        $this->checkSecurity();

        $verificationNotes = $_POST['verification_notes'] ?? '';
        $pickupScheduled = $_POST['pickup_scheduled'] ?? '';

        // Build datetime from separate fields if needed
        if (empty($pickupScheduled)) {
            $pickupDate = $_POST['pickup_date'] ?? '';
            $pickupTime = $_POST['pickup_time'] ?? '10:00';
            if ($pickupDate) {
                $pickupScheduled = $pickupDate . 'T' . $pickupTime . ':00';
            }
        }

        $data = [
            'action' => 'approve',
            'verification_notes' => $verificationNotes
        ];

        if ($pickupScheduled) {
            $data['pickup_scheduled'] = $pickupScheduled;
        }

        $response = apiRequest('/claims/' . $id . '/verify', 'PATCH', $data, getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Claim approved successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to approve claim';
            setFlash('danger', $message);
        }

        redirect('/admin/claims/' . $id);
    }

    public function rejectClaim($id)
    {
        $this->checkSecurity();

        $rejectionReason = $_POST['rejection_reason'] ?? '';

        if (empty($rejectionReason) || strlen($rejectionReason) < 10) {
            setFlash('danger', 'Please provide a detailed reason for rejection (at least 10 characters)');
            redirect('/admin/claims/' . $id);
            return;
        }

        $response = apiRequest('/claims/' . $id . '/verify', 'PATCH', [
            'action' => 'reject',
            'rejection_reason' => $rejectionReason
        ], getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Claim rejected');
        } else {
            $message = $response['data']['message'] ?? 'Failed to reject claim';
            setFlash('danger', $message);
        }

        redirect('/admin/claims/' . $id);
    }

    public function scheduleClaim($id)
    {
        $this->checkSecurity();

        $pickupDate = $_POST['pickup_date'] ?? '';
        $pickupTime = $_POST['pickup_time'] ?? '10:00';

        if (empty($pickupDate)) {
            setFlash('danger', 'Please select a pickup date');
            redirect('/admin/claims/' . $id);
            return;
        }

        $pickupScheduled = $pickupDate . 'T' . $pickupTime . ':00';

        $response = apiRequest('/claims/' . $id . '/schedule', 'PATCH', [
            'pickup_scheduled' => $pickupScheduled
        ], getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Pickup scheduled successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to schedule pickup';
            setFlash('danger', $message);
        }

        redirect('/admin/claims/' . $id);
    }

    public function recordPickup($id)
    {
        $this->checkSecurity();

        $pickedUpByName = $_POST['picked_up_by_name'] ?? '';
        $idPresented = $_POST['id_presented'] ?? '';

        if (empty($pickedUpByName)) {
            setFlash('danger', 'Please enter the name of the person picking up');
            redirect('/admin/claims/' . $id);
            return;
        }

        $data = ['picked_up_by_name' => $pickedUpByName];
        if ($idPresented)
            $data['id_presented'] = $idPresented;

        $response = apiRequest('/claims/' . $id . '/pickup', 'PATCH', $data, getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Pickup recorded successfully! Item has been handed over.');
        } else {
            $message = $response['data']['message'] ?? 'Failed to record pickup';
            setFlash('danger', $message);
        }

        redirect('/admin/claims/' . $id);
    }

    // ============ REPORTS ============

    public function reports()
    {
        $this->checkAdmin();

        // Fetch system health data
        $healthResponse = apiRequest('/health/report', 'GET', null, getToken());
        $health = $healthResponse['data'] ?? [];

        // Also fetch reports data so the combined reports index can render charts
        $catResp = apiRequest('/admin/reports/by-category', 'GET', null, getToken());
        $categoryData = $catResp['data']['data'] ?? [];

        $locResp = apiRequest('/admin/reports/by-location', 'GET', null, getToken());
        $locationData = $locResp['data']['data'] ?? [];

        $trendsResp = apiRequest('/admin/reports/trends?days=30', 'GET', null, getToken());
        $trendsData = $trendsResp['data']['data'] ?? [];

        include __DIR__ . '/../../views/admin/reports/index.php';
    }

    public function reportsByCategory()
    {
        $this->checkAdmin();

        $response = apiRequest('/admin/reports/by-category', 'GET', null, getToken());
        $data = $response['data']['data'] ?? [];

        include __DIR__ . '/../../views/admin/reports/by-category.php';
    }

    public function reportsByLocation()
    {
        $this->checkAdmin();

        $response = apiRequest('/admin/reports/by-location', 'GET', null, getToken());
        $data = $response['data']['data'] ?? [];

        include __DIR__ . '/../../views/admin/reports/by-location.php';
    }

    public function reportsTrends()
    {
        $this->checkAdmin();

        $days = $_GET['days'] ?? 30;

        $response = apiRequest('/admin/reports/trends?days=' . $days, 'GET', null, getToken());
        $data = $response['data']['data'] ?? [];

        include __DIR__ . '/../../views/admin/reports/trends.php';
    }

    // ============ ACTIVITY LOGS ============

    public function notifications()
    {
        $this->checkAdmin();

        $page = $_GET['page'] ?? 1;
        $unreadOnly = isset($_GET['unread_only']) && $_GET['unread_only'] === 'true';

        $queryParams = ["page={$page}", "limit=20"];
        if ($unreadOnly)
            $queryParams[] = "unread_only=true";

        $response = apiRequest('/notifications?' . implode('&', $queryParams), 'GET', null, getToken());
        $notifications = $response['data']['data'] ?? $response['data']['notifications'] ?? [];
        $pagination = $response['data']['pagination'] ?? null;

        include __DIR__ . '/../../views/notifications/index.php';
    }

    public function activityLogs()
    {
        $this->checkAdmin();

        $action = $_GET['action'] ?? '';
        $userId = $_GET['user_id'] ?? '';
        $limit = $_GET['limit'] ?? 50;

        $queryParams = ["limit={$limit}"];
        if ($action)
            $queryParams[] = "action=" . urlencode($action);
        if ($userId)
            $queryParams[] = "user_id=" . urlencode($userId);

        $response = apiRequest('/admin/activity?' . implode('&', $queryParams), 'GET', null, getToken());
        $logs = $response['data']['data'] ?? [];

        include __DIR__ . '/../../views/admin/activity.php';
    }

    // ============ CATEGORIES ============

    public function categories()
    {
        $this->checkAdmin();

        // Token auth shows all categories including inactive
        $response = apiRequest('/categories', 'GET', null, getToken());
        $categories = $response['data']['data'] ?? [];

        include __DIR__ . '/../../views/admin/categories/index.php';
    }

    public function storeCategory()
    {
        $this->checkAdmin();

        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'icon' => $_POST['icon'] ?? ''
        ];

        $response = apiRequest('/categories', 'POST', $data, getToken());

        if ($response['status'] === 201 || $response['status'] === 200) {
            setFlash('success', 'Category created successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to create category';
            setFlash('danger', $message);
        }

        redirect('/admin/categories');
    }

    public function updateCategory($id)
    {
        $this->checkAdmin();

        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'icon' => $_POST['icon'] ?? ''
        ];

        $response = apiRequest('/categories/' . $id, 'PUT', $data, getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Category updated successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to update category';
            setFlash('danger', $message);
        }

        redirect('/admin/categories');
    }

    public function toggleCategory($id)
    {
        $this->checkAdmin();

        $response = apiRequest('/categories/' . $id . '/toggle', 'PATCH', null, getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Category status toggled');
        } else {
            $message = $response['data']['message'] ?? 'Failed to toggle category';
            setFlash('danger', $message);
        }

        redirect('/admin/categories');
    }

    public function deleteCategory($id)
    {
        $this->checkAdmin();

        $response = apiRequest('/categories/' . $id, 'DELETE', null, getToken());

        if ($response['status'] === 200 || $response['status'] === 204) {
            setFlash('success', 'Category deleted successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to delete category';
            setFlash('danger', $message);
        }

        redirect('/admin/categories');
    }

    // ============ LOCATIONS ============

    public function locations()
    {
        $this->checkAdmin();

        // Token auth shows all locations including inactive
        $response = apiRequest('/locations', 'GET', null, getToken());
        $locations = $response['data']['data'] ?? [];

        include __DIR__ . '/../../views/admin/locations/index.php';
    }

    public function storeLocation()
    {
        $this->checkAdmin();

        $data = [
            'name' => $_POST['name'] ?? '',
            'building' => $_POST['building'] ?? '',
            'floor' => $_POST['floor'] ?? '',
            'description' => $_POST['description'] ?? '',
            'is_storage_location' => isset($_POST['is_storage_location'])
        ];

        $response = apiRequest('/locations', 'POST', $data, getToken());

        if ($response['status'] === 201 || $response['status'] === 200) {
            setFlash('success', 'Location created successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to create location';
            setFlash('danger', $message);
        }

        redirect('/admin/locations');
    }

    public function updateLocation($id)
    {
        $this->checkAdmin();

        $data = [
            'name' => $_POST['name'] ?? '',
            'building' => $_POST['building'] ?? '',
            'floor' => $_POST['floor'] ?? '',
            'description' => $_POST['description'] ?? '',
            'is_storage_location' => isset($_POST['is_storage_location']),
            'is_active' => isset($_POST['is_active']) ? 'true' : 'false'
        ];

        $response = apiRequest('/locations/' . $id, 'PUT', $data, getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Location updated successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to update location';
            setFlash('danger', $message);
        }

        redirect('/admin/locations');
    }

    public function toggleLocation($id)
    {
        $this->checkAdmin();

        // Toggle endpoint uses POST method
        $response = apiRequest('/locations/' . $id . '/toggle', 'POST', [], getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Location status toggled');
        } else {
            $message = $response['data']['message'] ?? 'Failed to toggle location';
            setFlash('danger', $message);
        }

        redirect('/admin/locations');
    }

    public function deleteLocation($id)
    {
        $this->checkAdmin();

        $response = apiRequest('/locations/' . $id, 'DELETE', null, getToken());

        if ($response['status'] === 200 || $response['status'] === 204) {
            setFlash('success', 'Location deleted successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to delete location';
            setFlash('danger', $message);
        }

        redirect('/admin/locations');
    }

    public function showLostItem($id)
    {
        $this->checkSecurity();
        $response = apiRequest('/lost-items/' . $id, 'GET', null, getToken());

        if ($response['status'] !== 200) {
            setFlash('danger', 'Item not found');
            redirect('/lost-items');
        }

        $item = $response['data']['data'] ?? $response['data'];

        // Get potential matches if logged in and user owns the item
        $matches = [];
        if (isLoggedIn()) {
            $user = getCurrentUser();
            if (isset($item['user_id']) && $item['user_id'] == $user['id']) {
                $matchResponse = apiRequest('/matches/lost/' . $id, 'GET', null, getToken());
                $matches = $matchResponse['data']['data'] ?? $matchResponse['data'] ?? [];
            }
        }

        include __DIR__ . '/../../views/admin/items/show-lost.php';
    }

    public function showFoundItem($id)
    {
        $this->checkSecurity();
        $response = apiRequest('/found-items/' . $id, 'GET', null, getToken());

        if ($response['status'] !== 200) {
            setFlash('danger', 'Item not found');
            redirect('/found-items');
        }

        $item = $response['data']['data'] ?? $response['data'];

        // Get potential matches if logged in and user owns the item
        $matches = [];
        if (isLoggedIn()) {
            $user = getCurrentUser();
            if (isset($item['user_id']) && $item['user_id'] == $user['id']) {
                $matchResponse = apiRequest('/matches/found/' . $id, 'GET', null, getToken());
                $matches = $matchResponse['data']['data'] ?? $matchResponse['data'] ?? [];
            }
        }

        include __DIR__ . '/../../views/admin/items/show-found.php';
    }
}
