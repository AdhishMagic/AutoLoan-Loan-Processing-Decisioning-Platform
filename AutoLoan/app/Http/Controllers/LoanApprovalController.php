<?php

namespace App\Http\Controllers;

use App\Models\LoanApplication;
use Illuminate\Http\RedirectResponse;

class LoanApprovalController extends Controller
{
    public function approve(LoanApplication $loan): RedirectResponse
    {
        $this->authorize('approve', $loan);

        $loan->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Loan approved');
    }

    public function reject(LoanApplication $loan): RedirectResponse
    {
        $this->authorize('reject', $loan);

        $loan->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'Loan rejected');
    }

    public function hold(LoanApplication $loan): RedirectResponse
    {
        $this->authorize('hold', $loan);

        $loan->update([
            'status' => 'on_hold',
        ]);

        return back()->with('success', 'Loan placed on hold');
    }
}
