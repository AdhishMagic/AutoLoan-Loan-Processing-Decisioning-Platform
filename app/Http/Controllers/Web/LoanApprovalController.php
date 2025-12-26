<?php

namespace App\Http\Controllers\Web;

use App\Events\LoanApproved;
use App\Events\LoanRejected;
use App\Events\LoanStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Services\LoanCacheService;
use Illuminate\Http\RedirectResponse;

class LoanApprovalController extends Controller
{
    public function __construct(private readonly LoanCacheService $cache)
    {
    }

    public function approve(LoanApplication $loan): RedirectResponse
    {
        $this->authorize('approve', $loan);

        $oldStatus = (string) $loan->status;

        $loan->update([
            'status' => 'APPROVED',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        event(new LoanStatusUpdated($loan, $oldStatus, 'APPROVED'));
        event(new LoanApproved($loan));

        // Invalidate caches immediately after any status update
        $this->cache->forgetLoanStatus((string) $loan->id);

        return back()->with('success', 'Loan approved');
    }

    public function reject(LoanApplication $loan): RedirectResponse
    {
        $this->authorize('reject', $loan);

        $oldStatus = (string) $loan->status;

        $loan->update([
            'status' => 'REJECTED',
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ]);

        event(new LoanStatusUpdated($loan, $oldStatus, 'REJECTED'));
        event(new LoanRejected($loan));

        // Invalidate caches immediately after any status update
        $this->cache->forgetLoanStatus((string) $loan->id);

        return back()->with('success', 'Loan rejected');
    }

    public function hold(LoanApplication $loan): RedirectResponse
    {
        $this->authorize('hold', $loan);

        $loan->update([
            'status' => 'ON_HOLD',
        ]);

        // Invalidate caches immediately after any status update
        $this->cache->forgetLoanStatus((string) $loan->id);

        return back()->with('success', 'Loan placed on hold');
    }
}
