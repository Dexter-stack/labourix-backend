# Labourix API Documentation

**Base URL:** `http://localhost:8000/api/v1`  
**Content-Type:** `application/json`  
**Authentication:** Bearer token via `Authorization: Bearer {token}` header (issued on login or email verification)

---

## Response Envelope

Every response follows this consistent structure:

```json
// Success (single object)
{
  "success": true,
  "message": "Human-readable result",
  "data": { }
}

// Success (paginated list)
{
  "success": true,
  "message": "...",
  "data": [ ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  },
  "links": {
    "first": "http://localhost:8000/api/v1/...",
    "last":  "http://localhost:8000/api/v1/...",
    "prev":  null,
    "next":  "http://localhost:8000/api/v1/...?page=2"
  }
}

// Error
{
  "success": false,
  "message": "Human-readable error",
  "errors": {
    "field": ["Validation message"]
  }
}
```

---

## Common Error Responses

| Status | When |
|--------|------|
| `401` | Missing or invalid Bearer token / wrong credentials |
| `403` | Authenticated but wrong role, or account is suspended |
| `404` | Resource or route not found |
| `409` | Worker unavailable or booking conflict |
| `422` | Validation failure or compliance block |
| `500` | Unexpected server error (message hidden in production) |

---

## Shared Object Shapes

### User Object
```json
{
  "id": 1,
  "name": "Jane Smith",
  "email": "jane@example.com",
  "role": "employer",
  "is_suspended": false,
  "suspended_at": null,
  "created_at": "2024-01-01T10:00:00.000000Z",
  "profile": null
}
```
`role` is one of: `employer` | `worker` | `admin` | `super_admin`  
`is_suspended` is `true` when `suspended_at` is set.  
`profile` is a **WorkerProfile Object** when loaded, otherwise `null`.

---

### WorkerProfile Object
```json
{
  "id": 1,
  "trade": "Electrician",
  "skills": ["wiring", "fault-finding", "inspection"],
  "location": "London",
  "hourly_rate": "28.00",
  "bio": "10 years commercial experience.",
  "is_available": true,
  "availability_schedule": { "mon": "08:00-17:00", "tue": "08:00-17:00" },
  "average_rating": "4.50",
  "total_jobs_completed": 14,
  "certifications": [ ],
  "match_score": 0.87
}
```
`match_score` is only present on the **matched-workers** endpoint.

---

### Certification Object
```json
{
  "id": 1,
  "name": "CSCS Card",
  "issuing_body": "CITB",
  "certificate_number": "ABC123456",
  "issued_at": "2022-03-15",
  "expires_at": "2025-03-15",
  "is_verified": false,
  "is_expired": false
}
```

---

### JobListing Object
```json
{
  "id": 1,
  "title": "Commercial Electrician – City of London",
  "description": "Install and test electrical systems...",
  "trade": "Electrician",
  "required_skills": ["wiring", "inspection"],
  "required_certifications": ["CSCS Card", "18th Edition"],
  "location": "City of London",
  "hourly_rate": "28.00",
  "start_date": "2024-07-01T08:00:00.000000Z",
  "end_date": "2024-07-31T17:00:00.000000Z",
  "workers_needed": 2,
  "status": "active",
  "employer": { },
  "created_at": "2024-06-01T10:00:00.000000Z"
}
```
`status` is one of: `draft` | `active` | `filled` | `cancelled`

---

### Booking Object
```json
{
  "id": 1,
  "status": "confirmed",
  "start_date": "2024-07-01T08:00:00.000000Z",
  "end_date": "2024-07-31T17:00:00.000000Z",
  "agreed_hourly_rate": "28.00",
  "cancellation_reason": null,
  "confirmed_at": "2024-06-15T09:30:00.000000Z",
  "completed_at": null,
  "job": { },
  "worker": { },
  "employer": { },
  "created_at": "2024-06-14T12:00:00.000000Z"
}
```
`status` is one of: `pending` | `confirmed` | `completed` | `cancelled`

---

### JobApplication Object
```json
{
  "id": 1,
  "status": "pending",
  "cover_note": "I have 5 years of commercial electrical experience.",
  "applied_at": "2024-06-10T11:00:00.000000Z",
  "job": { },
  "worker": { }
}
```
`status` is one of: `pending` | `shortlisted` | `rejected`  
`job` is a **JobListing Object** when loaded. `worker` is a **WorkerProfile Object** when loaded.

