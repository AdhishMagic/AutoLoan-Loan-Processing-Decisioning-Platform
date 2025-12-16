# 🏦 Loan Application System - Database Schema Documentation

## 📋 Overview

This document describes a **bank-grade, production-ready database schema** for a comprehensive Loan Application System designed for Indian financial institutions (HDFC-style mortgage applications).

### Tech Stack
- **Database:** PostgreSQL
- **Backend:** Laravel 11+ (Eloquent ORM)
- **Primary Key Strategy:** UUID (for security & scalability)
- **Normalization:** 3rd Normal Form (3NF)
- **Design Philosophy:** ACID-compliant, audit-friendly, compliance-ready

---

## 🎯 Key Design Principles

### ✅ Normalization & Data Integrity
- **3NF Compliance:** No redundant data, proper foreign key relationships
- **UUID Primary Keys:** Enhanced security, distributed system compatibility
- **Soft Deletes:** Data retention for audit trails
- **Enum Fields:** Type safety for predefined values
- **Indexed Columns:** Performance optimization for queries

### ✅ Banking & Compliance Standards
- **Audit Trail:** Complete history tracking via `loan_status_history`
- **Verification Tracking:** IP addresses, timestamps, verifier details
- **Digital Signatures:** Declaration acceptance with legal validity
- **GDPR Ready:** Soft deletes, data encryption support
- **KYC Compliance:** Separate KYC tracking with verification status

### ✅ Scalability & Performance
- **Polymorphic Relationships:** Reusable address table
- **Proper Indexing:** Query optimization on frequently searched columns
- **Partitioning Ready:** Date-based indexes for time-series data
- **Caching Strategy:** Computed fields for common calculations

---

## 📊 Entity Relationship Diagram

```
┌─────────────────────┐
│   LOAN_APPLICATIONS │ ◄───┐
│  (UUID Primary Key) │     │
└──────────┬──────────┘     │
           │                 │
           │ 1:N             │
           │                 │
┌──────────▼──────────┐     │
│     APPLICANTS      │     │
│  (UUID Primary Key) │     │
└──────────┬──────────┘     │
           │                 │
     ┌─────┴─────┬──────────┤
     │ 1:N       │ 1:N      │ 1:N
     │           │          │
┌────▼───┐  ┌───▼────┐ ┌──▼─────┐
│ADDRESS │  │EMPLOYMENT│INCOME  │
│(Poly-  │  │_DETAILS  │_DETAILS│
│morphic)│  └───┬────┘ └────┬───┘
└────────┘      │           │
                │ 1:N       │ 1:N
          ┌─────┴───────────┴──┐
          │                    │
     ┌────▼──────┐      ┌─────▼──────┐
     │BANK_      │      │EXISTING_   │
     │ACCOUNTS   │      │LOANS       │
     └───────────┘      └────────────┘

┌─────────────────────┐
│   LOAN_APPLICATIONS │
└──────────┬──────────┘
           │
     ┌─────┴─────┬──────────┬────────────┐
     │ 1:N       │ 1:N      │ 1:N        │
┌────▼───┐  ┌───▼────┐ ┌───▼──────┐┌───▼──────┐
│PROPERTIES│LOAN_    │LOAN_      ││LOAN_STATUS│
│         │REFERENCES│DECLARATIONS││_HISTORY   │
└─────────┘  └────────┘ └──────────┘└───────────┘
```

---

## 🗃️ Core Tables

### 1. **loan_applications** (Master Table)
Central table tracking the complete loan lifecycle.

**Key Features:**
- UUID primary key
- Comprehensive status workflow (20+ states)
- Financial assessment fields (FOIR, LTV, DSCR)
- Multi-officer assignment (officer, manager, underwriter)
- SLA tracking
- Risk categorization
- Top-up loan support

**Relationships:**
- `1:N` → applicants
- `1:N` → properties
- `1:N` → references
- `1:N` → declarations
- `1:N` → status_history
- `1:1` → parent_loan (self-referential for top-ups)

**Critical Indexes:**
```sql
- application_number (UNIQUE)
- status
- current_stage
- (status, assigned_officer_id)
- (loan_product_type, status)
- application_date
- is_sla_breached
```

