<?php

namespace App\Http\Controllers;

use App\Models\LoanApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LoanApplicationController extends Controller
{
    public function show(LoanApplication $loan): View
    {
        Gate::authorize('view', $loan);

        return view('loans.show', compact('loan'));
    }

    public function approve(LoanApplication $loan): RedirectResponse
    {
        Gate::authorize('approve', $loan);

        $loan->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Loan approved');
    }
}
