# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Labourix is an AI-powered Workforce Intelligence Exchange Platform for construction and facilities management companies. This repo is the **Laravel REST API backend only** (PHP 8.3+ / Laravel 11). The React frontend lives in a separate `labourix-frontend` repo.

**Stack**: Laravel 11, PostgreSQL, Redis (cache/queue), Laravel Sanctum (auth), AWS (ECS + RDS), Python AI microservices (called over internal HTTP — never couple AI logic into this repo).

## Commands

```bash
php artisan serve                # start dev server on port 8000
php artisan test --parallel      # run full test suite
php artisan test tests/Unit/Services/JobServiceTest.php  # run a single test file
php artisan migrate              # run database migrations
php artisan queue:work           # process queued jobs and listeners
php artisan route:list           # inspect all registered routes
php artisan make:service         # scaffold a service class (custom stub)
```

## Architecture

Strict **Controller → Service → Repository → Model → Event/Listener** layering. Never skip or cross layers.

```
HTTP Request
    ↓
Controller     — validate (FormRequest), call one Service method, return Resource
    ↓
Service        — all business logic; calls Repositories, fires Events
    ↓
Repository     — all Eloquent/DB queries; implements an interface in Repositories/Contracts/
    ↓
Model          — $fillable, $casts, relationships, local scopes only
    ↓
Events/Listeners — side-effects (notifications, AI triggers, audit logs); Listeners must implement ShouldQueue for any I/O
```

Repository interfaces are bound to Eloquent implementations in `AppServiceProvider`. Services use constructor injection.

## Key Coding Rules

**Controllers** — thin: validate via FormRequest, call one service method, return an API Resource. No DB queries, no if/else business logic.

**Services** — never use Eloquent directly; always go through the repository. Fire Events for side-effects; never call Listeners or Notifications directly from a service.

**Repositories** — all `where`, `with`, `orderBy`, pagination goes here. Always implement the interface in `Repositories/Contracts/`.

**Models** — no business logic, no service calls, no events. Relationships, casts, `$fillable` only.

**Enums** — use PHP 8.1 backed enums (in `app/Enums/`) for all status/type fields; never raw strings. Existing enums: `JobStatus`, `BookingStatus`, `UserRole`.

**API Resources** — every response must go through a Resource class. Never `$model->toArray()` or `response()->json($model)`.

## Domain Business Rules

1. **Compliance block**: A booking must be blocked if the worker has an expired or missing required certification. Always check via `ComplianceService` before confirming a booking.

2. **AI matching weights** (read from `config/labourix.php` — never hardcode): skill match 40%, proximity 20%, availability 20%, rating/performance 20%.

3. **AI service resilience**: `MatchingService` and `DemandForecastService` call Python microservices over HTTP. They must handle timeouts gracefully and fall back to rule-based scoring if the AI service is unavailable.

4. **Real-time availability**: Worker availability changes must be broadcast via Laravel Echo/Pusher so the frontend updates live (see `routes/channels.php`).

5. **Role hierarchy**: `employer` (post jobs, manage bookings) → `worker` (profile, availability, accept/decline) → `admin` (user mgmt, disputes, analytics) → `super_admin` (full access).

## API Versioning

All routes are prefixed `/api/v1/`. When breaking changes are needed, add `/api/v2/` without removing v1.

## Testing

- Unit tests for every Service in `tests/Unit/Services/`.
- Feature tests for every API endpoint in `tests/Feature/`.
- Use factories and seeders — never hardcode test data.