---

### 2. **applicants** (Primary & Co-Applicants)
Stores detailed information about loan applicants.

**Key Features:**
- Supports PRIMARY and CO_APPLICANT roles
- Complete KYC details (PAN, Aadhaar, Passport)
- Demographic information
- Residential status tracking
- KYC verification workflow
- Politically Exposed Person (PEP) flag

**Relationships:**
- `N:1` → loan_applications
- `1:N` → addresses (polymorphic)
- `1:N` → employment_details
- `1:N` → income_details
- `1:N` → bank_accounts
- `1:N` → credit_cards
- `1:N` → existing_loans

**Critical Indexes:**
```sql
- pan_number (UNIQUE)
- aadhaar_number (UNIQUE)
- (loan_application_id, applicant_role)
- kyc_status
- mobile
- email
```

---

### 3. **addresses** (Polymorphic - Reusable)
Universal address table using polymorphic relationships.

**Key Features:**
- Supports CURRENT, PERMANENT, OFFICE, PROPERTY addresses
- Can belong to: Applicants, Properties, Companies
- Geo-coordinates support
- Proof of address tracking
- Verification workflow
- Duration at address

**Polymorphic Usage:**
```php
// Can be attached to:
- Applicant → Current/Permanent Address
- Property → Property Location
- Employment → Office Address
```

**Critical Indexes:**
```sql
- (addressable_type, addressable_id, address_type)
- (city, state, pincode)
- pincode
```

---

### 4. **employment_details**
Comprehensive employment/business information.

**Key Features:**
- Supports SALARIED, SELF_EMPLOYED, BUSINESS
- Company details (PAN, GSTIN, Industry Code)
- Experience tracking (total & current company)
- Previous employment support
- Verification methods (phone, email, physical)
- Document references

**Critical Indexes:**
```sql
- (applicant_id, employment_status)
- employment_type
```

---

### 5. **income_details**
Detailed income breakdown with tax information.

**Key Features:**
- Multiple income types (Salary, Business, Rental, etc.)
- Comprehensive salary components (Basic, HRA, Allowances)
- Deductions tracking (PF, TDS, ESI)
- Business income (Turnover, Net Profit)
- ITR filing status
- Bank account linkage
- Monthly/Quarterly/Annual frequencies

**Critical Indexes:**
```sql
- (applicant_id, income_type)
- is_verified
```

---

### 6. **bank_accounts**
Banking relationship and transaction behavior.

**Key Features:**
- Account type support (Savings, Current, Salary, OD)
- Banking behavior metrics
- Bounced cheque/EMI tracking
- Average balance calculations
- Overdraft facility details
- Penny drop verification support
- Primary account flagging

**Critical Indexes:**
```sql
- (applicant_id, account_type)
- (account_number, ifsc_code)
- is_primary_account
```

---

### 7. **credit_cards**
Credit card details for creditworthiness assessment.

**Key Features:**
- Credit limit & utilization tracking
- Payment behavior metrics
- Card vintage (tenure)
- Missed payment tracking
- Security: Only last 4 digits stored

**Critical Indexes:**
```sql
- applicant_id
- card_status
```

---

### 8. **existing_loans**
Comprehensive tracking of existing financial obligations.

**Key Features:**
- Multiple loan types (Home, Personal, Auto, etc.)
- EMI and tenure details
- Payment behavior (DPD, bounced EMIs)
- Closure tracking
- Obligation calculation flag
- Credit report verification

**Critical Indexes:**
```sql
- (applicant_id, loan_type)
- repayment_status
- is_to_be_closed
```

---

### 9. **properties**
Detailed property/collateral information.

**Key Features:**
- Multiple property types (Residential, Commercial, Plot)
- Construction status tracking
- Ownership details with co-owners
- Multiple area measurements (Carpet, Built-up, Super built-up)
- Valuation tracking
- Legal verification (Title, Encumbrance, Mortgage)
- Technical & Legal verification workflows
- Builder/Project details
- Insurance tracking

