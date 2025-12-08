<?php

class ClaimController {
    
    public function index() {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        $itemId = $_GET['item_id'] ?? '';
        $foundItemId = $_GET['found_item_id'] ?? '';
        
        $queryParams = ["page={$page}", "limit=20"];
        if ($status) $queryParams[] = "status=" . urlencode($status);
        if ($foundItemId) {
            $queryParams[] = "found_item_id=" . urlencode($foundItemId);
        } elseif ($itemId) {
            $queryParams[] = "item_id=" . urlencode($itemId);
        }
        
        $response = apiRequest('/claims?' . implode('&', $queryParams), 'GET', null, getToken());
        $claims = $response['data']['data'] ?? $response['data'] ?? [];
        $pagination = $response['data']['pagination'] ?? null;
        
        include __DIR__ . '/../../views/claims/index.php';
    }
    
    public function show($id) {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $response = apiRequest('/claims/' . $id, 'GET', null, getToken());
        
        if ($response['status'] !== 200) {
            setFlash('danger', 'Claim not found');
            redirect('/claims');
        }
        
        $claim = $response['data']['data'] ?? $response['data'];
        include __DIR__ . '/../../views/claims/show.php';
    }
    
    public function create($foundItemId = null) {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to submit a claim');
            redirect('/login');
        }
        
        // Get found item ID from parameter or query string
        $foundItemId = $foundItemId ?? $_GET['found_item_id'] ?? null;
        
        if (!$foundItemId) {
            setFlash('danger', 'Please select an item to claim');
            redirect('/found-items');
        }
        
        // Get the found item details
        $response = apiRequest('/found-items/' . $foundItemId, 'GET', null, getToken());
        
        if ($response['status'] !== 200) {
            setFlash('danger', 'Item not found');
            redirect('/found-items');
        }
        
        $foundItem = $response['data']['data'] ?? $response['data'];
        include __DIR__ . '/../../views/claims/create.php';
    }
    
    public function store() {
        if (!isLoggedIn()) {
            setFlash('warning', 'Please login to submit a claim');
            redirect('/login');
        }
        
        $foundItemId = $_POST['found_item_id'] ?? '';
        $description = $_POST['description'] ?? '';
        $proofDetails = $_POST['proof_details'] ?? '';
        
        if (empty($foundItemId) || empty($description) || empty($proofDetails)) {
            setFlash('danger', 'Please fill in all required fields');
            redirect('/found-items/' . $foundItemId . '/claim');
        }
        
        $data = [
            'found_item_id' => (int)$foundItemId,
            'description' => $description,
            'proof_details' => $proofDetails
        ];
        
        // Handle image uploads with multipart (same pattern as lost/found items)
        if (!empty($_FILES['images']['name'][0])) {
            $formData = [];
            
            // Add all regular fields first
            foreach ($data as $key => $value) {
                $formData[$key] = $value;
            }
            
            // Add images using proper multipart array format for Multer .array('images', 5)
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $curlFile = new CURLFile(
                        $tmp_name,
                        $_FILES['images']['type'][$key],
                        $_FILES['images']['name'][$key]
                    );
                    $formData["images[$key]"] = $curlFile;
                }
            }
            
            $response = apiRequestMultipart('/claims', $formData, getToken());
        } else {
            $response = apiRequest('/claims', 'POST', $data, getToken());
        }
        
        if ($response['status'] === 201 || $response['status'] === 200) {
            setFlash('success', 'Claim submitted successfully! You will be notified when it is reviewed.');
            redirect('/dashboard/my-claims');
        } else {
            $message = $response['data']['message'] ?? 'Failed to submit claim';
            setFlash('danger', $message);
            redirect('/found-items/' . $foundItemId . '/claim');
        }
    }
    
    public function cancel($id) {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $response = apiRequest('/claims/' . $id . '/cancel', 'PATCH', null, getToken());
        
        if ($response['status'] === 200) {
            setFlash('success', 'Claim cancelled successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to cancel claim';
            setFlash('danger', $message);
        }
        
        redirect('/dashboard/my-claims');
    }
    
    // Finder or Admin/Security actions - Approve claim
    public function verify($id) {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $verificationNotes = $_POST['verification_notes'] ?? '';
        $pickupScheduled = $_POST['pickup_scheduled'] ?? '';
        
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
            $message = $response['data']['message'] ?? 'Failed to verify claim';
            setFlash('danger', $message);
        }
        
        redirect('/claims/' . $id);
    }
    
    // Reject claim
    public function reject($id) {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $rejectionReason = $_POST['rejection_reason'] ?? '';
        
        if (empty($rejectionReason) || strlen($rejectionReason) < 10) {
            setFlash('danger', 'Please provide a detailed reason for rejection (at least 10 characters)');
            redirect('/claims/' . $id);
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
        
        redirect('/claims/' . $id);
    }
    
    public function schedule($id) {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $pickupScheduled = $_POST['pickup_scheduled'] ?? '';
        
        if (empty($pickupScheduled)) {
            // Build datetime from separate fields if needed
            $pickupDate = $_POST['pickup_date'] ?? '';
            $pickupTime = $_POST['pickup_time'] ?? '10:00';
            if ($pickupDate) {
                $pickupScheduled = $pickupDate . 'T' . $pickupTime . ':00';
            }
        }
        
        if (empty($pickupScheduled)) {
            setFlash('danger', 'Please select a pickup date and time');
            redirect('/claims/' . $id);
        }
        
        $response = apiRequest('/claims/' . $id . '/schedule', 'PATCH', [
            'pickup_scheduled' => $pickupScheduled
        ], getToken());
        
        if ($response['status'] === 200) {
            setFlash('success', 'Pickup scheduled successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to schedule pickup';
            setFlash('danger', $message);
        }
        
        redirect('/claims/' . $id);
    }
    
    public function complete($id) {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $response = apiRequest('/claims/' . $id . '/complete', 'PATCH', null, getToken());
        
        if ($response['status'] === 200) {
            setFlash('success', 'Item handover completed successfully!');
        } else {
            $message = $response['data']['message'] ?? 'Failed to complete handover';
            setFlash('danger', $message);
        }
        
        redirect('/claims/' . $id);
    }
    
    public function pickup($id) {
        if (!isSecurity()) {
            setFlash('danger', 'Unauthorized');
            redirect('/dashboard');
        }
        
        $pickedUpByName = $_POST['picked_up_by_name'] ?? '';
        $idPresented = $_POST['id_presented'] ?? '';
        
        if (empty($pickedUpByName)) {
            setFlash('danger', 'Please enter the name of the person picking up');
            redirect('/claims/' . $id);
        }
        
        $data = ['picked_up_by_name' => $pickedUpByName];
        if ($idPresented) $data['id_presented'] = $idPresented;
        
        $response = apiRequest('/claims/' . $id . '/pickup', 'PATCH', $data, getToken());
        
        if ($response['status'] === 200) {
            setFlash('success', 'Pickup recorded successfully');
        } else {
            $message = $response['data']['message'] ?? 'Failed to record pickup';
            setFlash('danger', $message);
        }
        
        redirect('/claims/' . $id);
    }
}
