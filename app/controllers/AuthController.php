<?php

class AuthController
{

    public function showLogin()
    {
        if (isLoggedIn()) {
            if (isAdmin()) {
                redirect('/admin');
            } else {
                redirect('/dashboard');
            }
        }
        include __DIR__ . '/../../views/auth/login.php';
    }

    public function login()
    {
        $school_id = $_POST['school_id'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($school_id) || empty($password)) {
            setFlash('danger', 'Please fill in all fields');
            redirect('/login');
        }

        $response = apiRequest('/auth/login', 'POST', [
            'school_id' => $school_id,
            'password' => $password
        ]);

        if ($response['status'] === 200 && isset($response['data']['data']['accessToken'])) {
            $_SESSION['accessToken'] = $response['data']['data']['accessToken'];
            $_SESSION['refreshToken'] = $response['data']['data']['refreshToken'] ?? null;
            $_SESSION['user'] = $response['data']['data']['user'];
            setFlash('success', 'Welcome back!');

            // Redirect based on role
            $user = $response['data']['data']['user'];
            if (($user['role'] ?? '') === 'admin') {
                redirect('/admin');
            } else {
                redirect('/dashboard');
            }
        } else {
            $message = $response['data']['message'] ?? 'Invalid credentials';
            setFlash('danger', $message);
            redirect('/login');
        }
    }

    /**
     * Handle Firebase authentication callback (AJAX)
     * Creates PHP session after successful Firebase auth on backend
     */
    public function firebaseCallback()
    {
        header('Content-Type: application/json');

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['accessToken']) || !isset($input['user'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }

        // Set session from the API response
        $_SESSION['accessToken'] = $input['accessToken'];
        $_SESSION['refreshToken'] = $input['refreshToken'] ?? null;
        $_SESSION['user'] = $input['user'];

        // Determine redirect based on role
        $redirectPath = (($input['user']['role'] ?? '') === 'admin') ? '/admin' : '/dashboard';

        echo json_encode([
            'success' => true,
            'redirect' => APP_URL . $redirectPath
        ]);
    }

