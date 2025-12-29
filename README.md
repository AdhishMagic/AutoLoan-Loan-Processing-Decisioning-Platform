# AutoLoan — Loan Processing & Decisioning Platform

AutoLoan is a role-based loan application and decisioning platform with:
- An 8-step borrower wizard (draft/save/resume + final submit)
- Loan officer review workflow (approve/reject/hold)
- Secure document upload + time-limited downloads
- Event-driven emails + in-app notifications

## Process Coverage (Concept + Logic)

This section maps the key platform “processes” (conceptually) to where the logic currently lives in the codebase.

### 1) Authentication (SSO + login)

**Status:** Implemented (web login + Google SSO)

- Session-based auth flows (register/login/email-verify) are defined in [routes/auth.php](routes/auth.php)
- Google OAuth SSO flow is implemented in [app/Http/Controllers/Auth/GoogleAuthController.php](app/Http/Controllers/Auth/GoogleAuthController.php) and wired in [routes/web.php](routes/web.php)

**Token-based API auth:** Not currently wired for API routes (current API is just a health endpoint in [routes/api.php](routes/api.php)).

### 2) Authorization (roles + policies/gates)

**Status:** Implemented

- Role middleware enforcement via [app/Http/Middleware/RoleMiddleware.php](app/Http/Middleware/RoleMiddleware.php) and role-based route groups in [routes/web.php](routes/web.php)
- Loan application authorization rules via [app/Policies/LoanApplicationPolicy.php](app/Policies/LoanApplicationPolicy.php) and registration in [app/Providers/AuthServiceProvider.php](app/Providers/AuthServiceProvider.php)
- Document authorization rules via [app/Http/Policies/LoanDocumentPolicy.php](app/Http/Policies/LoanDocumentPolicy.php)
- Policy enforcement is used via `Gate::authorize(...)` and `$this->authorize(...)` in controllers

### 3) Queues, Jobs & Background processing

**Status:** Implemented (asynchronous pattern)

- Background processing job: [app/Jobs/ProcessLoanApplication.php](app/Jobs/ProcessLoanApplication.php)
- Dispatched on final submit (step 8) in [app/Http/Controllers/Web/LoanApplicationController.php](app/Http/Controllers/Web/LoanApplicationController.php)
- Emails are queued (non-blocking) via `->queue(...)` in listeners under [app/Listeners](app/Listeners)

### 4) Scheduling (periodic tasks)

**Status:** Not implemented yet (no app-level scheduled tasks defined)

Conceptually, scheduling is where you’d define periodic jobs (cleanup, reminders, reconciliation). The project currently relies on event-driven and on-demand processing rather than time-based scheduling.

### 5) Events & Listeners (decoupled workflow)

**Status:** Implemented (event-driven architecture)

- Domain events: [app/Events](app/Events)
- Listener wiring: [app/Providers/EventServiceProvider.php](app/Providers/EventServiceProvider.php)
- On submit: emits `LoanApplicationSubmitted` + `LoanSubmitted`
- On decisions: emits `LoanApproved` / `LoanRejected` and `LoanStatusUpdated`

**Broadcasting / realtime push:** Not implemented (events are currently internal and handled by listeners, not broadcast to clients).

### 6) Notifications & Mail (user-facing comms)

**Status:** Implemented

- Database in-app notifications via [app/Notifications/LoanStatusNotification.php](app/Notifications/LoanStatusNotification.php)
- Notifications are created for both applicant and officer via listeners:
	- [app/Listeners/SendLoanSubmittedNotification.php](app/Listeners/SendLoanSubmittedNotification.php)
	- [app/Listeners/SendLoanApprovedNotification.php](app/Listeners/SendLoanApprovedNotification.php)
	- [app/Listeners/SendLoanRejectedNotification.php](app/Listeners/SendLoanRejectedNotification.php)
- Navbar bell displays unread count + recent notifications in [resources/views/layouts/partials/navbar.blade.php](resources/views/layouts/partials/navbar.blade.php)
- Clicking a notification marks it read and redirects via [app/Http/Controllers/NotificationController.php](app/Http/Controllers/NotificationController.php) and [routes/web.php](routes/web.php)

### 7) Storage & Signed URLs (secure documents)

**Status:** Implemented

- Private document storage uses local disk + per-loan folders and is enforced in [app/Http/Controllers/LoanDocumentController.php](app/Http/Controllers/LoanDocumentController.php)
- Time-limited downloads use signed routes (temporary signed links + signature validation)
- Upload is integrated into wizard step 8 (pre-submit) in [resources/views/loans/step_8.blade.php](resources/views/loans/step_8.blade.php)

## Notes

- The repo is intentionally structured so the *logic* is portable: you can swap the underlying implementation details later (e.g., how tokens are issued, queue backend, or where files are stored) without changing the business workflow.

## Observability (Laravel Pulse)

Pulse is the built-in observability layer for AutoLoan and is available at `/pulse`.

**What Pulse monitors**
- HTTP request performance (including slow requests)
- Queue and job throughput/runtime (including slow jobs)
- Exceptions and error rates
- Slow database queries
- Cache interactions (hit/miss patterns)
- Server snapshots (worker / host health via scheduled `pulse:check`)

**Access control**
- `/pulse` is restricted to authenticated staff users only: `admin` and `loan_officer` (mapped to the `manager` role).

**AutoLoan workflows to watch**
- Loan processing delays: look at slow requests and job runtimes around loan submission and processing.
- KYC / Credit failures: check Exceptions + Queues views after dispatching background jobs.
- Slow admin dashboard queries: review Slow Queries when reviewing applications.
- Cache behavior for `loan:status:*`, `user:profile:*`, `kyc:result:*`: check Cache Interactions groups.

**Common issues Pulse helps diagnose**
- A spike in failed jobs when Redis/queue workers are down.
- Long request times caused by slow DB queries.
- Cache misses increasing load on loan status / KYC reads.
