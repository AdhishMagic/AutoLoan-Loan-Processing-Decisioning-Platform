<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'status',
        'email_verified_at',
        'last_login_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Role, User>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return HasMany<OauthAccount>
     */
    public function oauthAccounts(): HasMany
    {
        return $this->hasMany(OauthAccount::class);
    }

    /**
     * @return HasMany<ApiToken>
     */
    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

    /**
     * @return HasMany<LoanApplication>
     */
    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'user_id');
    }

    /**
     * @return HasMany<LoanApplication>
     */
    public function assignedLoanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class, 'assigned_officer_id');
    }

    /**
     * @return HasMany<LoanDecision>
     */
    public function loanDecisions(): HasMany
    {
        return $this->hasMany(LoanDecision::class, 'decided_by');
    }

    /**
     * @return HasMany<LoanDocument>
     */
    public function loanDocumentsVerified(): HasMany
    {
        return $this->hasMany(LoanDocument::class, 'verified_by');
    }

    /**
     * @return HasMany<KycCheck>
     */
    public function kycChecksVerified(): HasMany
    {
        return $this->hasMany(KycCheck::class, 'verified_by');
    }

    /**
     * @return HasMany<SecureAction>
     */
    public function secureActions(): HasMany
    {
        return $this->hasMany(SecureAction::class);
    }

    /**
     * @return HasMany<AuditLog>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
