<?php

class ItemController
{

    // ============ LOST ITEMS ============

    public function lostItems()
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 12;
        $status = $_GET['status'] ?? '';
        $category_id = $_GET['category_id'] ?? '';
        $location_id = $_GET['location_id'] ?? '';
        $search = $_GET['search'] ?? '';

        $queryParams = ["page={$page}", "limit={$limit}"];
        if ($status)
            $queryParams[] = "status=" . urlencode($status);
        if ($category_id)
            $queryParams[] = "category_id=" . urlencode($category_id);
        if ($location_id)
            $queryParams[] = "location_id=" . urlencode($location_id);
        if ($search)
            $queryParams[] = "search=" . urlencode($search);

        $endpoint = '/lost-items?' . implode('&', $queryParams);

        $response = apiRequest($endpoint, 'GET', null, getToken());
        // API returns: {"success": true, "data": {"items": [...], "pagination": {...}}}
        // apiRequest returns: ['status' => 200, 'data' => full JSON]
        $responseData = $response['data']['data'] ?? $response['data'] ?? [];
        $items = [
            'data' => $responseData['items'] ?? [],
            'pagination' => $responseData['pagination'] ?? null
        ];

        $filters = [
            'status' => $status,
            'category_id' => $category_id,
            'location_id' => $location_id
        ];

        // Get categories and locations for filters
        $catResponse = apiRequest('/categories?active_only=true', 'GET');
        $categories = $catResponse['data']['data'] ?? $catResponse['data'] ?? [];
        if (isset($categories['success']))
            $categories = [];

        $locResponse = apiRequest('/locations?active_only=true', 'GET');
        $locations = $locResponse['data']['data'] ?? $locResponse['data'] ?? [];

