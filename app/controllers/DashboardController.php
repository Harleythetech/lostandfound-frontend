<?php

class DashboardController
{

    public function index()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $response = apiRequest('/dashboard', 'GET', null, getToken());
        $dashboard = $response['data']['data'] ?? $response['data'] ?? [];

        // If API returned top-level stats (lost_items / found_items / claims / notifications), prefer them
        if (isset($dashboard['lost_items']) || isset($dashboard['found_items']) || isset($dashboard['claims']) || isset($dashboard['notifications'])) {
            // Normalize API top-level stats into a `stats` array expected by views
            if (!isset($dashboard['stats']) || !is_array($dashboard['stats'])) {
                $dashboard['stats'] = $dashboard;
            }
            // Keep `matches` in stats — frontend has custom logic but may still
            // rely on API-provided matches in some contexts. Do not remove it here.
        }

        // Get search/filter parameters
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $location = $_GET['location'] ?? '';
        $tab = $_GET['tab'] ?? 'lost';

        // Build query params for items
        $queryParams = ['page=1', 'limit=12', 'status=approved'];
        if ($search)
            $queryParams[] = "search=" . urlencode($search);
        if ($category)
            $queryParams[] = "category_id=" . urlencode($category);
        if ($location)
            $queryParams[] = "location_id=" . urlencode($location);

