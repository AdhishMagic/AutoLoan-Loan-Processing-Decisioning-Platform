# AutoLoan — Loan Processing & Decisioning Platform (Laravel)

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-Framework-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/PHP-8%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/Queue-Workers-0EA5E9?style=for-the-badge" alt="Queues" />
  <img src="https://img.shields.io/badge/Auth-Sanctum-10B981?style=for-the-badge" alt="Sanctum" />
  <img src="https://img.shields.io/badge/Rules-Decisioning-F59E0B?style=for-the-badge" alt="Decisioning" />
</p>

<p align="center">
  <b>AutoLoan</b> is a production-minded <b>Laravel-based loan management platform</b> that automates the complete loan lifecycle—digital applications, document verification, and <b>rule-based approval decisioning</b>—using secure APIs, background job processing, and clean architecture to showcase real-world fintech workflows.
</p>

---

## ✨ Highlights (What makes this “fintech-grade”)

- **End-to-end loan lifecycle**: application → document upload → checks → underwriting decision → notifications
- **Rule-based decisioning engine**: JSON rules stored in DB + auditable decision trace
- **Async processing**: queues/jobs for OCR/KYC/credit checks and PDF/email generation
- **Secure APIs**: Sanctum token auth, rate limiting, signed URLs, validation & resources
- **Clean architecture**: controllers stay thin, business logic in services, repositories, DTOs
- **Audit-friendly**: events log / audit trail mindset for compliance-like workflows

---

## 🧭 Feature-by-Feature Map (AutoLoan Architecture)

> This map is designed as a portfolio checklist. Each module is a “senior” Laravel feature with a clear AutoLoan use-case.

### 🔐 Authentication & Authorization
- **Socialite + Sanctum** for web + API auth
- **Policies/Gates + Roles** for underwriting/admin/borrower access control  
**Example rules:**
- Borrower can only view their loans
- Underwriter can approve/reject
- Admin can see all dashboards

---

### ⚙️ Queues, Workers & Scheduling
- **Jobs**: OCR parsing, KYC checks, credit checks, PDF creation, email notifications
- **Scheduler**: nightly reconciliation, retries, cleanup, summaries
- **Horizon** (optional) to monitor job health & throughput

---

### 🔔 Events, Broadcasting & Notifications
- **Events/Listeners** to decouple business flows
- **Real-time updates** (Echo/WebSockets) for loan progress
- **Notifications** via mail + database for a user-visible history

---

### 📁 Document Storage & Signed URLs
- Store borrower docs safely
- Use **temporary signed routes** to allow secure time-limited downloads  
Example:
- “Offer Letter” download link valid for 30 minutes

---

### 🧠 Underwriting Rule Engine (Decisioning)
- Underwriting rules stored as **JSON**
- Executor evaluates inputs like:
  - credit score
  - income
  - DTI ratio
  - employment type
- Produces: `approved` / `rejected` / `manual_review`
- Saves a **decision trace** for auditability

---

### 🧰 Testing + CI/CD
- Feature tests for API endpoints & auth
- Unit tests for underwriting engine and services
- GitHub Actions pipeline for tests & quality checks (free-tier friendly)

---

## 🏗️ Suggested Folder Structure (Clean & Maintainable)

```txt
app/
  DTOs/
    LoanDto.php
  Events/
    LoanStatusUpdated.php
  Exceptions/
  Http/
    Controllers/
      Api/
        LoanController.php
      Auth/
        SocialController.php
    Requests/
      StoreLoanRequest.php
    Resources/
      LoanResource.php
  Jobs/
    ProcessLoanApplication.php
  Listeners/
    NotifyUnderwriters.php
  Models/
    Loan.php
    LoanDocument.php
  Observers/
    LoanObserver.php
  Policies/
    LoanPolicy.php
  Repositories/
    LoanRepository.php
  Services/
    UnderwritingEngine.php
    OcrService.php
    OtpService.php
    LoanCacheService.php

database/
  migrations/

routes/
  web.php
  api.php
```

---

## 🚀 Quick Start (Local)

### 1) Clone & install
```bash
git clone https://github.com/AdhishMagic/AutoLoan-Loan-Processing-Decisioning-Platform.git
cd AutoLoan-Loan-Processing-Decisioning-Platform

composer install
cp .env.example .env
php artisan key:generate
```

### 2) Configure environment
Update `.env`:
- `DB_*` (MySQL/Postgres)
- `QUEUE_CONNECTION=database` (easy default) or `redis`
- `MAIL_*` (Mailtrap recommended for dev)

### 3) Migrate database
```bash
php artisan migrate
```

### 4) Run the app
```bash
php artisan serve
```

---

## 🧵 Run Queues (Background Processing)

### Option A: Database queue (simplest)
```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

### Option B: Redis queue (faster)
```bash
# set QUEUE_CONNECTION=redis in .env
php artisan queue:work
```

---

## ⏱️ Scheduler (Cron)

Run locally for development:
```bash
php artisan schedule:work
```

Production cron (example):
```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

---

## 🔒 Security Notes (Fintech mindset)

- **Rate limit sensitive endpoints** (OTP / status checks)
- **Encrypt PII at rest** using Laravel encryption or encrypted casts
- Use **signed URLs** for document downloads
- Keep **APP_KEY** safe and rotate secrets properly
- Add **audit logs** for status changes and decisions

---

## 📡 API (Example Endpoints)

> Actual routes may differ—this is the intended public API shape.

- `POST /api/loans` — submit loan application
- `GET /api/loans/{id}` — fetch loan status & details
- `POST /api/loans/{id}/documents` — upload documents
- `GET /api/loans/{id}/documents/{doc}` — download (signed URL)

**Common API practices included:**
- FormRequest validation
- API Resources for consistent JSON
- Sanctum token auth
- Throttle middleware

---

## 🧪 Testing

```bash
php artisan test
```

Suggested test coverage:
- Underwriting decision matrix (approve/reject/manual_review)
- Policy rules (borrower vs underwriter vs admin)
- Job dispatch flow for new loan applications
- Signed URL download protections

---

## 📦 Free-tier Deployment Ideas

- **App**: Render / Railway / Fly.io (free tiers vary)
- **DB**: Render PostgreSQL / any free Postgres provider
- **Queue**: database queue (simple) or Redis (if available)
- **WebSockets**: self-host (free) + secure tunnel if needed

---

## 🗺️ Roadmap (Portfolio-grade add-ons)

- [ ] Admin dashboard (loan funnel, SLA metrics, exceptions)
- [ ] Decision trace UI (rule-by-rule evaluation breakdown)
- [ ] Manual review workflow (assign underwriter, comments, attachments)
- [ ] KYC/Credit check adapters (stubs → real integrations)
- [ ] OpenAPI docs (`docs/openapi.yaml`) + Postman collection

---

## 🤝 Contributing

PRs and suggestions are welcome:
1. Fork the repo
2. Create a feature branch
3. Add tests for new logic
4. Open a pull request with a clear description

---

## 📄 License

This project is provided for learning and portfolio demonstration purposes. Add a license file if you plan to distribute it openly.

---

### ⭐ If you found this useful
Give the repository a star and use the checklist above to turn AutoLoan into a standout “advanced Laravel” portfolio project.
