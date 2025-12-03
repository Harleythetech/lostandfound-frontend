# Lost and Found API - Complete Endpoint Reference

> **Base URL:** `http://localhost:8080/api`  
> **Authentication:** Bearer Token (JWT)  
> **Content-Type:** `application/json` (unless noted otherwise)  
> **Total Endpoints:** 92

---

## Table of Contents

1. [Authentication](#authentication) (12 endpoints)
2. [User Dashboard](#user-dashboard) (10 endpoints)
3. [Lost Items](#lost-items) (6 endpoints)
4. [Found Items](#found-items) (6 endpoints)
5. [Claims System](#claims-system) (8 endpoints)
6. [Notifications](#notifications) (9 endpoints)
7. [Search](#search) (3 endpoints)
8. [Categories](#categories) (6 endpoints)
9. [Locations](#locations) (6 endpoints)
10. [Matches](#matches) (8 endpoints)
11. [Admin Dashboard](#admin-dashboard) (11 endpoints)
12. [Health Check](#health-check) (6 endpoints)
13. [Error Responses](#error-responses)

---

## Authentication

### 1. Register User

```
POST /auth/register
```

**Auth Required:** No

**Body:**
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| school_id | string | ✅ | Format: `XX-XXXXX` (e.g., `20-12345`) |
| email | string | ✅ | Valid email, must be unique |
| password | string | ✅ | Min 8 chars, uppercase, lowercase, number, special char |
| first_name | string | ✅ | 2-100 chars, letters only |
| last_name | string | ✅ | 2-100 chars, letters only |
| contact_number | string | ✅ | PH format: `09XXXXXXXXX` or `+639XXXXXXXXX` |
| date_of_birth | string | ✅ | `YYYY-MM-DD`, must be 13+ years old |
| gender | string | ✅ | `male`, `female`, `other`, `prefer_not_to_say` |
| address_line1 | string | ✅ | Max 255 chars |
| address_line2 | string | ❌ | Max 255 chars |
| city | string | ✅ | Max 100 chars |
| province | string | ✅ | Max 100 chars |
| postal_code | string | ✅ | Max 20 chars |
| emergency_contact_name | string | ✅ | Max 200 chars |
| emergency_contact_number | string | ✅ | Valid phone format |
| department | string | ❌ | Max 100 chars |
| year_level | string | ❌ | Max 50 chars |

---

### 2. Login

```
POST /auth/login
```

**Auth Required:** No

**Body:**
| Field | Type | Required |
|-------|------|----------|
| school_id | string | ✅ |
| password | string | ✅ |

**Response:**

```json
{
  "success": true,
  "data": {
    "accessToken": "jwt_token",
    "refreshToken": "refresh_token",
    "user": { ... }
  }
}
```

---

### 3. Get Current User Profile

```
GET /auth/me
```

**Auth Required:** Yes

---

### 4. Logout

```
POST /auth/logout
```

**Auth Required:** Yes

---

### 5. Forgot Password

```
POST /auth/forgot-password
```

**Auth Required:** No

**Body:**
| Field | Type | Required |
|-------|------|----------|
| email | string | ✅ |

---

### 6. Verify Reset Token

```
GET /auth/reset-password/:token
```

**Auth Required:** No

**Parameters:**
| Param | Type | Validation |
|-------|------|------------|
| token | string | 64 character hex string |

---

### 7. Reset Password

```
POST /auth/reset-password
```

**Auth Required:** No

**Body:**
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| token | string | ✅ | 64 char hex string |
| new_password | string | ✅ | 8-128 chars, uppercase, lowercase, number, special char |

---

### 8. Firebase/OAuth Register

```
POST /auth/firebase/register
```

**Auth Required:** No (Firebase ID Token in body)

**Body:**
| Field | Type | Required |
|-------|------|----------|
| firebase_token | string | ✅ |
| school_id | string | ✅ |
| first_name | string | ✅ |
| last_name | string | ✅ |
| contact_number | string | ❌ |

---

### 9. Firebase Login

```
POST /auth/firebase/login
```

**Auth Required:** No

**Body:**
| Field | Type | Required |
|-------|------|----------|
| firebase_token | string | ✅ |

---

### 10. Link Firebase Account

```
POST /auth/firebase/link
```

**Auth Required:** No

**Body:**
| Field | Type | Required |
|-------|------|----------|
| firebase_token | string | ✅ |
| school_id | string | ✅ |
| password | string | ✅ |

---

### 11. Unlink Firebase Account

```
POST /auth/firebase/unlink
```

**Auth Required:** Yes

---

### 12. Manage User (Admin)

```
POST /auth/users/:userId/manage
```

**Auth Required:** Yes (Admin only)

**Body:**
| Field | Type | Required | Values |
|-------|------|----------|--------|
| action | string | ✅ | `approve`, `decline`, `suspend`, `unsuspend` |
| reason | string | ❌ | Required for decline/suspend |
| duration_days | number | ❌ | For suspend action |

---

## User Dashboard

> All endpoints require authentication

### 1. Get Dashboard Overview

```
GET /dashboard
```

**Response:** User stats including items count, claims, matches

---

### 2. Get Profile

```
GET /dashboard/profile
```

---

### 3. Update Profile

```
PUT /dashboard/profile
```

**Body:** (all optional)
| Field | Type | Validation |
|-------|------|------------|
| first_name | string | 2-100 chars |
| last_name | string | 2-100 chars |
| contact_number | string | PH mobile format |
| date_of_birth | string | `YYYY-MM-DD` |
| gender | string | `male`, `female`, `other`, `prefer_not_to_say` |
| address_line1 | string | Max 255 chars |
| address_line2 | string | Max 255 chars |
| city | string | Max 100 chars |
| province | string | Max 100 chars |
| postal_code | string | Max 20 chars |
| emergency_contact_name | string | Max 200 chars |
| emergency_contact_number | string | Valid phone |
| department | string | Max 100 chars |
| year_level | string | Max 50 chars |

---

### 4. Change Password

```
PUT /dashboard/profile/password
```

**Body:**
| Field | Type | Required |
|-------|------|----------|
| current_password | string | ✅ |
| new_password | string | ✅ |

---

### 5. Get Recent Activity

```
GET /dashboard/activity
```

---

### 6. Get My Lost Items

```
GET /dashboard/my-lost-items
```

**Query Parameters:**
| Param | Type | Default |
|-------|------|---------|
| status | string | all |
| page | int | 1 |
| limit | int | 10 |

---

### 7. Get My Found Items

```
GET /dashboard/my-found-items
```

**Query Parameters:** Same as above

---

### 8. Get My Claims

```
GET /dashboard/my-claims
```

**Query Parameters:** Same as above

---

### 9. Get Claims On My Items

```
GET /dashboard/claims-on-my-items
```

**Query Parameters:** Same as above

---

### 10. Get My Matches

```
GET /dashboard/my-matches
```

**Query Parameters:** Same as above

---

## Lost Items

### 1. List Lost Items

```
GET /lost-items
```

**Auth Required:** No (public items only) / Yes (all visible)

**Query Parameters:**
| Param | Type | Default | Description |
|-------|------|---------|-------------|
| page | int | 1 | Page number |
| limit | int | 10 | Items per page (max 100) |
| status | string | - | Filter by status |
| category_id | int | - | Filter by category |
| search | string | - | Search in title/description |

---

### 2. Get Single Lost Item

```
GET /lost-items/:id
```

---

### 3. Create Lost Item

```
POST /lost-items
```

**Auth Required:** Yes

**Content-Type:** `multipart/form-data` (if uploading images)

**Body:**
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| title | string | ✅ | 5-200 chars |
| description | string | ✅ | 20-2000 chars |
| category_id | int | ✅ | Valid category ID |
| last_seen_date | string | ✅ | `YYYY-MM-DD` |
| last_seen_location | string | ❌ | Max 500 chars |
| location_id | int | ❌ | Valid location ID |
| distinctive_features | string | ❌ | Max 1000 chars |
| reward_offered | decimal | ❌ | Min 0 |
| images | file[] | ❌ | Max 5 images, 5MB each, jpg/png/gif |

---

### 4. Update Lost Item

```
PUT /lost-items/:id
```

**Auth Required:** Yes (owner or admin)

**Body:** Same fields as create (all optional)

**Note:** Updates reset status to `pending` for re-review

---

### 5. Delete Lost Item

```
DELETE /lost-items/:id
```

**Auth Required:** Yes (owner or admin)

---

### 6. Review Lost Item (Admin)

```
PATCH /lost-items/:id/review
```

**Auth Required:** Yes (Admin or Security only)

**Body:**
| Field | Type | Required | Values |
|-------|------|----------|--------|
| action | string | ✅ | `approve` or `reject` |
| rejection_reason | string | ❌ | Required if rejecting |

---

## Found Items

### 1. List Found Items

```
GET /found-items
```

**Query Parameters:** Same as lost items

---

### 2. Get Single Found Item

```
GET /found-items/:id
```

---

### 3. Create Found Item

```
POST /found-items
```

**Auth Required:** Yes

**Content-Type:** `multipart/form-data`

**Body:**
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| title | string | ✅ | 5-200 chars |
| description | string | ✅ | 20-2000 chars |
| category_id | int | ✅ | Valid category ID |
| found_date | string | ✅ | `YYYY-MM-DD` |
| found_location | string | ❌ | Max 500 chars |
| location_id | int | ❌ | Valid location ID |
| current_location | string | ❌ | Where item is now stored |
| distinctive_features | string | ❌ | Max 1000 chars |
| images | file[] | ❌ | Max 5 images |

---

### 4. Update Found Item

```
PUT /found-items/:id
```

**Auth Required:** Yes (owner or admin)

**Note:** Updates reset status to `pending` for re-review

---

### 5. Delete Found Item

```
DELETE /found-items/:id
```

**Auth Required:** Yes (owner or admin)

---

### 6. Review Found Item (Admin)

```
PATCH /found-items/:id/review
```

**Auth Required:** Yes (Admin or Security only)

**Body:**
| Field | Type | Required | Values |
|-------|------|----------|--------|
| action | string | ✅ | `approve` or `reject` |
| rejection_reason | string | ❌ | Required if rejecting |

---

## Claims System

### 1. Submit Claim

```
POST /claims
```

**Auth Required:** Yes

**Content-Type:** `multipart/form-data`

**Body:**
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| found_item_id | int | ✅ | Valid found item ID |
| description | string | ✅ | 20-1000 chars |
| proof_details | string | ✅ | 20-2000 chars (serial numbers, unique features) |
| images | file[] | ❌ | Max 5 proof images |

---

### 2. List Claims

```
GET /claims
```

**Auth Required:** Yes

**Query Parameters:**
| Param | Type | Values |
|-------|------|--------|
| status | string | `pending`, `approved`, `rejected`, `cancelled` |
| page | int | Default: 1 |
| limit | int | 1-50, Default: 10 |

---

### 3. Get Single Claim

```
GET /claims/:id
```

**Auth Required:** Yes (claim owner, item owner, or admin)

---

### 4. Get Claims by Item

```
GET /claims/item/:itemId
```

**Auth Required:** Yes (item owner or admin)

---

### 5. Verify Claim (Approve/Reject)

```
PATCH /claims/:id/verify
```

**Auth Required:** Yes (Admin or Security only)

**Body (Approve):**

```json
{
  "action": "approve",
  "verification_notes": "Verified via serial number",
  "pickup_scheduled": "2025-12-05T10:00:00Z"
}
```

**Body (Reject):**

```json
{
  "action": "reject",
  "rejection_reason": "Description does not match item"
}
```

| Field              | Type   | Required                        |
| ------------------ | ------ | ------------------------------- |
| action             | string | ✅ (`approve` or `reject`)      |
| verification_notes | string | ❌                              |
| rejection_reason   | string | ✅ (if rejecting, 10-500 chars) |
| pickup_scheduled   | string | ❌ (ISO8601 datetime)           |

---

### 6. Schedule Pickup

```
PATCH /claims/:id/schedule
```

**Auth Required:** Yes (Admin or Security)

**Body:**
| Field | Type | Required |
|-------|------|----------|
| pickup_scheduled | string | ✅ (ISO8601) |

---

### 7. Record Pickup

```
PATCH /claims/:id/pickup
```

**Auth Required:** Yes (Admin or Security)

**Body:**
| Field | Type | Required |
|-------|------|----------|
| picked_up_by_name | string | ✅ (2-200 chars) |
| id_presented | string | ❌ (max 100 chars) |

---

### 8. Cancel Claim

```
PATCH /claims/:id/cancel
```

**Auth Required:** Yes (claim owner only)

---

## Notifications

### 1. Unsubscribe (Public)

```
GET /notifications/unsubscribe/:token
```

**Auth Required:** No (base64 token from email link)

---

### 2. Get Notifications

```
GET /notifications
```

**Auth Required:** Yes

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| page | int | Page number |
| limit | int | 1-100 |
| unread_only | bool | `true` or `false` |
| type | string | Filter by notification type |

---

### 3. Get Unread Count

```
GET /notifications/unread-count
```

**Auth Required:** Yes

---

### 4. Get Preferences

```
GET /notifications/preferences
```

**Auth Required:** Yes

---

### 5. Update Preferences

```
PATCH /notifications/preferences
```

**Auth Required:** Yes

**Body:**
| Field | Type | Required |
|-------|------|----------|
| email_notifications | boolean | ✅ |

---

### 6. Mark All as Read

```
PATCH /notifications/read-all
```

**Auth Required:** Yes

---

### 7. Mark Single as Read

```
PATCH /notifications/:id/read
```

**Auth Required:** Yes

---

### 8. Delete All Read

```
DELETE /notifications/clear-read
```

**Auth Required:** Yes

---

### 9. Delete Single Notification

```
DELETE /notifications/:id
```

**Auth Required:** Yes

---

## Search

### 1. Search Lost Items

```
GET /search/lost
```

**Auth Required:** Yes

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| q | string | Search query (optional) |
| category_id | int | Filter by category |
| location_id | int | Filter by location |
| lost_location_id | int | Filter by lost location |
| date_from | string | `YYYY-MM-DD` |
| date_to | string | `YYYY-MM-DD` |
| status | string | `pending`, `approved`, `rejected`, `matched`, `resolved`, `archived` |
| page | int | Page number |
| limit | int | Items per page (1-100) |

---

### 2. Search Found Items

```
GET /search/found
```

**Auth Required:** Yes

**Query Parameters:** Same as above (with `found_location_id`, `storage_location_id`)

---

### 3. Search All Items

```
GET /search/all
```

**Auth Required:** Yes

**Query Parameters:** Same as above (searches both lost and found)

---

## Categories

### 1. List Categories

```
GET /categories
```

**Auth Required:** No

**Query Parameters:**
| Param | Type | Default |
|-------|------|---------|
| active_only | bool | false |

---

### 2. Get Category

```
GET /categories/:id
```

**Auth Required:** No

---

### 3. Create Category

```
POST /categories
```

**Auth Required:** Yes (Admin only)

**Body:**
| Field | Type | Required |
|-------|------|----------|
| name | string | ✅ |
| description | string | ❌ |
| icon | string | ❌ |

---

### 4. Update Category

```
PUT /categories/:id
```

**Auth Required:** Yes (Admin only)

**Body:** Same as create

---

### 5. Delete Category

```
DELETE /categories/:id
```

**Auth Required:** Yes (Admin only)

---

### 6. Toggle Category Status

```
PATCH /categories/:id/toggle
```

**Auth Required:** Yes (Admin only)

---

## Locations

### 1. List Locations

```
GET /locations
```

**Auth Required:** No

**Query Parameters:**
| Param | Type | Default |
|-------|------|---------|
| active_only | bool | false |
| storage_only | bool | false |

---

### 2. Get Location

```
GET /locations/:id
```

**Auth Required:** No

---

### 3. Create Location

```
POST /locations
```

**Auth Required:** Yes (Admin only)

**Body:**
| Field | Type | Required |
|-------|------|----------|
| name | string | ✅ |
| building | string | ❌ |
| floor | string | ❌ |
| description | string | ❌ |
| is_storage | bool | ❌ |

---

### 4. Update Location

```
PUT /locations/:id
```

**Auth Required:** Yes (Admin only)

**Body:** Same as create

---

### 5. Delete Location

```
DELETE /locations/:id
```

**Auth Required:** Yes (Admin only)

---

### 6. Toggle Location Status

```
PATCH /locations/:id/toggle
```

**Auth Required:** Yes (Admin only)

---

## Matches

### 1. Get Matches for Lost Item

```
GET /matches/lost/:id
```

**Auth Required:** Yes (item owner or admin)

---

### 2. Get Matches for Found Item

```
GET /matches/found/:id
```

**Auth Required:** Yes (item owner or admin)

---

### 3. Get My Lost Item Matches

```
GET /matches/my-lost-items
```

**Auth Required:** Yes

---

### 4. Accept Match

```
POST /matches/:id/accept
```

**Auth Required:** Yes (lost item owner)

---

### 5. Reject Match

```
POST /matches/:id/reject
```

**Auth Required:** Yes (lost item owner)

---

### 6. Get Saved Matches

```
GET /matches/saved/:itemType/:itemId
```

**Auth Required:** Yes (item owner or admin)

**Parameters:**
| Param | Type | Values |
|-------|------|--------|
| itemType | string | `lost` or `found` |
| itemId | int | Item ID |

**Query Parameters:**
| Param | Type | Values |
|-------|------|--------|
| status | string | `suggested`, `confirmed`, `dismissed` |

---

### 7. Update Match Status

```
PATCH /matches/:matchId/status
```

**Auth Required:** Yes (item owner or admin)

**Body:**
| Field | Type | Required | Values |
|-------|------|----------|--------|
| status | string | ✅ | `suggested`, `confirmed`, `dismissed` |

---

### 8. Run Auto-Matching (Admin)

```
POST /matches/run-auto-match
```

**Auth Required:** Yes (Admin only)

---

## Admin Dashboard

> **All endpoints require Admin authentication**

### 1. Get Dashboard Overview

```
GET /admin/dashboard
```

**Response:** System-wide statistics including pending counts, user stats, item stats

---

### 2. Get Pending Items

```
GET /admin/pending
```

**Query Parameters:**
| Param | Type | Values |
|-------|------|--------|
| type | string | `users`, `lost`, `found`, `claims` |
| limit | int | 1-100, Default: 20 |

---

### 3. List All Users

```
GET /admin/users
```

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| status | string | `active`, `pending`, `suspended` |
| role | string | `user`, `security`, `admin` |
| search | string | Search by name/email/school_id (max 100 chars) |
| page | int | Page number |
| limit | int | 1-100 |

---

### 4. Get User Details

```
GET /admin/users/:id
```

---

### 5. Update User Role

```
PATCH /admin/users/:id/role
```

**Body:**
| Field | Type | Required | Values |
|-------|------|----------|--------|
| role | string | ✅ | `user`, `security`, `admin` |

---

### 6. Get Activity Logs

```
GET /admin/activity
```

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| action | string | Filter by action type (max 50 chars) |
| user_id | int | Filter by user |
| limit | int | 1-200, Default: 50 |

---

### 7. List All Lost Items (Admin)

```
GET /admin/lost-items
```

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| status | string | `pending`, `approved`, `rejected`, `matched`, `claimed`, `resolved`, `archived` |
| category_id | int | Filter by category |
| search | string | Search query (max 200 chars) |
| page | int | Page number |
| limit | int | 1-100 |

---

### 8. List All Found Items (Admin)

```
GET /admin/found-items
```

**Query Parameters:** Same as above

---

### 9. Reports: By Category

```
GET /admin/reports/by-category
```

**Response:** Item statistics grouped by category

---

### 10. Reports: By Location

```
GET /admin/reports/by-location
```

**Response:** Item statistics grouped by location

---

### 11. Reports: Trends

```
GET /admin/reports/trends
```

**Query Parameters:**
| Param | Type | Default | Range |
|-------|------|---------|-------|
| days | int | 30 | 7-90 |

**Response:** Daily statistics for the specified time period

---

## Health Check

### 1. Health Status (Public)

```
GET /health
```

**Auth Required:** No

**Response:**

```json
{
  "success": true,
  "status": "healthy",
  "timestamp": "2025-12-03T00:00:00.000Z",
  "version": "1.0.0"
}
```

---

### 2. Detailed Health Report

```
GET /health/report
```

**Auth Required:** Yes (Admin only)

**Response:** Comprehensive system report including:

- System info (platform, arch, hostname, node version)
- Memory usage (process and system)
- CPU info
- Disk usage
- Database status and statistics
- Uptime information

---

### 3. Database Health

```
GET /health/db
```

**Auth Required:** Yes (Admin only)

**Response:**

```json
{
  "success": true,
  "status": "connected",
  "responseTime": "5ms",
  "timestamp": "2025-12-03T00:00:00.000Z"
}
```

---

### 4. Memory Usage

```
GET /health/memory
```

**Auth Required:** Yes (Admin only)

**Response:** Detailed memory statistics for process and system

---

### 5. Live Statistics

```
GET /health/stats
```

**Auth Required:** Yes (Admin only)

**Response:** Real-time statistics including:

- User counts by status
- Item counts by status
- Match statistics
- Top categories
- Top locations

---

### 6. Disk Usage

```
GET /health/disk
```

**Auth Required:** Yes (Admin only)

**Response:** Disk usage information for all mounted drives

---

## Error Responses

All errors follow this format:

```json
{
  "success": false,
  "message": "Error description",
  "errors": [
    {
      "field": "email",
      "message": "Invalid email format"
    }
  ]
}
```

### HTTP Status Codes

| Code | Description                          |
| ---- | ------------------------------------ |
| 200  | Success                              |
| 201  | Created                              |
| 400  | Bad Request (validation error)       |
| 401  | Unauthorized (missing/invalid token) |
| 403  | Forbidden (insufficient permissions) |
| 404  | Not Found                            |
| 409  | Conflict (duplicate resource)        |
| 413  | Payload Too Large                    |
| 429  | Too Many Requests (rate limited)     |
| 500  | Internal Server Error                |

---

## Rate Limits

| Endpoint Type  | Limit                 |
| -------------- | --------------------- |
| General API    | 100 requests / 15 min |
| Auth endpoints | 10 requests / 15 min  |
| File uploads   | 20 requests / hour    |

---

## File Upload Limits

- **Max file size:** 5MB per file
- **Max files per request:** 5
- **Allowed types:** `image/jpeg`, `image/png`, `image/gif`
- **Forbidden:** Executables, scripts, SVG, HTML

---

_Last updated: December 3, 2025_
