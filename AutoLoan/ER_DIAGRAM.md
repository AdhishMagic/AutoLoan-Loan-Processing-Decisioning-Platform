# Entity Relationship Diagram (with Datatypes)

This document provides a visual ER diagram using Mermaid, listing major tables, key columns with datatypes, primary/foreign keys, and relationships.

Note: Some legacy tables (`loan_documents`, `kyc_checks`, `credit_checks`) still use BIGINT for `loan_application_id` while `loan_applications.id` is UUID. These should be aligned in follow-up migrations. The diagram shows actual current types for transparency.

## Mermaid ERD

```mermaid
erDiagram
		USERS {
			BIGINT id PK
			STRING name
			STRING email
			BIGINT role_id
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		ROLES {
			BIGINT id PK
			STRING name
			TEXT description
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		LOAN_APPLICATIONS {
			UUID id PK
			STRING application_number
			DATE application_date
			BIGINT user_id FK
			ENUM loan_product_type
			STRING loan_product_code
			STRING loan_scheme
			DECIMAL requested_amount
			DECIMAL sanctioned_amount
			DECIMAL disbursed_amount
			INT requested_tenure_months
			INT sanctioned_tenure_months
			DECIMAL requested_interest_rate
			DECIMAL sanctioned_interest_rate
			ENUM interest_type
			DECIMAL processing_fee
			DECIMAL processing_fee_percentage
			DECIMAL emi_amount
			INT emi_date
			ENUM loan_purpose
			TEXT purpose_description
			ENUM status
			STRING current_stage
			INT stage_order
			BIGINT assigned_officer_id FK
			BIGINT credit_manager_id FK
			BIGINT underwriter_id FK
			STRING assigned_branch
			TIMESTAMP submitted_at
			TIMESTAMP kyc_completed_at
			TIMESTAMP documents_completed_at
			TIMESTAMP credit_check_completed_at
			TIMESTAMP technical_verification_at
			TIMESTAMP legal_verification_at
			TIMESTAMP valuation_completed_at
			TIMESTAMP approved_at
			TIMESTAMP rejected_at
			TIMESTAMP sanctioned_at
			TIMESTAMP disbursed_at
			TEXT rejection_reason
			TEXT cancellation_reason
			BIGINT rejected_by FK
			BIGINT approved_by FK
			DECIMAL monthly_income
			DECIMAL monthly_obligations
			DECIMAL foir
			DECIMAL ltv_ratio
			DECIMAL dscr
			INT cibil_score
			STRING credit_bureau
			DATE credit_report_date
			ENUM risk_category
			INT risk_score
			STRING loan_account_number
			UUID disbursement_bank_account_id
			ENUM disbursement_mode
			STRING disbursement_reference
			TEXT disbursement_remarks
			ENUM preferred_communication
			ENUM priority
			TIMESTAMP sla_deadline
			BOOLEAN is_sla_breached
			BOOLEAN is_high_value
			BOOLEAN requires_manager_approval
			BOOLEAN is_fast_track
			BOOLEAN is_top_up_loan
			UUID parent_loan_id
			TEXT internal_notes
			TEXT customer_notes
			TIMESTAMP created_at
			TIMESTAMP updated_at
			TIMESTAMP deleted_at
		}

		APPLICANTS {
			UUID id PK
			UUID loan_application_id FK
			BIGINT user_id FK
			ENUM applicant_role
			STRING first_name
			STRING middle_name
			STRING last_name
			DATE date_of_birth
			ENUM gender
			ENUM marital_status
			STRING father_name
			STRING mother_name
			STRING spouse_name
			STRING mobile
			STRING alternate_mobile
			STRING email
			STRING alternate_email
			STRING pan_number
			STRING aadhaar_number
			STRING passport_number
			STRING voter_id
			STRING driving_license
			STRING religion
			STRING category
			STRING nationality
			ENUM education_level
			ENUM residential_status
			INT years_at_current_residence
			INT number_of_dependents
			ENUM kyc_status
			STRING kyc_reference_number
			TIMESTAMP kyc_verified_at
			BIGINT kyc_verified_by FK
			BOOLEAN is_politically_exposed
			TEXT additional_notes
			TIMESTAMP created_at
			TIMESTAMP updated_at
			TIMESTAMP deleted_at
		}

		ADDRESSES {
			UUID id PK
			STRING addressable_type
			UUID addressable_id
			ENUM address_type
			TEXT address_line_1
			TEXT address_line_2
			TEXT address_line_3
			STRING landmark
			STRING locality
			STRING city
			STRING district
			STRING state
			STRING country
			STRING pincode
			BOOLEAN is_verified
			TIMESTAMP verified_at
			BIGINT verified_by FK
			TEXT verification_notes
			STRING proof_type
			STRING proof_document_path
			DECIMAL latitude
			DECIMAL longitude
			INT years_at_address
			INT months_at_address
			BOOLEAN is_primary
			TIMESTAMP created_at
			TIMESTAMP updated_at
			TIMESTAMP deleted_at
		}

		EMPLOYMENT_DETAILS {
			UUID id PK
			UUID applicant_id FK
			ENUM employment_type
			ENUM employment_status
			STRING company_name
			STRING company_type
			STRING industry_type
			STRING industry_code
			STRING company_pan
			STRING company_gstin
			DATE company_incorporation_date
			STRING designation
			STRING department
			STRING employee_id
			DATE date_of_joining
			DATE date_of_leaving
			INT total_experience_years
			INT total_experience_months
			INT current_company_experience_years
			INT current_company_experience_months
			STRING business_nature
			INT years_in_business
			STRING office_ownership
			STRING office_phone
			STRING office_email
			STRING reporting_manager_name
			STRING reporting_manager_contact
			STRING hr_contact_name
			STRING hr_contact_phone
			BOOLEAN is_verified
			TIMESTAMP verified_at
			BIGINT verified_by FK
			ENUM verification_method
			TEXT verification_notes
			STRING appointment_letter_path
			STRING experience_letter_path
			STRING business_registration_path
			TIMESTAMP created_at
			TIMESTAMP updated_at
			TIMESTAMP deleted_at
		}

		INCOME_DETAILS {
			UUID id PK
			UUID applicant_id FK
			ENUM income_type
			ENUM income_frequency
			DECIMAL gross_income_amount
			DECIMAL net_income_amount
			DECIMAL deductions_amount
			DECIMAL gross_annual_income
			DECIMAL net_annual_income
			DECIMAL basic_salary
			DECIMAL hra
			DECIMAL special_allowance
			DECIMAL variable_pay
			DECIMAL bonus
			DECIMAL commission
			DECIMAL overtime
			DECIMAL other_allowances
			DECIMAL pf_deduction
			DECIMAL professional_tax
			DECIMAL tds
			DECIMAL esi
			DECIMAL loan_deduction
			DECIMAL other_deductions
			DECIMAL turnover
			DECIMAL net_profit
			DECIMAL depreciation
			ENUM salary_mode
			UUID salary_bank_account_id FK
			STRING itr_filing_status
			STRING last_itr_year
			DECIMAL last_itr_income
			STRING salary_slip_path
			STRING form16_path
			STRING itr_path
			STRING bank_statement_path
			STRING audit_report_path
			BOOLEAN is_verified
			TIMESTAMP verified_at
			BIGINT verified_by FK
			TEXT verification_notes
			STRING reference_month
			STRING reference_year
			TIMESTAMP created_at
			TIMESTAMP updated_at
			TIMESTAMP deleted_at
		}

		BANK_ACCOUNTS {
			UUID id PK
			UUID applicant_id FK
			ENUM account_type
			STRING bank_name
			STRING branch_name
			STRING ifsc_code
			STRING micr_code
			STRING swift_code
			STRING account_number
			STRING account_holder_name
			DATE account_opening_date
			INT account_vintage_months
			ENUM account_status
			DECIMAL average_monthly_balance
			DECIMAL current_balance
			DECIMAL minimum_balance
			INT monthly_credit_count
			DECIMAL monthly_credit_amount
			INT monthly_debit_count
			DECIMAL monthly_debit_amount
			INT bounced_cheque_count
			INT returned_emi_count
			BOOLEAN has_overdraft_facility
			DECIMAL overdraft_limit
			BOOLEAN is_salary_account
			BOOLEAN is_primary_account
			BOOLEAN is_loan_disbursement_account
			BOOLEAN is_verified
			TIMESTAMP verified_at
			BIGINT verified_by FK
			ENUM verification_method
			TEXT verification_notes
			STRING bank_statement_path
			STRING cancelled_cheque_path
			STRING passbook_front_page_path
			TIMESTAMP created_at
			TIMESTAMP updated_at
			TIMESTAMP deleted_at
		}

		CREDIT_CARDS {
			UUID id PK
			UUID applicant_id FK
			STRING card_issuer
			STRING card_type
			ENUM card_variant
			STRING card_number_last_4
			STRING card_holder_name
			DECIMAL credit_limit
			DECIMAL available_credit
			DECIMAL utilized_credit
			DECIMAL credit_utilization_percentage
			ENUM card_status
			DATE card_issue_date
			DATE card_expiry_date
			INT card_vintage_months
			DECIMAL average_monthly_spend
			DECIMAL current_outstanding
			BOOLEAN is_payment_regular
			INT missed_payment_count
			DATE last_payment_date
			BOOLEAN is_verified
			TIMESTAMP verified_at
			BIGINT verified_by FK
			TEXT verification_notes
			STRING card_statement_path
			TIMESTAMP created_at
			TIMESTAMP updated_at
			TIMESTAMP deleted_at
		}

		EXISTING_LOANS {
			UUID id PK
			UUID applicant_id FK
			ENUM loan_type
			STRING lender_name
			ENUM lender_type
			STRING loan_account_number
			DECIMAL original_loan_amount
			DECIMAL current_outstanding
			DECIMAL emi_amount
			INT total_tenure_months
			INT remaining_tenure_months
			DECIMAL interest_rate
			ENUM interest_type
			DATE loan_disbursement_date
			DATE loan_maturity_date
			DATE last_emi_date
			DATE next_emi_date
			ENUM repayment_status
			INT dpd_days
			INT bounced_emi_count
			INT missed_emi_count
			BOOLEAN has_overdue
			ENUM loan_security_type
			STRING collateral_description
			BOOLEAN is_to_be_closed
			DECIMAL preclosure_amount
			BOOLEAN is_considered_for_obligation
			BOOLEAN is_verified
			TIMESTAMP verified_at
			BIGINT verified_by FK
			ENUM verification_method
			TEXT verification_notes
			STRING loan_statement_path
			STRING sanction_letter_path
			STRING noc_path
			TIMESTAMP created_at
			TIMESTAMP updated_at
			TIMESTAMP deleted_at
		}

		PROPERTIES {
			UUID id PK
			UUID loan_application_id FK
			ENUM property_type
			ENUM property_sub_type
			ENUM construction_status
			INT property_age_years
			ENUM ownership_type
			STRING owner_name
			TEXT co_owners
			STRING property_id
			STRING survey_number
			STRING plot_number
			STRING khata_number
			STRING deed_number
			STRING registration_number
			DECIMAL carpet_area_sqft
			DECIMAL built_up_area_sqft
			DECIMAL super_built_up_area_sqft
			DECIMAL plot_area_sqft
			DECIMAL market_value
			DECIMAL government_value
			DECIMAL agreement_value
			DECIMAL stamp_duty_value
			DECIMAL rate_per_sqft
			DATE valuation_date
			STRING valuation_report_number
			BIGINT valued_by FK
			STRING builder_name
			STRING project_name
			STRING wing_tower
			STRING floor_number
			STRING flat_unit_number
			INT parking_count
			ENUM parking_type
			ENUM property_approval_status
			BOOLEAN has_clear_title
			BOOLEAN has_encumbrance
			BOOLEAN is_mortgaged
			STRING mortgaged_to
			TEXT amenities
			STRING boundary_north
			STRING boundary_south
			STRING boundary_east
			STRING boundary_west
			DECIMAL maintenance_charges
			DECIMAL property_tax_annual
			DECIMAL society_charges
			BOOLEAN is_insured
			STRING insurance_company
			STRING insurance_policy_number
			DECIMAL insurance_amount
			DATE insurance_expiry_date
			ENUM verification_status
			TIMESTAMP technical_verified_at
			BIGINT technical_verified_by FK
			TIMESTAMP legal_verified_at
			BIGINT legal_verified_by FK
			TEXT verification_notes
			STRING sale_deed_path
			STRING title_deed_path
			STRING ec_path
			STRING tax_receipt_path
			STRING approved_plan_path
			STRING noc_path
			STRING valuation_report_path
			TIMESTAMP created_at
			TIMESTAMP updated_at
			TIMESTAMP deleted_at
		}

		LOAN_REFERENCES {
			UUID id PK
			UUID loan_application_id FK
			ENUM reference_type
			STRING full_name
			ENUM relationship
			STRING mobile
			STRING alternate_mobile
			STRING email
			TEXT address
			STRING city
			STRING state
			STRING pincode
			STRING occupation
			STRING company_name
			STRING designation
			INT known_since_years
			STRING how_do_you_know
			ENUM verification_status
			TIMESTAMP contacted_at
			TIMESTAMP verified_at
			BIGINT verified_by FK
			ENUM verification_method
			TEXT verification_notes
			TEXT reference_feedback
			ENUM feedback_rating
			INT priority_order
			TIMESTAMP created_at
			TIMESTAMP updated_at
			TIMESTAMP deleted_at
		}

		LOAN_DECLARATIONS {
			UUID id PK
			UUID loan_application_id FK
			UUID applicant_id FK
			ENUM declaration_type
			STRING declaration_title
			TEXT declaration_text
			TEXT declaration_points
			BOOLEAN is_accepted
			TIMESTAMP accepted_at
			INET accepted_ip
			STRING accepted_user_agent
			STRING accepted_device_info
			STRING digital_signature_hash
			STRING signature_image_path
			STRING declaration_version
			BOOLEAN is_mandatory
			INT display_order
			STRING witness_name
			STRING witness_signature_path
			DATE valid_from
			DATE valid_till
			TEXT remarks
			TIMESTAMP created_at
			TIMESTAMP updated_at
			TIMESTAMP deleted_at
		}

		LOAN_STATUS_HISTORY {
			UUID id PK
			UUID loan_application_id FK
			STRING previous_status
			STRING current_status
			ENUM action_type
			STRING action_title
			TEXT action_description
			TEXT reason
			BIGINT performed_by FK
			ENUM actor_role
			STRING stage
			INT stage_order
			BIGINT assigned_from FK
			BIGINT assigned_to FK
			TIMESTAMP action_timestamp
			INT time_taken_minutes
			INET ip_address
			STRING user_agent
			TEXT additional_data
			BOOLEAN notification_sent
			TIMESTAMP notification_sent_at
			TEXT attached_documents
			ENUM priority
			TIMESTAMP sla_deadline
			BOOLEAN is_sla_breached
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		%% Legacy tables referencing BIGINT loan_application_id
		LOAN_DOCUMENTS {
			BIGINT id PK
			BIGINT loan_application_id FK
			STRING document_type
			STRING file_path
			BIGINT verified_by FK
			TIMESTAMP verified_at
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		KYC_CHECKS {
			BIGINT id PK
			BIGINT loan_application_id FK
			STRING kyc_type
			STRING result
			BIGINT verified_by FK
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		CREDIT_CHECKS {
			BIGINT id PK
			BIGINT loan_application_id FK
			INT credit_score
			STRING risk_level
			STRING source
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		UNDERWRITING_RULES {
			BIGINT id PK
			STRING name
			JSONB rules_json
			BOOLEAN active
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		NOTIFICATIONS {
			UUID id PK
			STRING type
			STRING notifiable_type
			BIGINT notifiable_id
			JSONB data
			TIMESTAMP read_at
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		SECURE_ACTIONS {
			BIGINT id PK
			BIGINT user_id FK
			STRING token
			STRING action
			TIMESTAMP expires_at
			TIMESTAMP used_at
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		OAUTH_ACCOUNTS {
			BIGINT id PK
			BIGINT user_id FK
			STRING provider
			STRING provider_user_id
			STRING access_token
			STRING refresh_token
			TIMESTAMP token_expires_at
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		API_TOKENS {
			BIGINT id PK
			BIGINT user_id FK
			STRING name
			STRING token
			BOOLEAN active
			TIMESTAMP last_used_at
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		LOAN_DECISIONS {
			BIGINT id PK
			BIGINT loan_application_id FK
			STRING decision
			DECIMAL sanctioned_amount
			DECIMAL interest_rate
			INT tenure_months
			TEXT remarks
			BIGINT decided_by FK
			TIMESTAMP decided_at
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		JOB_LOGS {
			BIGINT id PK
			STRING job_name
			STRING status
			TEXT message
			TIMESTAMP started_at
			TIMESTAMP finished_at
			BIGINT run_by FK
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		AUDIT_LOGS {
			BIGINT id PK
			STRING auditable_type
			BIGINT auditable_id
			STRING event
			JSONB old_values
			JSONB new_values
			BIGINT user_id FK
			INET ip_address
			STRING user_agent
			TIMESTAMP created_at
			TIMESTAMP updated_at
		}

		%% Relationships
		USERS ||--o{ LOAN_APPLICATIONS : "created_by user_id"
		USERS ||--o{ LOAN_APPLICATIONS : "assigned_officer_id"
		USERS ||--o{ LOAN_APPLICATIONS : "credit_manager_id"
		USERS ||--o{ LOAN_APPLICATIONS : "underwriter_id"
		USERS ||--o{ LOAN_APPLICATIONS : "approved_by"
		USERS ||--o{ LOAN_APPLICATIONS : "rejected_by"
		ROLES ||--o{ USERS : "role_id"

		LOAN_APPLICATIONS ||--o{ APPLICANTS : "loan_application_id"
		LOAN_APPLICATIONS ||--o{ PROPERTIES : "loan_application_id"
		LOAN_APPLICATIONS ||--o{ LOAN_REFERENCES : "loan_application_id"
		LOAN_APPLICATIONS ||--o{ LOAN_DECLARATIONS : "loan_application_id"
		LOAN_APPLICATIONS ||--o{ LOAN_STATUS_HISTORY : "loan_application_id"

		APPLICANTS ||--o{ EMPLOYMENT_DETAILS : "applicant_id"
		APPLICANTS ||--o{ INCOME_DETAILS : "applicant_id"
		APPLICANTS ||--o{ BANK_ACCOUNTS : "applicant_id"
		APPLICANTS ||--o{ CREDIT_CARDS : "applicant_id"
		APPLICANTS ||--o{ EXISTING_LOANS : "applicant_id"
		APPLICANTS ||--o{ LOAN_DECLARATIONS : "applicant_id"

		%% Polymorphic address relations (modeled as simple 1:N for diagram)
		APPLICANTS ||--o{ ADDRESSES : "addressable_id"
		EMPLOYMENT_DETAILS ||--o{ ADDRESSES : "addressable_id"
		PROPERTIES ||--o{ ADDRESSES : "addressable_id"

		%% Cross refs
		INCOME_DETAILS }o--|| BANK_ACCOUNTS : "salary_bank_account_id"
		USERS ||--o{ LOAN_STATUS_HISTORY : "performed_by"
		USERS ||--o{ LOAN_STATUS_HISTORY : "assigned_from/assigned_to"

		%% Legacy relationships
		LOAN_APPLICATIONS ||--o{ LOAN_DOCUMENTS : "loan_application_id (BIGINT)"
		LOAN_APPLICATIONS ||--o{ KYC_CHECKS : "loan_application_id (BIGINT)"
		LOAN_APPLICATIONS ||--o{ CREDIT_CHECKS : "loan_application_id (BIGINT)"
		USERS ||--o{ LOAN_DOCUMENTS : "verified_by"
		USERS ||--o{ KYC_CHECKS : "verified_by"
    
		%% Additional relationships
		USERS ||--o{ OAUTH_ACCOUNTS : "user_id"
		USERS ||--o{ API_TOKENS : "user_id"
		USERS ||--o{ SECURE_ACTIONS : "user_id"
		USERS ||--o{ JOB_LOGS : "run_by"
		USERS ||--o{ AUDIT_LOGS : "user_id"
    
		LOAN_APPLICATIONS ||--o{ LOAN_DECISIONS : "loan_application_id"
		LOAN_APPLICATIONS ||--o{ AUDIT_LOGS : "auditable_id (type LoanApplication)"
		APPLICANTS ||--o{ AUDIT_LOGS : "auditable_id (type Applicant)"
		PROPERTIES ||--o{ AUDIT_LOGS : "auditable_id (type Property)"
```

## Notes

- Rendering: VS Code supports Mermaid preview in Markdown. Use the built-in preview to see the diagram.
- Types: Datatypes mirror Laravel migrations targeting PostgreSQL (e.g., UUID, JSONB, TIMESTAMPTZ, NUMERIC).
- Legacy alignment: Consider adding migrations to convert `loan_documents`, `kyc_checks`, `credit_checks` `loan_application_id` to UUID to fully align with `loan_applications.id`.

