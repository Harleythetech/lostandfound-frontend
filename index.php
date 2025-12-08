<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/PageController.php';
require_once __DIR__ . '/app/controllers/DashboardController.php';
require_once __DIR__ . '/app/controllers/ItemController.php';
require_once __DIR__ . '/app/controllers/ClaimController.php';
require_once __DIR__ . '/app/controllers/NotificationController.php';
require_once __DIR__ . '/app/controllers/SearchController.php';
require_once __DIR__ . '/app/controllers/AdminController.php';

$router = new Router();

// ============ PUBLIC ROUTES ============
$router->get('/', [new PageController(), 'home']);
$router->get('/about', [new PageController(), 'about']);
$router->get('/contact', [new PageController(), 'contact']);
$router->get('/health', [new PageController(), 'health']);

// ============ AUTH ROUTES ============
$router->get('/login', [new AuthController(), 'showLogin']);
$router->post('/login', [new AuthController(), 'login']);
$router->get('/register', [new AuthController(), 'showRegister']);
$router->post('/register', [new AuthController(), 'register']);
$router->get('/logout', [new AuthController(), 'logout']);
$router->post('/auth/firebase/callback', [new AuthController(), 'firebaseCallback']);
$router->post('/auth/firebase/login', [new AuthController(), 'firebaseLogin']);
$router->post('/auth/firebase/link', [new AuthController(), 'firebaseLink']);
$router->post('/auth/firebase/register', [new AuthController(), 'firebaseRegister']);
$router->get('/forgot-password', [new AuthController(), 'showForgotPassword']);
$router->post('/forgot-password', [new AuthController(), 'forgotPassword']);
$router->get('/reset-password', [new AuthController(), 'showResetPasswordWithQuery']);
$router->get('/reset-password/{token}', [new AuthController(), 'showResetPassword']);
$router->post('/reset-password', [new AuthController(), 'resetPassword']);

// ============ DASHBOARD ROUTES ============
$router->get('/dashboard', [new DashboardController(), 'index']);
$router->get('/dashboard/profile', [new DashboardController(), 'profile']);
$router->post('/dashboard/profile', [new DashboardController(), 'updateProfile']);
$router->post('/dashboard/profile/password', [new DashboardController(), 'changePassword']);
$router->get('/dashboard/activity', [new DashboardController(), 'activity']);
$router->get('/dashboard/my-lost-items', [new DashboardController(), 'myLostItems']);
$router->get('/dashboard/my-found-items', [new DashboardController(), 'myFoundItems']);
$router->get('/dashboard/my-claims', [new DashboardController(), 'myClaims']);
$router->get('/dashboard/claims-on-my-items', [new DashboardController(), 'claimsOnMyItems']);
$router->get('/dashboard/my-matches', [new DashboardController(), 'myMatches']);

// ============ LOST ITEMS ROUTES ============
$router->get('/lost-items', [new ItemController(), 'lostItems']);
$router->get('/lost-items/create', [new ItemController(), 'createLostItem']);
$router->post('/lost-items', [new ItemController(), 'storeLostItem']);
$router->get('/lost-items/{id}', [new ItemController(), 'showLostItem']);
$router->get('/lost-items/{id}/edit', [new ItemController(), 'editLostItem']);
$router->post('/lost-items/{id}/update', [new ItemController(), 'updateLostItem']);
$router->post('/lost-items/{id}/delete', [new ItemController(), 'deleteLostItem']);

// ============ FOUND ITEMS ROUTES ============
$router->get('/found-items', [new ItemController(), 'foundItems']);
$router->get('/found-items/create', [new ItemController(), 'createFoundItem']);
$router->post('/found-items', [new ItemController(), 'storeFoundItem']);
$router->get('/found-items/{id}', [new ItemController(), 'showFoundItem']);
$router->get('/found-items/{id}/edit', [new ItemController(), 'editFoundItem']);
$router->post('/found-items/{id}/update', [new ItemController(), 'updateFoundItem']);
$router->post('/found-items/{id}/delete', [new ItemController(), 'deleteFoundItem']);
$router->get('/found-items/{id}/claim', [new ClaimController(), 'create']);

// ============ CLAIMS ROUTES ============
$router->get('/claims', [new ClaimController(), 'index']);
$router->post('/claims', [new ClaimController(), 'store']);
$router->get('/claims/create', [new ClaimController(), 'create']);
$router->get('/claims/{id}', [new ClaimController(), 'show']);
$router->post('/claims/{id}/cancel', [new ClaimController(), 'cancel']);
$router->post('/claims/{id}/verify', [new ClaimController(), 'verify']);
$router->post('/claims/{id}/reject', [new ClaimController(), 'reject']);
$router->post('/claims/{id}/schedule', [new ClaimController(), 'schedule']);
$router->post('/claims/{id}/complete', [new ClaimController(), 'complete']);
$router->post('/claims/{id}/pickup', [new ClaimController(), 'pickup']);