**Critical Indexes:**
```sql
- loan_application_id
- (property_type, construction_status)
- verification_status
```

---

### 10. **loan_references**
Personal & professional references for verification.

**Key Features:**
- Multiple reference types
- Contact verification workflow
- Feedback & rating system
- Priority ordering
- Know-since duration tracking

**Critical Indexes:**
```sql
- loan_application_id
- verification_status
- mobile
````

---

### 11. **loan_declarations**
Legal declarations and consent management.

**Key Features:**
- Multiple declaration types (Income, KYC, Consent, etc.)
- Digital signature support
- IP address & device tracking (legal validity)
- Version control
- Mandatory/optional flagging
- Witness signature support
- Validity period

**Critical Indexes:**
```sql
- (loan_application_id, declaration_type)
- (applicant_id, is_accepted)
- is_mandatory
```

---

### 12. **loan_status_history**
Complete audit trail of all actions.

**Key Features:**
- Status change tracking
- Action categorization (20+ types)
- Actor information with role
- Stage tracking
- Time taken metrics
- SLA breach monitoring
- Document attachment references
- Assignment tracking
- Notification status

**Critical Indexes:**
```sql
- loan_application_id
- (loan_application_id, action_timestamp)
- (current_status, action_type)
- performed_by
- action_timestamp
```

---

## 🔗 Key Relationships Summary

| Parent Table | Child Table | Type | Relationship |
|-------------|-------------|------|-------------|
| loan_applications | applicants | 1:N | One loan can have multiple applicants |
| loan_applications | properties | 1:N | One loan can have multiple properties |
| loan_applications | loan_references | 1:N | Multiple references per loan |
| loan_applications | loan_declarations | 1:N | Multiple declarations per loan |
| loan_applications | loan_status_history | 1:N | Complete audit trail |
| applicants | addresses | 1:N | Polymorphic (current, permanent) |
| applicants | employment_details | 1:N | Multiple employment records |
| applicants | income_details | 1:N | Multiple income sources |
| applicants | bank_accounts | 1:N | Multiple bank accounts |
| applicants | credit_cards | 1:N | Multiple credit cards |
| applicants | existing_loans | 1:N | Multiple existing obligations |
| properties | addresses | 1:N | Polymorphic property location |
| income_details | bank_accounts | N:1 | Salary credit account linkage |

---

## 📈 Indexing Strategy

### Primary Indexes (Automatically Created)
- All UUID primary keys
- All foreign keys

### Custom Performance Indexes

**loan_applications:**
```sql
CREATE INDEX idx_application_number ON loan_applications(application_number);
CREATE INDEX idx_status ON loan_applications(status);
CREATE INDEX idx_status_officer ON loan_applications(status, assigned_officer_id);
CREATE INDEX idx_product_status ON loan_applications(loan_product_type, status);
CREATE INDEX idx_sla_breach ON loan_applications(is_sla_breached);
```

**applicants:**
```sql
CREATE UNIQUE INDEX idx_pan ON applicants(pan_number);
CREATE UNIQUE INDEX idx_aadhaar ON applicants(aadhaar_number);
CREATE INDEX idx_mobile ON applicants(mobile);
CREATE INDEX idx_loan_applicant_role ON applicants(loan_application_id, applicant_role);
```

**addresses:**
```sql
CREATE INDEX idx_addressable_type ON addresses(addressable_type, addressable_id, address_type);
CREATE INDEX idx_location ON addresses(city, state, pincode);
```

**loan_status_history:**
```sql
CREATE INDEX idx_loan_timeline ON loan_status_history(loan_application_id, action_timestamp);
CREATE INDEX idx_timestamp ON loan_status_history(action_timestamp);
```

---

## 🚀 Laravel Eloquent Model Mapping

### Model Traits Used
- `HasUuids` - UUID primary key support
- `SoftDeletes` - Audit trail & data retention
- `HasFactory` - Testing support (optional)

### Key Relationship Examples

**LoanApplication Model:**
```php
// One loan → Many applicants
public function applicants(): HasMany
public function primaryApplicant(): HasMany
public function coApplicants(): HasMany

// One loan → Many properties
public function properties(): HasMany