        // Fetch lost items using correct endpoint
        $lostResponse = apiRequest('/lost-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $lostData = $lostResponse['data']['data'] ?? $lostResponse['data'] ?? [];
        $lostItems = $lostData['items'] ?? $lostData ?? [];

        // Fetch found items using correct endpoint
        $foundResponse = apiRequest('/found-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $foundData = $foundResponse['data']['data'] ?? $foundResponse['data'] ?? [];
        $foundItems = $foundData['items'] ?? $foundData ?? [];

        // Ensure dashboard stats include counts for lost/found items when API doesn't provide them
        try {
            if (!isset($dashboard['stats']) || !is_array($dashboard['stats'])) {
                $dashboard['stats'] = [];
            }

            // Prefer pagination totals if available
            $lostCount = 0;
            if (isset($lostData['pagination']['total'])) {
                $lostCount = (int) $lostData['pagination']['total'];
            } elseif (isset($lostData['total'])) {
                $lostCount = (int) $lostData['total'];
            } elseif (is_array($lostItems)) {
                $lostCount = count($lostItems);
            }

            $foundCount = 0;
            if (isset($foundData['pagination']['total'])) {
                $foundCount = (int) $foundData['pagination']['total'];
            } elseif (isset($foundData['total'])) {
                $foundCount = (int) $foundData['total'];
            } elseif (is_array($foundItems)) {
                $foundCount = count($foundItems);
            }

            $dashboard['stats']['lost_items'] = $dashboard['stats']['lost_items'] ?? $lostCount;
            $dashboard['stats']['found_items'] = $dashboard['stats']['found_items'] ?? $foundCount;
        } catch (Exception $e) {
            // ignore and let view fall back to zero
        }



        // Fetch categories and locations for filters
        $categoriesResponse = apiRequest('/categories?active_only=true', 'GET');
        $categories = $categoriesResponse['data']['data'] ?? $categoriesResponse['data'] ?? [];
        if (isset($categories['success']) || !is_array($categories))
            $categories = [];

        $locationsResponse = apiRequest('/locations?active_only=true', 'GET');
        $locations = $locationsResponse['data']['data'] ?? $locationsResponse['data'] ?? [];
        if (isset($locations['success']) || !is_array($locations))
            $locations = [];

        $filters = [
            'search' => $search,
            'category' => $category,
            'location' => $location,
            'tab' => $tab
        ];

        // --- Fetch my matches briefly to compute a filtered matches count
        $matchesCount = null;
        try {
            $matchesResp = apiRequest('/dashboard/my-matches?page=1&limit=1000', 'GET', null, getToken());
            $matchesData = $matchesResp['data'] ?? [];
            $matchesList = $matchesData['data'] ?? $matchesData;
            if (!is_array($matchesList))
                $matchesList = [];

            // Apply same filtering logic as the `my-matches` view
            $filtered = array_filter($matchesList, function ($match) {
                $found = $match['found_item'] ?? null;
                $status = strtolower((string) ($match['found_item']['status'] ?? $match['found_item_status'] ?? $found['status'] ?? $match['status'] ?? ''));
                $excludeStatuses = ['completed', 'claimed', 'returned', 'reserved', 'unavailable', 'resolved'];
                if (in_array($status, $excludeStatuses, true)) {
                    return false;
                }
                $matchStatus = strtolower((string) ($match['status'] ?? ''));
                if ($matchStatus === 'dismissed')
                    return false;
                $flagFields = [
                    $found['is_claimed'] ?? null,
                    $found['claimed'] ?? null,
                    $match['is_claimed'] ?? null,
                    $match['claimed'] ?? null,
                ];
                foreach ($flagFields as $f) {
                    if ($f === true || $f === 1 || $f === '1' || $f === 'true')
                        return false;
                }
                if (!empty($found['claimed_at']) || !empty($found['picked_up_at']) || !empty($match['picked_up_at']) || !empty($match['claimed_at'])) {
                    return false;
                }
                return true;
            });
            $matchesCount = is_array($filtered) ? count($filtered) : 0;
        } catch (Exception $e) {
            $matchesCount = null;
        }

        // --- Ensure we have a claims count for the dashboard stats
        try {
            // Only fetch if backend didn't already provide it
            $providedClaims = $dashboard['stats']['my_claims'] ?? $dashboard['my_claims'] ?? null;
            if (empty($providedClaims)) {
                $claimsResp = apiRequest('/dashboard/my-claims?page=1&limit=1', 'GET', null, getToken());
                $claimsData = $claimsResp['data'] ?? $claimsResp;
                // Look for pagination total or data count
                $count = 0;
                if (isset($claimsData['pagination']['total'])) {
                    $count = (int) $claimsData['pagination']['total'];
                } elseif (isset($claimsData['total'])) {
                    $count = (int) $claimsData['total'];
                } elseif (is_array($claimsData)) {
                    // If endpoint returned a list directly
                    $list = $claimsResp['data']['data'] ?? $claimsResp['data'] ?? null;
                    if (is_array($list)) {
                        $count = count($list);
                    }
                }
                // Normalize into dashboard stats structure expected by the view
                if (!isset($dashboard['stats']) || !is_array($dashboard['stats']))
                    $dashboard['stats'] = [];
                $dashboard['stats']['my_claims'] = $count;
            }
        } catch (Exception $e) {
            // ignore and let view fall back to zero
        }

        include __DIR__ . '/../../views/dashboard/index.php';
    }

    public function profile()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        // Fetch full profile from API
        $response = apiRequest('/auth/me', 'GET', null, getToken());



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

    public function updateProfile()
    {
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

    public function changePassword()
    {
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

    public function activity()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $response = apiRequest('/dashboard/activity', 'GET', null, getToken());
        $activities = $response['data']['data'] ?? [];

        include __DIR__ . '/../../views/dashboard/activity.php';
    }

    public function myLostItems()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        $queryParams = ["page={$page}"];
        if ($status)
            $queryParams[] = "status=" . urlencode($status);
        if ($search)
            $queryParams[] = "search=" . urlencode($search);

        $response = apiRequest('/dashboard/my-lost-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $items = $response['data'] ?? [];

        $filters = [
            'status' => $status,
            'search' => $search
        ];

        include __DIR__ . '/../../views/dashboard/my-lost-items.php';
    }

    public function myFoundItems()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        $queryParams = ["page={$page}"];
        if ($status)
            $queryParams[] = "status=" . urlencode($status);
        if ($search)
            $queryParams[] = "search=" . urlencode($search);

        $response = apiRequest('/dashboard/my-found-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $items = $response['data'] ?? [];

        $filters = [
            'status' => $status,
            'search' => $search
        ];

        include __DIR__ . '/../../views/dashboard/my-found-items.php';
    }

    public function myClaims()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';

        $queryParams = ["page={$page}"];
        if ($status)
            $queryParams[] = "status=" . urlencode($status);

        $response = apiRequest('/dashboard/my-claims?' . implode('&', $queryParams), 'GET', null, getToken());
        $claims = $response['data'] ?? [];

        include __DIR__ . '/../../views/dashboard/my-claims.php';
    }

    public function claimsOnMyItems()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';

        $queryParams = ["page={$page}"];
        if ($status)
            $queryParams[] = "status=" . urlencode($status);

        $response = apiRequest('/dashboard/claims-on-my-items?' . implode('&', $queryParams), 'GET', null, getToken());
        $claims = $response['data']['data'] ?? [];
        $pagination = $response['data']['pagination'] ?? null;

        include __DIR__ . '/../../views/dashboard/claims-on-items.php';
    }

    public function myMatches()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $page = $_GET['page'] ?? 1;
        $type = $_GET['type'] ?? '';

        $queryParams = ["page={$page}"];
        if ($type)
            $queryParams[] = "type=" . urlencode($type);

        $response = apiRequest('/dashboard/my-matches?' . implode('&', $queryParams), 'GET', null, getToken());
        $matches = $response['data'] ?? [];

        include __DIR__ . '/../../views/dashboard/my-matches.php';
    }
}