---

---

## Authentication

> All auth endpoints are **public** (no token required).

---

### POST /auth/register

Creates a new user account. An OTP is emailed immediately. **No token is returned** — the user must verify their email first.

**Request Body**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `name` | string | Yes | Max 255 chars |
| `email` | string | Yes | Must be unique |
| `password` | string | Yes | Min 8 chars |
| `password_confirmation` | string | Yes | Must match `password` |
| `role` | string | No | `employer` or `worker` (default: `worker`) |

**Example Request**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secret1234",
  "password_confirmation": "secret1234",
  "role": "employer"
}
```

**Response `201`**
```json
{
  "success": true,
  "message": "Account created. Please check your email for a 6-digit verification code.",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "employer",
    "created_at": "2024-06-01T10:00:00.000000Z",
    "profile": null
  }
}
```

---

### POST /auth/verify-email

Verifies the 6-digit OTP sent to the user's email. On success, returns a Sanctum token granting full API access.

**Request Body**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `email` | string | Yes | |
| `code` | string | Yes | Exactly 6 digits |

**Example Request**
```json
{
  "email": "john@example.com",
  "code": "482910"
}
```

**Response `200`**
```json
{
  "success": true,
  "message": "Email verified successfully.",
  "data": {
    "user": { },
    "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
  }
}
```

**Error `422`** — invalid or expired code
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": { "code": ["Invalid or expired OTP."] }
}
```

---

### POST /auth/resend-otp

Resends the email verification OTP. Subject to a **60-second cooldown** between requests.

**Request Body**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `email` | string | Yes | Must belong to an existing unverified account |

**Response `200`**
```json
{
  "success": true,
  "message": "A new verification code has been sent to your email."
}
```

---

### POST /auth/login

Returns a Sanctum token for a verified account. If the account email is **not yet verified**, a new OTP is auto-sent and a 422 is returned.

**Request Body**
| Field | Type | Required |
|-------|------|----------|
| `email` | string | Yes |
| `password` | string | Yes |

**Response `200`**
```json
{
  "success": true,
  "message": "Logged in successfully.",
  "data": {
    "user": { },
    "token": "2|xyz..."
  }
}
```

**Error `401`** — wrong credentials
```json
{ "success": false, "message": "Invalid credentials." }
```

