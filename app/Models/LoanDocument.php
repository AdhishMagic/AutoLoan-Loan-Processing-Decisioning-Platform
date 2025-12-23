<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanDocument extends Model
{
    use HasUuids;

    public const TYPE_AADHAAR = 'aadhaar';
    public const TYPE_PAN = 'pan';
    public const TYPE_INCOME_PROOF = 'income_proof';
    public const TYPE_PROPERTY_DOC = 'property_doc';
    public const TYPE_VEHICLE_DOC = 'vehicle_doc';

    /**
     * @return array<int, string>
     */
    public static function supportedTypes(): array
    {
        return [
            self::TYPE_AADHAAR,
            self::TYPE_PAN,
            self::TYPE_INCOME_PROOF,
            self::TYPE_PROPERTY_DOC,
            self::TYPE_VEHICLE_DOC,
        ];
    }

    /**
     * Hard requirement: some docs must be present before submission.
     *
     * @return array<int, string>
     */
    public static function requiredTypes(): array
    {
        return [
            self::TYPE_AADHAAR,
            self::TYPE_PAN,
            self::TYPE_INCOME_PROOF,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function optionalTypes(): array
    {
        return [
            self::TYPE_PROPERTY_DOC,
            self::TYPE_VEHICLE_DOC,
        ];
    }

    /** @var array<int, string> */
    protected $fillable = [
        'loan_application_id',
        'user_id',
        'document_type',
        'file_path',
        'original_name',
        'ocr_text',
        'ocr_normalized_text',
        'ocr_hash',
        'extracted_data',
        'verification_result',
        'authenticity_score',
        'uniqueness_score',
        'trust_score',
        'analyzed_at',
        'analysis_version',
        'verified_by',
        'verified_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'verified_at' => 'datetime',
        'analyzed_at' => 'datetime',
        'extracted_data' => 'array',
        'verification_result' => 'array',
    ];

    /**
     * @return BelongsTo<LoanApplication, LoanDocument>
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * @return BelongsTo<User, LoanDocument>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, LoanDocument>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