// One loan → Complete history
public function statusHistory(): HasMany

// Top-up loan support
public function parentLoan(): BelongsTo
public function topUpLoans(): HasMany
```

**Applicant Model:**
```php
// Belongs to loan
public function loanApplication(): BelongsTo

// Has many related entities
public function addresses(): MorphMany
public function employmentDetails(): HasMany
public function incomeDetails(): HasMany
public function bankAccounts(): HasMany
public function creditCards(): HasMany
public function existingLoans(): HasMany

// Helper methods
public function isPrimary(): bool
public function isKycVerified(): bool
public function getFullNameAttribute(): string
```

**Address Model (Polymorphic):**
```php
// Can belong to multiple models
public function addressable(): MorphTo

// Usage examples:
Applicant::find($id)->addresses()->where('address_type', 'CURRENT')->get();
Property::find($id)->addresses()->first();
```

---

## 🔐 Security Features

### Data Protection
- **UUID Primary Keys:** Prevents ID enumeration attacks
- **Masked Sensitive Data:** Card numbers, account numbers (last 4 digits only)
- **Soft Deletes:** No permanent data loss
- **Encryption Ready:** GDPR-compliant fields can be encrypted

### Audit & Compliance
- **IP Address Logging:** Declaration acceptance, status changes
- **Timestamp Tracking:** Created, updated, deleted timestamps
- **Actor Tracking:** Who performed each action
- **Version Control:** Declaration versions tracked

### Access Control Ready
- Role-based relationships (officer, manager, underwriter)
- Assignment tracking in status history
- Verification workflows with user references

---

## 💡 Why This Design is Bank-Grade

### 1. **Regulatory Compliance**
- ✅ KYC/AML tracking with verification status
- ✅ Complete audit trail (RBI requirements)
- ✅ Declaration management with legal validity
- ✅ IP address & device info for digital signatures

### 2. **Risk Management**
- ✅ Credit score tracking (CIBIL integration ready)
- ✅ Risk categorization (Low/Medium/High/Very High)
- ✅ Banking behavior analysis (bounced cheques, EMI delays)
- ✅ Credit utilization monitoring

### 3. **Operational Excellence**
- ✅ SLA tracking & breach monitoring
- ✅ Stage-wise workflow management
- ✅ Multi-level approval support
- ✅ Priority-based processing

### 4. **Scalability**
- ✅ UUID keys for distributed systems
- ✅ Polymorphic relationships reduce redundancy
- ✅ Proper indexing for millions of records
- ✅ Soft deletes for data retention

### 5. **Data Integrity**
- ✅ 3NF normalization (no redundancy)
- ✅ Foreign key constraints
- ✅ Enum validations
- ✅ Required field validations

---

## 📝 Running Migrations

```bash
# Run all migrations
php artisan migrate

# Run specific migration
php artisan migrate --path=/database/migrations/2025_12_16_000001_create_applicants_table.php

# Rollback last batch
php artisan migrate:rollback

# Fresh migration (⚠️ Deletes all data)
php artisan migrate:fresh

# With seeders
php artisan migrate:fresh --seed
```

---

## 🧪 Sample Usage Examples

### Creating a Loan Application with Applicants

```php
use App\Models\LoanApplication;
use App\Models\Applicant;
use App\Models\Address;

// Create loan application
$loan = LoanApplication::create([
    'application_number' => 'LAP2025120001',
    'application_date' => now(),
    'user_id' => auth()->id(),
    'loan_product_type' => 'LAP',
    'requested_amount' => 5000000,
    'requested_tenure_months' => 240,
    'loan_purpose' => 'BUSINESS_EXPANSION',
    'status' => 'DRAFT',
]);

// Create primary applicant
$primaryApplicant = $loan->applicants()->create([
    'applicant_role' => 'PRIMARY',
    'first_name' => 'Rajesh',
    'last_name' => 'Kumar',
    'date_of_birth' => '1985-05-15',
    'gender' => 'MALE',
    'mobile' => '9876543210',
    'email' => 'rajesh.kumar@example.com',
    'pan_number' => 'ABCDE1234F',
    'aadhaar_number' => '123456789012',
    'marital_status' => 'MARRIED',
]);

