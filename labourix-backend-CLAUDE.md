# Labourix – Laravel Backend (CLAUDE.md)

## Project Overview
Labourix is an AI-powered Workforce Intelligence Exchange Platform for construction and
facilities management companies. This repo is the **Laravel REST API backend only**.
The React frontend lives in a separate repo (`labourix-frontend`).

## Tech Stack
- **Framework**: Laravel 11 (PHP 8.3+)
- **Database**: PostgreSQL
- **Cache / Queue**: Redis
- **Auth**: Laravel Sanctum (JWT-style tokens)
- **AI Layer**: Python microservices (called via internal HTTP — do NOT couple AI logic here)
- **Hosting target**: AWS (ECS + RDS)

---

## Architecture: Service + Repository + Event-Driven

This codebase follows a strict layered architecture. Never put business logic in
controllers or models. Every layer has one job.

```
HTTP Request
    ↓
Controller          → validates input, delegates, returns response
    ↓
Service             → orchestrates business logic, fires Events
    ↓
Repository          → all database queries live here (no raw DB:: in services)
    ↓
Model               → Eloquent relationships + casts only (no logic)
    ↓
Events / Listeners  → side-effects (notifications, AI triggers, audit logs)
```

---

## Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── Employer/
│   │   ├── Worker/
│   │   └── Admin/
│   ├── Requests/          # FormRequest validation classes — one per action
│   └── Resources/         # API Resources for all responses (never raw model)
│
├── Services/              # Business logic — one service class per domain
│   ├── AuthService.php
│   ├── JobService.php
│   ├── WorkerService.php
│   ├── BookingService.php
│   ├── MatchingService.php        # Calls AI microservice
│   ├── DemandForecastService.php  # Calls AI microservice
│   ├── ComplianceService.php
│   ├── OptimisationService.php
│   └── NotificationService.php
│
├── Repositories/          # All DB queries — one repository per model
│   ├── Contracts/         # Repository interfaces
│   │   ├── JobRepositoryInterface.php
│   │   └── WorkerRepositoryInterface.php
│   └── Eloquent/          # Concrete Eloquent implementations
│       ├── JobRepository.php
│       └── WorkerRepository.php
│
├── Models/                # Eloquent models (relationships + casts only)
│   ├── User.php
│   ├── Job.php
│   ├── Booking.php
│   ├── WorkerProfile.php
│   ├── Certification.php
│   ├── Rating.php
│   └── WorkforceDemandForecast.php
│
├── Events/                # One event class per domain action
│   ├── JobPosted.php
│   ├── WorkerBooked.php
│   ├── BookingCancelled.php
│   ├── CertificationExpired.php
│   └── DemandForecastGenerated.php
│
├── Listeners/             # React to events (queued by default)
│   ├── NotifyMatchedWorkers.php
│   ├── TriggerAIMatching.php
│   ├── BlockBookingIfNonCompliant.php
│   └── SendComplianceAlert.php
│
├── Jobs/                  # Queued background jobs (Laravel Queue)
│   ├── RunDemandForecast.php
│   ├── RunOptimisationEngine.php
│   └── SyncWorkerAvailability.php
│
├── Policies/              # Authorization policies (one per model)
├── Enums/                 # PHP 8.1+ backed enums for status fields
│   ├── JobStatus.php      # draft, active, filled, cancelled
│   ├── BookingStatus.php  # pending, confirmed, completed, cancelled
│   └── UserRole.php       # employer, worker, admin, super_admin
│
└── Exceptions/            # Custom exception classes
    ├── ComplianceBlockException.php
    └── WorkerUnavailableException.php

database/
├── migrations/
└── seeders/

routes/
├── api.php               # versioned: /api/v1/...
└── channels.php          # broadcast channels (Laravel Echo / Pusher)

config/
└── labourix.php          # platform-specific config (scoring weights, etc.)
```

---

## Coding Rules — ALWAYS Follow These

### Controllers
- Controllers are thin. They only: validate (via FormRequest), call ONE service method, return a Resource.
- Never query the database directly from a controller.
- Never put if/else business logic in a controller.

```php
// Correct
public function store(PostJobRequest $request): JobResource
{
    $job = $this->jobService->createJob($request->validated(), auth()->user());
    return new JobResource($job);
}

