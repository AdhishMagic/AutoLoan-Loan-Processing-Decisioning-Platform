<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminLoanController extends Controller
{
    public function index(): View
    {
        $loans = LoanApplication::query()
            ->with(['primaryApplicant', 'user'])
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('admin.loans.index', compact('loans'));
    }

    public function show(LoanApplication $loan): RedirectResponse
    {
        // Reuse officer view for rich details; policy allows admin to view.
        return redirect()->route('officer.loans.show', $loan);
    }
}