    /**
     * Proxy Firebase login request to backend API (avoids CORS)
     */
    public function firebaseLogin()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['firebase_token'])) {
            echo json_encode(['success' => false, 'message' => 'Firebase token required']);
            return;
        }

        $response = apiRequest('/auth/firebase/login', 'POST', [
            'firebase_token' => $input['firebase_token']
        ]);

        echo json_encode($response['data']);
    }

    /**
     * Proxy Firebase link request to backend API (avoids CORS)
     */
    public function firebaseLink()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['firebase_token']) || !isset($input['school_id']) || !isset($input['password'])) {
            echo json_encode(['success' => false, 'message' => 'All fields required']);
            return;
        }

        $response = apiRequest('/auth/firebase/link', 'POST', [
            'firebase_token' => $input['firebase_token'],
            'school_id' => $input['school_id'],
            'password' => $input['password']
        ]);

        echo json_encode($response['data']);
    }

    /**
     * Proxy Firebase register request to backend API (avoids CORS)
     */
    public function firebaseRegister()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['firebase_token'])) {
            echo json_encode(['success' => false, 'message' => 'Firebase token required']);
            return;
        }

        $response = apiRequest('/auth/firebase/register', 'POST', $input);

        echo json_encode($response['data']);
    }

    public function showRegister()
    {
        if (isLoggedIn()) {
            if (isAdmin()) {
                redirect('/admin');
            } else {
                redirect('/dashboard');
            }
        }
        include __DIR__ . '/../../views/auth/register.php';
    }

    public function register()
    {
        // Validate required fields (using form field names)
        $required = [
            'school_id',
            'email',
            'password',
            'first_name',
            'last_name',
            'contact_number',
            'date_of_birth',
            'gender',
            'address_line1',
            'city',
            'province',
            'postal_code',
            'emergency_contact_name',
            'emergency_contact_number',
            // backend does not expect emergency_contact_relationship - omit
        ];

        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                setFlash('danger', 'Please fill in all required fields');
                redirect('/register');
            }
        }

        if ($_POST['password'] !== $_POST['confirm_password']) {
            setFlash('danger', 'Passwords do not match');
            redirect('/register');
        }

        // Map form fields to API field names
        $data = [
            'school_id' => $_POST['school_id'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'contact_number' => $_POST['contact_number'],
            'date_of_birth' => $_POST['date_of_birth'],
            'gender' => $_POST['gender'],
            'address_line1' => $_POST['address_line1'],
            'address_line2' => $_POST['address_line2'] ?? '',
            'city' => $_POST['city'],
            'province' => $_POST['province'],
            'postal_code' => $_POST['postal_code'],
            'emergency_contact_name' => $_POST['emergency_contact_name'],
            'emergency_contact_number' => $_POST['emergency_contact_number'],
            'department' => $_POST['department'] ?? '',
            'year_level' => $_POST['year_level'] ?? ''
        ];

        $response = apiRequest('/auth/register', 'POST', $data);

        if ($response['status'] === 201 || $response['status'] === 200) {
            setFlash('success', 'Registration successful! Please wait for admin approval, then login.');
            redirect('/login');
        } else {
            $message = $response['data']['message'] ?? 'Registration failed';
            if (isset($response['data']['errors'])) {
                $errorsData = $response['data']['errors'];

                // Recursive flattener for nested error structures
                $flattenErrors = function ($data) use (&$flattenErrors) {
                    if (!is_array($data))
                        return (string) $data;
                    $parts = [];
                    foreach ($data as $k => $v) {
                        if (is_array($v)) {
                            $parts[] = $flattenErrors($v);
                        } else {
                            $parts[] = (string) $v;
                        }
                    }
                    return implode(', ', $parts);
                };

                $errorsStr = is_array($errorsData) ? $flattenErrors($errorsData) : (string) $errorsData;
                $message .= ': ' . $errorsStr;
            }
            setFlash('danger', $message);
            redirect('/register');
        }
    }

    public function logout()
    {
        // Call API logout
        apiRequest('/auth/logout', 'POST', null, getToken());

        session_destroy();
        session_start();
        setFlash('success', 'You have been logged out');
        redirect('/login');
    }

    public function showForgotPassword()
    {
        include __DIR__ . '/../../views/auth/forgot-password.php';
    }

    public function forgotPassword()
    {
        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            setFlash('danger', 'Please enter your email');
            redirect('/forgot-password');
        }

        $response = apiRequest('/auth/forgot-password', 'POST', ['email' => $email]);

        // Always show success to prevent email enumeration
        setFlash('success', 'If an account with that email exists, a password reset link has been sent.');
        redirect('/login');
    }

    public function showResetPasswordWithQuery()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            setFlash('danger', 'Invalid reset link');
            redirect('/login');
        }

        $this->showResetPassword($token);
    }

    public function showResetPassword($token)
    {
        // Verify token first
        $response = apiRequest('/auth/reset-password/' . $token, 'GET');

        if ($response['status'] !== 200) {
            setFlash('danger', 'Invalid or expired reset token');
            redirect('/login');
        }

        include __DIR__ . '/../../views/auth/reset-password.php';
    }

    public function resetPassword()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password)) {
            setFlash('danger', 'Please fill in all fields');
            redirect('/login');
        }

        if ($password !== $confirmPassword) {
            setFlash('danger', 'Passwords do not match');
            redirect('/reset-password/' . $token);
        }

        $response = apiRequest('/auth/reset-password', 'POST', [
            'token' => $token,
            'new_password' => $password
        ]);

        if ($response['status'] === 200) {
            setFlash('success', 'Password reset successful! Please login.');
            redirect('/login');
        } else {
            $message = $response['data']['message'] ?? 'Password reset failed';
            setFlash('danger', $message);
            redirect('/login');
        }
    }
}
