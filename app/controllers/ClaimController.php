<?php

class ClaimController
{

    public function index()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        $itemId = $_GET['item_id'] ?? '';
        $foundItemId = $_GET['found_item_id'] ?? '';

        $queryParams = ["page={$page}", "limit=20"];
        if ($status)
            $queryParams[] = "status=" . urlencode($status);
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

    public function show($id)
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $response = apiRequest('/claims/' . $id, 'GET', null, getToken());

        if ($response['status'] !== 200) {
            setFlash('danger', 'Claim not found');
            redirect('/claims');
        }

        $claim = $response['data']['data'] ?? $response['data'];

        // If finder metadata is missing, try to enrich from the related found item
        $hasFinderInfo = !empty($claim['finder_school_id']) || !empty($claim['finder_email']) || !empty($claim['finder_contact']) || !empty($claim['finder_user_id']);
        $foundItemId = $claim['found_item_id'] ?? $claim['item_id'] ?? null;
        if (!$hasFinderInfo && $foundItemId) {
            $fiResp = apiRequest('/found-items/' . $foundItemId, 'GET', null, getToken());
            if ($fiResp['status'] === 200) {
                $fi = $fiResp['data']['data'] ?? $fiResp['data'] ?? [];

                // Try nested user object
                if (!empty($fi['user']) && is_array($fi['user'])) {
                    $u = $fi['user'];
                    if (empty($claim['finder_school_id']) && !empty($u['school_id']))
                        $claim['finder_school_id'] = $u['school_id'];
                    if (empty($claim['finder_email']) && !empty($u['email']))
                        $claim['finder_email'] = $u['email'];
                    if (empty($claim['finder_contact']) && !empty($u['contact_number']))
                        $claim['finder_contact'] = $u['contact_number'];
                    if (empty($claim['finder_user_id']) && !empty($u['id']))
                        $claim['finder_user_id'] = $u['id'];
                }

                // Try flattened reporter/finder fields on found item
                if (empty($claim['finder_school_id'])) {
                    $claim['finder_school_id'] = $fi['reporter_school_id'] ?? $fi['found_by_school'] ?? $fi['school_id'] ?? $claim['finder_school_id'] ?? null;
                }
                if (empty($claim['finder_email'])) {
                    $claim['finder_email'] = $fi['reporter_email'] ?? $fi['found_by_email'] ?? null;
                }
                if (empty($claim['finder_contact'])) {
                    $claim['finder_contact'] = $fi['reporter_contact'] ?? $fi['found_by_contact'] ?? null;
                }
                if (empty($claim['finder_user_id'])) {
                    $claim['finder_user_id'] = $fi['reporter_id'] ?? $fi['found_by_id'] ?? $fi['user_id'] ?? null;
                }
                // Also surface some found-item root fields onto the claim object so views using root fallbacks pick them up
                if (empty($claim['school_id']) && !empty($fi['school_id']))
                    $claim['school_id'] = $fi['school_id'];
                if (empty($claim['email']) && !empty($fi['email']))
                    $claim['email'] = $fi['email'];
                if (empty($claim['user_id']) && !empty($fi['user_id']))
                    $claim['user_id'] = $fi['user_id'];
                if (empty($claim['first_name']) && !empty($fi['first_name']))
                    $claim['first_name'] = $fi['first_name'];
                if (empty($claim['last_name']) && !empty($fi['last_name']))
                    $claim['last_name'] = $fi['last_name'];
                // attach the raw found item for debugging or deeper view usage
                $claim['found_item'] = $fi;
            }
        }

        // If we have a finder user id but still missing specific fields, fetch the user profile
        $finderUid = $claim['finder_user_id'] ?? null;
        if (!empty($finderUid) && (empty($claim['finder_school_id']) || empty($claim['finder_email']) || empty($claim['finder_contact']))) {
            $uResp = apiRequest('/users/' . $finderUid, 'GET', null, getToken());
            if ($uResp['status'] === 200) {
                $u = $uResp['data']['data'] ?? $uResp['data'] ?? [];
                if (empty($claim['finder_school_id']) && !empty($u['school_id']))
                    $claim['finder_school_id'] = $u['school_id'];
                if (empty($claim['finder_email']) && !empty($u['email']))
                    $claim['finder_email'] = $u['email'];
                if (empty($claim['finder_contact']) && !empty($u['contact_number']))
                    $claim['finder_contact'] = $u['contact_number'];
            }
        }
        include __DIR__ . '/../../views/claims/show.php';
    }

    public function create($foundItemId = null)
    {
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

    public function store()
    {
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
            'found_item_id' => (int) $foundItemId,
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

    public function cancel($id)
    {
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
    public function verify($id)
    {
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
    public function reject($id)
    {
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

    public function schedule($id)
    {
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

    public function complete($id)
    {
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

    public function pickup($id)
    {
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
        if ($idPresented)
            $data['id_presented'] = $idPresented;

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