**Error `422`** — email not verified (OTP resent automatically)
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": { "email": ["Your email is not verified. A new verification code has been sent."] }
}
```

---

### POST /auth/forgot-password

Sends a password-reset OTP to the given email. **Always returns `200`** regardless of whether the email is registered, to prevent account enumeration.

**Request Body**
| Field | Type | Required |
|-------|------|----------|
| `email` | string | Yes |

**Response `200`**
```json
{
  "success": true,
  "message": "If that email is registered, a reset code has been sent."
}
```

---

### POST /auth/verify-reset-otp

Verifies the password-reset OTP and exchanges it for a short-lived `reset_token`. The token is valid for **30 minutes**.

**Request Body**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `email` | string | Yes | |
| `code` | string | Yes | Exactly 6 digits |

**Response `200`**
```json
{
  "success": true,
  "message": "OTP verified. Use the reset token to set your new password.",
  "data": {
    "reset_token": "aB3kLm9pQr..."
  }
}
```

---

### POST /auth/reset-password

Resets the password using the `reset_token` from the previous step. **Revokes all existing sessions** on success.

**Request Body**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `reset_token` | string | Yes | From `verify-reset-otp` |
| `password` | string | Yes | Min 8 chars |
| `password_confirmation` | string | Yes | Must match `password` |

**Response `200`**
```json
{
  "success": true,
  "message": "Password reset successfully. Please log in."
}
```

---

### POST /auth/logout

**Auth required:** Yes (any role)

Revokes the current access token.

**Response `200`**
```json
{ "success": true, "message": "Logged out successfully." }
```

---

### GET /auth/me

**Auth required:** Yes (any role)

Returns the authenticated user, including their worker profile if they have one.

**Response `200`**
```json
{
  "success": true,
  "message": "Authenticated user retrieved.",
  "data": {
    "id": 1,
    "name": "Jane Smith",
    "email": "jane@example.com",
    "role": "worker",
    "created_at": "...",
    "profile": { }
  }
}
```

---

---

## Job Search

### GET /jobs

**Auth required:** No (public)

Searches active job listings. All filters are optional.

**Query Parameters**
| Parameter | Type | Notes |
|-----------|------|-------|
| `trade` | string | Filter by trade (e.g. `Electrician`) |
| `location` | string | Partial match on location name |
| `skills[]` | array | Filter by required skills (e.g. `skills[]=wiring`) |
| `max_rate` | numeric | Maximum hourly rate |
| `page` | integer | Page number (default: 1) |

**Response `200`** — paginated list of **JobListing Objects**

---

---

## Employer — Stats

> Requires **Auth + Role: `employer`**

### GET /employer/stats

Returns a dashboard summary for the authenticated employer.

**Response `200`**
```json
{
  "success": true,
  "message": "Employer stats retrieved.",
  "data": {
    "jobs": {
      "draft": 2,
      "active": 5,
      "filled": 10,
      "cancelled": 1
    },
    "bookings": {
      "pending": 3,
      "confirmed": 5,
      "completed": 12,
      "cancelled": 2
    },
    "spend": {
      "total": "28500.00",
      "this_month": "3200.00"
    },
    "unique_workers_hired": 8
  }
}
```

`spend` values are calculated as `agreed_hourly_rate × hours` across all completed bookings. `this_month` covers bookings whose `start_date` falls in the current calendar month.

---

---

## Employer — Jobs

> All routes below require **Auth + Role: `employer`**

---

### GET /employer/jobs

Lists all jobs posted by the authenticated employer (all statuses, paginated). Supports filtering and keyword search.

**Query Parameters**
| Parameter | Type | Notes |
|-----------|------|-------|
| `status` | string | Filter by status: `draft`, `active`, `filled`, `cancelled` |
| `trade` | string | Filter by trade (exact match, e.g. `Electrician`) |
| `search` | string | Keyword search across `title` and `description` |
| `per_page` | integer | Results per page (default: 15) |
| `page` | integer | Page number (default: 1) |

**Response `200`** — paginated list of **JobListing Objects**

---

### POST /employer/jobs

Creates a new job listing in `draft` status. Call `/publish` to make it visible.

**Request Body**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `title` | string | Yes | Max 255 |
| `description` | string | Yes | |
| `trade` | string | Yes | Max 100 |
| `required_skills` | array of strings | Yes | |
| `required_certifications` | array of strings | No | Cert names workers must hold |
| `location` | string | Yes | Max 255 |
| `latitude` | numeric | No | Between -90 and 90 |
| `longitude` | numeric | No | Between -180 and 180 |
| `hourly_rate` | numeric | Yes | Min 0 |
| `start_date` | datetime | Yes | Must be after now |
| `end_date` | datetime | No | Must be after `start_date` |
| `workers_needed` | integer | No | Min 1, default 1 |

**Response `201`** — single **JobListing Object**

---

### GET /employer/jobs/{job}

Returns a single job with employer details and booking list.

**Response `200`** — single **JobListing Object** (with `employer` and `bookings` loaded)

---

### PUT /employer/jobs/{job}

Updates a job listing. All fields are optional (`sometimes`).

**Request Body** — same fields as `POST /employer/jobs`, all optional

**Response `200`** — updated **JobListing Object**

---

### DELETE /employer/jobs/{job}

Soft-deletes a job listing.

**Response `200`**
```json
{ "success": true, "message": "Deleted successfully" }
```

---

### POST /employer/jobs/{job}/publish

Transitions a job from `draft` → `active`, making it visible in search and triggering AI worker matching in the background.

**Request Body** — none

**Response `200`** — updated **JobListing Object** (`status: "active"`)

---

### GET /employer/jobs/{job}/matched-workers

Returns workers ranked by AI matching score for the job (skill match 40%, proximity 20%, availability 20%, rating 20%). Falls back to rule-based scoring if the AI service is unavailable.

**Response `200`**
```json
{
  "success": true,
  "message": "Matched workers retrieved.",
  "data": [
    {
      "id": 5,
      "trade": "Electrician",
      "skills": ["wiring", "inspection"],
      "location": "London",
      "hourly_rate": "26.00",
      "is_available": true,
      "average_rating": "4.80",
      "match_score": 0.9200,
      "certifications": [ ]
    }
  ]
}
```
Results are ordered best-match first. `match_score` is between 0 and 1.

---

### GET /employer/jobs/{job}/applications

Lists all workers who have applied for a specific job (paginated).

**Query Parameters**
| Parameter | Type | Notes |
|-----------|------|-------|
| `per_page` | integer | Results per page (default: 15) |
| `page` | integer | Page number (default: 1) |

**Response `200`** — paginated list of **JobApplication Objects** (with `worker` loaded)

---

---

## Employer — Bookings

> All routes below require **Auth + Role: `employer`**

---

### GET /employer/bookings

Lists all bookings created by the authenticated employer (paginated).

**Response `200`** — paginated list of **Booking Objects**

---

### POST /employer/bookings

Creates a new booking, assigning a specific worker to a specific job.  
Fails with `409` if the worker is unavailable or has an overlapping booking.  
Fails with `422` if the worker is missing a required certification.

**Request Body**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `job_listing_id` | integer | Yes | Must exist in `job_listings` |
| `worker_id` | integer | Yes | Must exist in `users` |

**Response `201`** — single **Booking Object** (`status: "pending"`)

**Error `409`** — worker unavailable
```json
{ "success": false, "message": "Worker already has a booking that overlaps this period." }
```

**Error `422`** — compliance failure
```json
{ "success": false, "message": "Worker is missing required certification: CSCS Card." }
```

---

### GET /employer/bookings/{booking}

Returns a single booking with job, worker, and employer details.

**Response `200`** — single **Booking Object**

---

### POST /employer/bookings/{booking}/confirm

Transitions a booking from `pending` → `confirmed`. Notifies the worker by email.

**Request Body** — none

**Response `200`** — updated **Booking Object** (`status: "confirmed"`)

---

### POST /employer/bookings/{booking}/cancel

Cancels a booking. Notifies the worker by email.

**Request Body**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `reason` | string | Yes | Max 500 chars |

**Response `200`** — updated **Booking Object** (`status: "cancelled"`)

---

---

## Worker — Stats

> Requires **Auth + Role: `worker`**

### GET /worker/stats

Returns a dashboard summary for the authenticated worker.

**Response `200`**
```json
{
  "success": true,
  "message": "Worker stats retrieved.",
  "data": {
    "bookings": {
      "pending": 1,
      "confirmed": 2,
      "completed": 15,
      "cancelled": 3
    },
    "earnings": {
      "total": "4250.00",
      "this_month": "600.00"
    },
    "profile": {
      "average_rating": "4.75",
      "total_jobs_completed": 15,
      "is_available": true
    },
    "certifications": {
      "active": 3,
      "expiring_soon": 1,
      "expired": 0
    }
  }
}
```

`earnings` values are calculated as `agreed_hourly_rate × hours` across completed bookings. `this_month` covers bookings whose `start_date` falls in the current calendar month. `certifications.expiring_soon` counts certs expiring within the next **30 days**.

---

---

## Worker — Profile

> All routes below require **Auth + Role: `worker`**

---

### GET /worker/profile

Returns the authenticated worker's profile including all certifications.

**Response `200`**
```json
{
  "success": true,
  "message": "Profile retrieved.",
  "data": {
    "id": 3,
    "trade": "Plumber",
    "skills": ["pipework", "heating"],
    "location": "Manchester",
    "hourly_rate": "24.00",
    "bio": "...",
    "is_available": true,
    "availability_schedule": null,
    "average_rating": "4.20",
    "total_jobs_completed": 8,
    "certifications": [ ]
  }
}
```

---

### PUT /worker/profile

Creates or updates the worker's profile. All fields are optional.

**Request Body**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `trade` | string | No | Max 100 |
| `skills` | array of strings | No | |
| `location` | string | No | Max 255 |
| `latitude` | numeric | No | Between -90 and 90 |
| `longitude` | numeric | No | Between -180 and 180 |
| `hourly_rate` | numeric | No | Min 0 |
| `bio` | string | No | Max 1000 chars |
| `availability_schedule` | object | No | Free-form schedule object |

**Response `200`** — updated **WorkerProfile Object** with certifications

---

### POST /worker/profile/availability

Toggles the worker's real-time availability. Broadcasts the change via Pusher so the employer dashboard updates live.

**Request Body**
| Field | Type | Required |
|-------|------|----------|
| `is_available` | boolean | Yes |

**Response `200`**
```json
{
  "success": true,
  "message": "Availability updated.",
  "data": { "is_available": false }
}
```

---

---

## Worker — Job Applications

> All routes below require **Auth + Role: `worker`**

---

### POST /worker/jobs/{job}/apply

Submits an application for an active job listing. A worker can only apply once per job.

**Request Body**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `cover_note` | string | No | Max 1000 chars |

**Response `201`**
```json
{
  "success": true,
  "message": "Application submitted successfully.",
  "data": {
    "id": 12,
    "status": "pending",
    "cover_note": "I have 5 years of commercial electrical experience.",
    "applied_at": "2024-06-10T11:00:00.000000Z",
    "job": { }
  }
}
```

**Error `422`** — job not accepting applications
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": { "job": ["This job is not accepting applications."] }
}
```