        include __DIR__ . '/../../views/items/lost-items.php';
    }

    public function showLostItem($id)
    {
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

        include __DIR__ . '/../../views/items/show-lost.php';
    }

    public function createLostItem()
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to report a lost item');
            redirect('/login');
        }

        // Get categories and locations
        $catResponse = apiRequest('/categories?active_only=true', 'GET');
        $categories = $catResponse['data']['data'] ?? $catResponse['data'] ?? [];

        $locResponse = apiRequest('/locations?active_only=true', 'GET');
        $locations = $locResponse['data']['data'] ?? $locResponse['data'] ?? [];

        include __DIR__ . '/../../views/items/create-lost.php';
    }

    public function storeLostItem()
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to report a lost item');
            redirect('/login');
        }

        // Build data with correct API field names
        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'last_seen_location_id' => (int) ($_POST['last_seen_location_id'] ?? 0),
            'last_seen_date' => $_POST['last_seen_date'] ?? '',
            'last_seen_time' => $_POST['last_seen_time'] ?? null,
            'unique_identifiers' => $_POST['unique_identifiers'] ?? '',
            'reward_offered' => !empty($_POST['reward_offered']) ? (float) $_POST['reward_offered'] : null,
            'contact_via_email' => isset($_POST['contact_via_email']) ? true : false,
            'contact_via_phone' => isset($_POST['contact_via_phone']) ? true : false,
            'email' => $_POST['email'] ?? null,
            'phone_number' => $_POST['phone_number'] ?? null
        ];

        // Remove null/empty optional fields
        if (empty($data['last_seen_time']))
            unset($data['last_seen_time']);
        if (empty($data['unique_identifiers']))
            unset($data['unique_identifiers']);
        if ($data['reward_offered'] === null)
            unset($data['reward_offered']);
        if (empty($data['email']))
            unset($data['email']);
        if (empty($data['phone_number']))
            unset($data['phone_number']);

        // Handle image uploads with multipart
        if (!empty($_FILES['images']['name'][0])) {
            // For Multer .array('images'), we need to send each file with the same field name
            // CURL handles this with the CURLStringFile postname parameter
            $formData = [];

            // Add all regular fields first
            foreach ($data as $key => $value) {
                if (is_bool($value)) {
                    $formData[$key] = $value ? 'true' : 'false';
                } else {
                    $formData[$key] = $value;
                }
            }

            // Add images using proper multipart array format for Multer .array('images', 5)
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $curlFile = new CURLFile(
                        $tmp_name,
                        $_FILES['images']['type'][$key],
                        $_FILES['images']['name'][$key]
                    );
                    // Multer accepts 'images' field repeated - PHP CURL handles this via indexed keys
                    $formData["images[$key]"] = $curlFile;
                }
            }

            // Use custom multipart request that sends all files with same field name
            $response = apiRequestMultipart('/lost-items', $formData, getToken());
        } else {
            // Even without images, use multipart if the endpoint expects it
            // This ensures consistency with Multer middleware
            $formData = [];
            foreach ($data as $key => $value) {
                if (is_bool($value)) {
                    $formData[$key] = $value ? 'true' : 'false';
                } else {
                    $formData[$key] = $value;
                }
            }
            $response = apiRequestMultipart('/lost-items', $formData, getToken());
        }

        if ($response['status'] === 201 || $response['status'] === 200) {
            setFlash('success', 'Lost item reported successfully! It will be visible after admin approval.');
            redirect('/dashboard/my-lost-items');
        } else {
            // Prepare error message and preserve old input for re-render
            $message = $response['data']['message'] ?? 'Failed to report item';
            $errors = [];
            if (isset($response['data']['errors'])) {
                $errorsArray = is_array($response['data']['errors']) ? $response['data']['errors'] : [$response['data']['errors']];
                $errors = array_map(function ($e) {
                    return is_array($e) ? ($e['message'] ?? json_encode($e)) : $e; }, $errorsArray);
                if (!empty($errors))
                    $message .= ': ' . implode(', ', $errors);
            }

            // Preserve posted values
            $old = $_POST;

            // Re-fetch categories and locations (they were fetched above but ensure available)
            $catResponse = apiRequest('/categories?active_only=true', 'GET');
            $categories = $catResponse['data']['data'] ?? $catResponse['data'] ?? [];
            $locResponse = apiRequest('/locations?active_only=true', 'GET');
            $locations = $locResponse['data']['data'] ?? $locResponse['data'] ?? [];

            // Render the create form with $message, $errors and $old available
            include __DIR__ . '/../../views/items/create-lost.php';
            return;
        }
    }

    public function editLostItem($id)
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to edit an item');
            redirect('/login');
        }

        $response = apiRequest('/lost-items/' . $id, 'GET', null, getToken());

        if ($response['status'] !== 200) {
            setFlash('danger', 'Item not found');
            redirect('/dashboard/my-lost-items');
        }

        $item = $response['data']['data'] ?? $response['data'];
        $itemType = 'lost';

        // Get categories and locations
        $catResponse = apiRequest('/categories?active_only=true', 'GET');
        $categories = $catResponse['data']['data'] ?? $catResponse['data'] ?? [];

        $locResponse = apiRequest('/locations?active_only=true', 'GET');
        $locations = $locResponse['data']['data'] ?? $locResponse['data'] ?? [];

        include __DIR__ . '/../../views/items/edit.php';
    }

    public function updateLostItem($id)
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to update an item');
            redirect('/login');
        }

        // Build data with correct API field names (matching create-lost form)
        $data = [
            'title' => $_POST['title'] ?? $_POST['item_name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'last_seen_location_id' => (int) ($_POST['location_id'] ?? 0),
            'last_seen_date' => $_POST['last_seen_date'] ?? $_POST['date_lost'] ?? '',
            'last_seen_time' => $_POST['last_seen_time'] ?? $_POST['time_lost'] ?? null,
            'unique_identifiers' => $_POST['unique_identifiers'] ?? $_POST['proof_details'] ?? '',
            'reward_offered' => !empty($_POST['reward_offered']) ? (float) $_POST['reward_offered'] : null,
            'contact_via_email' => isset($_POST['contact_via_email']) ? true : false,
            'contact_via_phone' => isset($_POST['contact_via_phone']) ? true : false,
            'email' => $_POST['email'] ?? null,
            'phone_number' => $_POST['phone_number'] ?? null
        ];

        // Remove null/empty optional fields
        if (empty($data['last_seen_time']))
            unset($data['last_seen_time']);
        if (empty($data['unique_identifiers']))
            unset($data['unique_identifiers']);

        // Remove empty optional fields
        if (empty($data['email']))
            unset($data['email']);
        if (empty($data['phone_number']))
            unset($data['phone_number']);

        $response = apiRequest('/lost-items/' . $id, 'PUT', $data, getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Item updated successfully!');
            redirect('/lost-items/' . $id);
        } else {
            $message = $response['data']['message'] ?? 'Failed to update item';
            setFlash('danger', $message);
            redirect('/lost-items/' . $id . '/edit');
        }
    }

    public function deleteLostItem($id)
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to delete an item');
            redirect('/login');
        }

        $response = apiRequest('/lost-items/' . $id, 'DELETE', null, getToken());

        if ($response['status'] === 200 || $response['status'] === 204) {
            setFlash('success', 'Item deleted successfully!');
        } else {
            $message = $response['data']['message'] ?? 'Failed to delete item';
            setFlash('danger', $message);
        }

        redirect('/dashboard/my-lost-items');
    }

    // ============ FOUND ITEMS ============

    public function foundItems()
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 12;
        $status = $_GET['status'] ?? '';
        $category_id = $_GET['category_id'] ?? '';
        $location_id = $_GET['location_id'] ?? '';
        $search = $_GET['search'] ?? '';

        $queryParams = ["page={$page}", "limit={$limit}"];
        if ($status)
            $queryParams[] = "status=" . urlencode($status);
        if ($category_id)
            $queryParams[] = "category_id=" . urlencode($category_id);
        if ($location_id)
            $queryParams[] = "location_id=" . urlencode($location_id);
        if ($search)
            $queryParams[] = "search=" . urlencode($search);

        $endpoint = '/found-items?' . implode('&', $queryParams);

        $response = apiRequest($endpoint, 'GET', null, getToken());
        // API returns: {"success": true, "data": {"items": [...], "pagination": {...}}}
        $responseData = $response['data']['data'] ?? $response['data'] ?? [];
        $items = [
            'data' => $responseData['items'] ?? [],
            'pagination' => $responseData['pagination'] ?? null
        ];

        $filters = [
            'status' => $status,
            'category_id' => $category_id,
            'location_id' => $location_id
        ];

        // Get categories and locations for filters
        $catResponse = apiRequest('/categories?active_only=true', 'GET');
        $categories = $catResponse['data']['data'] ?? $catResponse['data'] ?? [];
        if (isset($categories['success']))
            $categories = [];

        $locResponse = apiRequest('/locations?active_only=true', 'GET');
        $locations = $locResponse['data']['data'] ?? $locResponse['data'] ?? [];
        if (isset($locations['success']))
            $locations = [];

        include __DIR__ . '/../../views/items/found-items.php';
    }

    public function showFoundItem($id)
    {
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

        include __DIR__ . '/../../views/items/show-found.php';
    }

    public function createFoundItem()
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to report a found item');
            redirect('/login');
        }

        // Get categories and locations
        $catResponse = apiRequest('/categories?active_only=true', 'GET');
        $categories = $catResponse['data']['data'] ?? $catResponse['data'] ?? [];

        $locResponse = apiRequest('/locations?active_only=true', 'GET');
        $locations = $locResponse['data']['data'] ?? $locResponse['data'] ?? [];

        include __DIR__ . '/../../views/items/create-found.php';
    }

    public function storeFoundItem()
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to report a found item');
            redirect('/login');
        }

        // Build data with correct API field names
        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'found_location_id' => (int) ($_POST['found_location_id'] ?? 0),
            'found_date' => $_POST['found_date'] ?? '',
            'found_time' => $_POST['found_time'] ?? null,
            'storage_location_id' => !empty($_POST['storage_location_id']) ? (int) $_POST['storage_location_id'] : null,
            'storage_notes' => $_POST['storage_notes'] ?? '',
            'turned_in_to_security' => isset($_POST['turned_in_to_security']) ? true : false,
            'unique_identifiers' => $_POST['unique_identifiers'] ?? '',
            'condition_notes' => $_POST['condition_notes'] ?? ''
        ];

        // Remove null/empty optional fields
        if (empty($data['found_time']))
            unset($data['found_time']);
        if ($data['storage_location_id'] === null)
            unset($data['storage_location_id']);
        if (empty($data['storage_notes']))
            unset($data['storage_notes']);
        if (empty($data['unique_identifiers']))
            unset($data['unique_identifiers']);
        if (empty($data['condition_notes']))
            unset($data['condition_notes']);

        // Handle image uploads with multipart
        if (!empty($_FILES['images']['name'][0])) {
            // For Multer .array('images'), we need to send each file with the same field name
            $formData = [];

            // Add all regular fields first
            foreach ($data as $key => $value) {
                if (is_bool($value)) {
                    $formData[$key] = $value ? 'true' : 'false';
                } else {
                    $formData[$key] = $value;
                }
            }

            // Add images using proper multipart array format for Multer .array('images', 5)
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $curlFile = new CURLFile(
                        $tmp_name,
                        $_FILES['images']['type'][$key],
                        $_FILES['images']['name'][$key]
                    );
                    // Multer accepts 'images' field repeated - PHP CURL handles this via indexed keys
                    $formData["images[$key]"] = $curlFile;
                }
            }

            // Use custom multipart request that sends all files with same field name
            $response = apiRequestMultipart('/found-items', $formData, getToken());
        } else {
            // Even without images, use multipart if the endpoint expects it
            $formData = [];
            foreach ($data as $key => $value) {
                if (is_bool($value)) {
                    $formData[$key] = $value ? 'true' : 'false';
                } else {
                    $formData[$key] = $value;
                }
            }
            $response = apiRequestMultipart('/found-items', $formData, getToken());
        }

        if ($response['status'] === 201 || $response['status'] === 200) {
            setFlash('success', 'Found item reported successfully! It will be visible after admin approval.');
            redirect('/dashboard/my-found-items');
        } else {
            // Prepare error message and preserve old input for re-render
            $message = $response['data']['message'] ?? 'Failed to report item';
            $errors = [];
            if (isset($response['data']['errors'])) {
                $errorsArray = is_array($response['data']['errors']) ? $response['data']['errors'] : [$response['data']['errors']];
                $errors = array_map(function ($e) {
                    return is_array($e) ? ($e['message'] ?? json_encode($e)) : $e; }, $errorsArray);
                if (!empty($errors))
                    $message .= ': ' . implode(', ', $errors);
            }

            // Preserve posted values
            $old = $_POST;

            // Re-fetch categories and locations
            $catResponse = apiRequest('/categories?active_only=true', 'GET');
            $categories = $catResponse['data']['data'] ?? $catResponse['data'] ?? [];
            $locResponse = apiRequest('/locations?active_only=true', 'GET');
            $locations = $locResponse['data']['data'] ?? $locResponse['data'] ?? [];

            // Render the create form with $message, $errors and $old available
            include __DIR__ . '/../../views/items/create-found.php';
            return;
        }
    }

    public function editFoundItem($id)
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to edit an item');
            redirect('/login');
        }

        $response = apiRequest('/found-items/' . $id, 'GET', null, getToken());

        if ($response['status'] !== 200) {
            setFlash('danger', 'Item not found');
            redirect('/dashboard/my-found-items');
        }

        $item = $response['data']['data'] ?? $response['data'];
        $itemType = 'found';

        // Get categories and locations
        $catResponse = apiRequest('/categories?active_only=true', 'GET');
        $categories = $catResponse['data']['data'] ?? $catResponse['data'] ?? [];

        $locResponse = apiRequest('/locations?active_only=true', 'GET');
        $locations = $locResponse['data']['data'] ?? $locResponse['data'] ?? [];

        include __DIR__ . '/../../views/items/edit.php';
    }

    public function updateFoundItem($id)
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to update an item');
            redirect('/login');
        }

        // Accept both new API field names and legacy names with fallback
        $data = [
            'title' => $_POST['title'] ?? $_POST['item_name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'found_date' => $_POST['found_date'] ?? $_POST['date_found'] ?? '',
            'found_location' => $_POST['found_location'] ?? $_POST['location_details'] ?? '',
            'current_location' => $_POST['current_location'] ?? $_POST['turnover_location'] ?? '',
            'proof_details' => $_POST['proof_details'] ?? $_POST['distinctive_features'] ?? ''
        ];

        if (!empty($_POST['location_id'])) {
            $data['location_id'] = (int) $_POST['location_id'];
        }

        $response = apiRequest('/found-items/' . $id, 'PUT', $data, getToken());

        if ($response['status'] === 200) {
            setFlash('success', 'Item updated successfully!');
            redirect('/found-items/' . $id);
        } else {
            $message = $response['data']['message'] ?? 'Failed to update item';
            setFlash('danger', $message);
            redirect('/found-items/' . $id . '/edit');
        }
    }

    public function deleteFoundItem($id)
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to delete an item');
            redirect('/login');
        }

        $response = apiRequest('/found-items/' . $id, 'DELETE', null, getToken());

        if ($response['status'] === 200 || $response['status'] === 204) {
            setFlash('success', 'Item deleted successfully!');
        } else {
            $message = $response['data']['message'] ?? 'Failed to delete item';
            setFlash('danger', $message);
        }

        redirect('/dashboard/my-found-items');
    }
}