// Wrong — logic belongs in Service
public function store(Request $request)
{
    $job = Job::create([...]);
    event(new JobPosted($job));
    return response()->json($job);
}
```

### Services
- Services contain ALL business logic.
- Services call Repositories for data — never use Eloquent directly in a service.
- Services fire Events for side-effects — never call Listeners or Notifications directly.
- Services are injected via constructor (use dependency injection).

```php
class JobService
{
    public function __construct(
        private JobRepositoryInterface $jobRepo,
        private WorkerRepositoryInterface $workerRepo,
    ) {}

    public function createJob(array $data, User $employer): Job
    {
        $job = $this->jobRepo->create($data + ['employer_id' => $employer->id]);
        event(new JobPosted($job));
        return $job;
    }
}
```

### Repositories
- Repositories implement an interface in `Repositories/Contracts/`.
- All Eloquent queries go here — including where, with, orderBy, pagination.
- Bind interfaces to implementations in `AppServiceProvider`.

```php
// Interface
interface JobRepositoryInterface {
    public function create(array $data): Job;
    public function findAvailableBySkills(array $skills, string $location): Collection;
}

// Implementation
class JobRepository implements JobRepositoryInterface {
    public function findAvailableBySkills(array $skills, string $location): Collection
    {
        return Job::where('status', JobStatus::Active)
            ->whereJsonContains('required_skills', $skills)
            ->where('location', $location)
            ->with(['employer', 'bookings'])
            ->get();
    }
}
```

### Events & Listeners
- Every significant state change MUST fire an Event.
- Listeners handle side-effects only (emails, AI calls, notifications, audit).
- All Listeners that do I/O must implement `ShouldQueue`.

```php
// Fire event in Service
event(new WorkerBooked($booking));

// Listener
class NotifyWorkerOfBooking implements ShouldQueue
{
    public function handle(WorkerBooked $event): void
    {
        $event->booking->worker->notify(new BookingConfirmedNotification($event->booking));
    }
}
```

### Models
- Models only define: $fillable, $casts, relationships, and local scopes.
- No business logic. No service calls. No events fired from models.

### Enums
- Use PHP 8.1 backed enums for all status/type fields. Never use raw strings.

```php
enum JobStatus: string {
    case Draft = 'draft';
    case Active = 'active';
    case Filled = 'filled';
    case Cancelled = 'cancelled';
}
```

### API Responses
- ALL responses must go through an API Resource class — never $model->toArray() or response()->json($model).
- Resources control exactly what gets exposed to the client.

---

## Key Domain Rules (Business Logic)

1. **Compliance Block**: A booking MUST be blocked if the worker has an expired or missing
   required certification. Check in `ComplianceService` before confirming any booking.

2. **AI Matching Scoring Weights** (stored in config/labourix.php — never hardcode):
   - Skill match: 40%
   - Proximity: 20%
   - Availability: 20%
   - Rating/performance: 20%

3. **Real-time availability**: Worker availability changes must be broadcast via
   Laravel Echo/Pusher so the frontend updates live.

4. **Role-based access**:
   - `employer` — post jobs, view matched workers, manage bookings
   - `worker` — update profile, set availability, accept/decline bookings
   - `admin` — user management, dispute resolution, analytics
   - `super_admin` — full system access

5. **AI Layer separation**: `MatchingService` and `DemandForecastService` make HTTP calls
   to Python microservices. They must handle timeouts gracefully and fall back to
   rule-based scoring if the AI service is unavailable.

---

## API Versioning
All routes are prefixed `/api/v1/`. When breaking changes are needed, create `/api/v2/`
without removing v1.

```
/api/v1/auth/...
/api/v1/jobs/...
/api/v1/workers/...
/api/v1/bookings/...
/api/v1/compliance/...
/api/v1/admin/...
```

---

## Testing
- Every Service must have a unit test in `tests/Unit/Services/`.
- Every API endpoint must have a feature test in `tests/Feature/`.
- Use factories and seeders — never hardcode test data.
- Run: `php artisan test --parallel`

---

## Useful Commands
```bash
php artisan serve           # start dev server (port 8000)
php artisan test            # run all tests
php artisan migrate         # run migrations
php artisan queue:work      # process queued jobs/listeners
php artisan route:list      # list all routes
php artisan make:service     # (custom stub) scaffold a service class
```