// ============ NOTIFICATIONS ROUTES ============
$router->get('/notifications', [new NotificationController(), 'index']);
$router->get('/notifications/unread-count', [new NotificationController(), 'unreadCount']);
$router->get('/notifications/preferences', [new NotificationController(), 'preferences']);
$router->post('/notifications/preferences', [new NotificationController(), 'updatePreferences']);
$router->post('/notifications/mark-all-read', [new NotificationController(), 'markAllAsRead']);
$router->post('/notifications/clear-read', [new NotificationController(), 'clearRead']);
$router->get('/notifications/{id}/read', [new NotificationController(), 'markAsRead']);
$router->post('/notifications/{id}/read', [new NotificationController(), 'markAsRead']);
$router->post('/notifications/{id}/delete', [new NotificationController(), 'delete']);

// ============ API ROUTES (JSON) ============
$router->get('/api/notifications', [new NotificationController(), 'apiList']);

// ============ SEARCH ROUTES ============
$router->get('/search', [new SearchController(), 'index']);
$router->get('/search/lost', [new SearchController(), 'lost']);
$router->get('/search/found', [new SearchController(), 'found']);

// ============ ADMIN ROUTES ============
$router->get('/admin', [new AdminController(), 'dashboard']);
$router->get('/admin/dashboard', [new AdminController(), 'dashboard']);
$router->get('/admin/pending', [new AdminController(), 'pending']);
// Admin profile
$router->get('/admin/profile', [new AdminController(), 'profile']);

// Admin - Users
$router->get('/admin/users', [new AdminController(), 'users']);
$router->get('/admin/users/{id}', [new AdminController(), 'showUser']);
$router->post('/admin/users/{id}/manage', [new AdminController(), 'manageUser']);
$router->post('/admin/users/{id}/role', [new AdminController(), 'updateUserRole']);

// Admin - Items
$router->get('/admin/lost-items', [new AdminController(), 'lostItems']);
$router->get('/admin/found-items', [new AdminController(), 'foundItems']);
$router->post('/admin/lost-items/{id}/review', [new AdminController(), 'reviewLostItem']);
$router->post('/admin/found-items/{id}/review', [new AdminController(), 'reviewFoundItem']);
$router->get('/admin/found-items/{id}', [new AdminController(), 'showFoundItem']);
$router->get('/admin/lost-items/{id}', [new AdminController(), 'showLostItem']);

// Admin - Claims
$router->get('/admin/claims', [new AdminController(), 'claims']);
$router->get('/admin/claims/{id}', [new AdminController(), 'showClaim']);
$router->post('/admin/claims/{id}/approve', [new AdminController(), 'approveClaim']);
$router->post('/admin/claims/{id}/reject', [new AdminController(), 'rejectClaim']);
$router->post('/admin/claims/{id}/schedule', [new AdminController(), 'scheduleClaim']);
$router->post('/admin/claims/{id}/pickup', [new AdminController(), 'recordPickup']);

// Admin - Reports
$router->get('/admin/reports', [new AdminController(), 'reports']);
$router->get('/admin/reports/by-category', [new AdminController(), 'reportsByCategory']);
$router->get('/admin/reports/by-location', [new AdminController(), 'reportsByLocation']);
$router->get('/admin/reports/trends', [new AdminController(), 'reportsTrends']);

// Admin - Activity Logs
$router->get('/admin/activity', [new AdminController(), 'activityLogs']);
// Admin - Notifications
$router->get('/admin/notifications', [new AdminController(), 'notifications']);

// Admin - Categories
$router->get('/admin/categories', [new AdminController(), 'categories']);
$router->post('/admin/categories', [new AdminController(), 'storeCategory']);
$router->post('/admin/categories/{id}/update', [new AdminController(), 'updateCategory']);
$router->post('/admin/categories/{id}/toggle', [new AdminController(), 'toggleCategory']);
$router->post('/admin/categories/{id}/delete', [new AdminController(), 'deleteCategory']);

// Admin - Locations
$router->get('/admin/locations', [new AdminController(), 'locations']);
$router->post('/admin/locations', [new AdminController(), 'storeLocation']);
$router->post('/admin/locations/{id}/update', [new AdminController(), 'updateLocation']);
$router->post('/admin/locations/{id}/toggle', [new AdminController(), 'toggleLocation']);
$router->post('/admin/locations/{id}/delete', [new AdminController(), 'deleteLocation']);

$router->resolve();