**Error `422`** — already applied
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": { "job": ["You have already applied for this job."] }
}
```

---

### GET /worker/applications

Lists all job applications submitted by the authenticated worker (paginated).

**Query Parameters**
| Parameter | Type | Notes |
|-----------|------|-------|
| `per_page` | integer | Results per page (default: 15) |
| `page` | integer | Page number (default: 1) |

**Response `200`** — paginated list of **JobApplication Objects** (with `job.employer` loaded)

---

---

## Worker — Bookings

> All routes below require **Auth + Role: `worker`**

---

### GET /worker/bookings

Lists all bookings for the authenticated worker (paginated).

**Response `200`** — paginated list of **Booking Objects**

---

### GET /worker/bookings/{booking}

Returns a single booking with job listing and employer details.

**Response `200`** — single **Booking Object**

---

### POST /worker/bookings/{booking}/cancel

Cancels a booking the worker is assigned to.

**Request Body**
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `reason` | string | Yes | Max 500 chars |

**Response `200`** — updated **Booking Object** (`status: "cancelled"`)

---

---

## Worker — Certifications

> All routes below require **Auth + Role: `worker`**

---

### GET /worker/certifications

Lists all certifications on the authenticated worker's profile.

**Response `200`**
```json
{
  "success": true,
  "message": "Certifications retrieved.",
  "data": [
    {
      "id": 1,
      "name": "CSCS Card",
      "issuing_body": "CITB",
      "certificate_number": "ABC123",
      "issued_at": "2022-03-01",
      "expires_at": "2025-03-01",
      "is_verified": false,
      "is_expired": false
    }
  ]
}
```

---

### POST /worker/certifications

Adds a new certification. Accepts an optional document upload (PDF, JPG, or PNG, max 5 MB). Use `multipart/form-data` when uploading a file.

**Request Body** (`multipart/form-data`)
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `name` | string | Yes | Max 255 (e.g. `CSCS Card`) |
| `issuing_body` | string | No | Max 255 (e.g. `CITB`) |
| `certificate_number` | string | No | Max 100 |
| `issued_at` | date | No | Must not be in the future |
| `expires_at` | date | No | Must be after `issued_at` |
| `document` | file | No | PDF, JPG, JPEG, or PNG — max 5 MB |

**Response `201`** — single **Certification Object**

---

### DELETE /worker/certifications/{certification}

Deletes a certification and removes the associated stored document file (if any). Only the owner can delete their own certifications.

**Response `200`**
```json
{ "success": true, "message": "Deleted successfully" }
```

**Error `403`** — attempting to delete another worker's certification

---

---

## Admin — Stats

> Requires **Auth + Role: `admin` or `super_admin`**

### GET /admin/stats

Returns a platform-wide dashboard summary.

**Response `200`**
```json
{
  "success": true,
  "message": "Admin stats retrieved.",
  "data": {
    "users": {
      "total": 250,
      "workers": 180,
      "employers": 65,
      "admins": 5,
      "new_this_month": 12
    },
    "jobs": {
      "total": 340,
      "draft": 20,
      "active": 85,
      "filled": 200,
      "cancelled": 35
    },
    "bookings": {
      "total": 450,
      "pending": 30,
      "confirmed": 45,
      "completed": 340,
      "cancelled": 35,
      "total_spend": "125000.00"
    },
    "platform": {
      "workers_available": 65,
      "compliance_alerts": 8,
      "total_spend": "125000.00"
    }
  }
}
```

`compliance_alerts` is the count of worker profiles that have at least one certification expiring within the next **30 days**. `total_spend` is the sum of `agreed_hourly_rate × hours` across all completed platform bookings.

---

---

## Admin — Users

> All routes below require **Auth + Role: `admin` or `super_admin`**

---

### GET /admin/users

Lists all users (paginated, 20 per page). Optionally filter by role.

**Query Parameters**
| Parameter | Type | Notes |
|-----------|------|-------|
| `role` | string | Filter by role: `employer`, `worker`, `admin`, `super_admin` |

**Response `200`** — paginated list of **User Objects** (with `profile` loaded)

---

### GET /admin/users/{user}

Returns a single user with their worker profile and all certifications.

**Response `200`** — single **User Object** (with `profile.certifications` loaded)

---

### POST /admin/users/{user}/suspend

Suspends a user by setting `suspended_at` to the current timestamp. The user can still log in and access `GET /auth/me` and `POST /auth/logout`, but all role-specific endpoints return `403` until the account is unsuspended. Returns `409` if the user is already suspended.

**Request Body** — none

**Response `200`** — updated **User Object**
```json
{
  "success": true,
  "message": "User suspended.",
  "data": {
    "id": 7,
    "is_suspended": true,
    "suspended_at": "2024-06-10T14:30:00.000000Z"
  }
}
```

**Error `409`** — already suspended
```json
{ "success": false, "message": "User is already suspended." }
```

---

### POST /admin/users/{user}/unsuspend

Lifts a suspension by clearing `suspended_at`. Only admins can call this endpoint. Returns `409` if the user is not currently suspended.

**Request Body** — none

**Response `200`** — updated **User Object**
```json
{
  "success": true,
  "message": "User unsuspended.",
  "data": {
    "id": 7,
    "is_suspended": false,
    "suspended_at": null
  }
}
```

**Error `409`** — not currently suspended
```json
{ "success": false, "message": "User is not suspended." }
```

---

---

## Appendix

### Role Permissions Summary

| Endpoint Group | Required Role |
|----------------|---------------|
| Public (register, login, jobs search) | None |
| `/auth/logout`, `/auth/me` | Any authenticated user |
| `/employer/stats` | `employer` |
| `/employer/*` | `employer` |
| `/worker/stats` | `worker` |
| `/worker/*` | `worker` |
| `/admin/stats` | `admin` or `super_admin` |
| `/admin/*` | `admin` or `super_admin` |

### OTP Behaviour

- OTP codes are **6 digits**, valid for **10 minutes**
- A **60-second cooldown** is enforced between resend requests
- Requesting a new OTP immediately invalidates any previous unused OTP for the same email and type

### Forgot Password Token Lifetime

- The `reset_token` returned by `POST /auth/verify-reset-otp` is valid for **30 minutes**
- After `POST /auth/reset-password` succeeds, the token is invalidated and **all existing sessions are revoked**

### Pagination

All list endpoints are paginated. The default page size is **15** (20 for the admin user list). Use `?page=N` to navigate pages and `?per_page=N` to override the page size where supported.

### Job Application Flow

1. Employer posts a job (`POST /employer/jobs`) and publishes it (`POST /employer/jobs/{job}/publish`)
2. Worker discovers the job via `GET /jobs` (public search)
3. Worker applies (`POST /worker/jobs/{job}/apply`) — only allowed while job `status` is `active`
4. Worker can track their applications at `GET /worker/applications`
5. Employer reviews applicants at `GET /employer/jobs/{job}/applications`
6. Employer can then create a formal booking (`POST /employer/bookings`) for a chosen applicant

### Account Suspension

Suspended accounts follow this behaviour:

- **Login** — works normally; the user receives a token
- **`GET /auth/me`** — works; the response includes `"is_suspended": true` so clients can display a notice
- **`POST /auth/logout`** — works; suspended users can still revoke their token
- **All other authenticated routes** (`/employer/*`, `/worker/*`, `/admin/*`) — return `403 Your account has been suspended. Please contact support.`
- **Only admins** can call `POST /admin/users/{user}/unsuspend` to lift a suspension

### File Uploads

The `POST /worker/certifications` endpoint accepts file uploads. Use `Content-Type: multipart/form-data` when including a document.