// Add current address
$primaryApplicant->addresses()->create([
    'address_type' => 'CURRENT',
    'address_line_1' => 'Flat 101, Building A',
    'city' => 'Mumbai',
    'state' => 'Maharashtra',
    'pincode' => '400001',
    'country' => 'INDIA',
]);

// Add employment
$primaryApplicant->employmentDetails()->create([
    'employment_type' => 'SALARIED',
    'employment_status' => 'CURRENT',
    'company_name' => 'Tech Corp India',
    'designation' => 'Senior Manager',
    'date_of_joining' => '2020-01-15',
    'total_experience_years' => 12,
]);

// Add income
$primaryApplicant->incomeDetails()->create([
    'income_type' => 'SALARY',
    'income_frequency' => 'MONTHLY',
    'gross_income_amount' => 150000,
    'net_income_amount' => 125000,
    'gross_annual_income' => 1800000,
]);
```

### Querying Complex Relationships

```php
// Get all loan applications with applicants
$loans = LoanApplication::with(['applicants', 'properties', 'statusHistory'])
    ->where('status', 'PENDING_APPROVAL')
    ->get();

// Get applicant's total obligations
$applicant = Applicant::find($id);
$totalObligations = $applicant->existingLoans()
    ->consideredObligations()
    ->sum('emi_amount');

// Get loan application timeline
$timeline = LoanStatusHistory::forLoan($loanId)
    ->timeline()
    ->with('performer')
    ->get();

// Check if all mandatory declarations are accepted
$allAccepted = $loan->mandatoryDeclarations()
    ->where('is_accepted', false)
    ->doesntExist();
```

---

## 📊 Performance Considerations

### Query Optimization
- Use eager loading: `with()` for relationships
- Index frequently queried columns
- Use chunk() for large datasets
- Implement query caching for static data

### Monitoring
- Track slow queries
- Monitor index usage
- Implement query result caching
- Use database connection pooling

---

## 🎓 Best Practices

### 1. **Always Use Transactions**
```php
DB::transaction(function () use ($loan, $data) {
    $loan->update($data);
    $loan->statusHistory()->create([...]);
});
```

### 2. **Validate at Application Level**
```php
$request->validate([
    'pan_number' => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
    'aadhaar_number' => 'required|digits:12',
]);
```

### 3. **Use Scopes for Complex Queries**
```php
$loans = LoanApplication::byStatus('PENDING_APPROVAL')
    ->assignedTo(auth()->id())
    ->highPriority()
    ->get();
```

### 4. **Track All Changes in History**
```php
$loan->statusHistory()->create([
    'previous_status' => $loan->status,
    'current_status' => 'APPROVED',
    'action_type' => 'APPROVAL',
    'performed_by' => auth()->id(),
    'action_timestamp' => now(),
]);
```

---

## 📚 Additional Resources

### Enums to Create
```php
// app/Enums/ApplicantRole.php
// app/Enums/LoanProductType.php
// app/Enums/LoanStatus.php
// app/Enums/EmploymentType.php
// app/Enums/IncomeType.php
// etc.
```

### Seeders to Create
- RoleSeeder
- LoanProductSeeder
- DeclarationTemplateSeeder
- UnderwritingRuleSeeder

### Policies
- LoanApplicationPolicy (authorization)
- ApplicantPolicy
- PropertyPolicy

---

## ✅ Schema Verification Checklist

- [x] All tables use UUID primary keys
- [x] All foreign keys have proper constraints
- [x] Soft deletes implemented where needed
- [x] Proper indexes on query columns
- [x] Audit trail tables present
- [x] Verification workflows supported
- [x] Polymorphic relationships for reusability
- [x] Enum fields for type safety
- [x] Timestamp fields (created_at, updated_at)
- [x] Security fields (IP, user agent) for compliance

---

**Document Version:** 1.0  
**Last Updated:** December 16, 2025  
**Author:** Senior FinTech Backend Architect  
**Database:** PostgreSQL with Laravel Eloquent ORM
