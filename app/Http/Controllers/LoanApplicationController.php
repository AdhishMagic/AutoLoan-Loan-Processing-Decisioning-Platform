<?php

namespace App\Http\Controllers;

use App\Models\LoanApplication;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LoanApplicationController extends Controller
{
    public function index(): View
    {
        $loans = LoanApplication::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('loans.index', compact('loans'));
    }

    public function create(): View
    {
        return view('loans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'loan_type' => ['required', 'string', 'max:50'],
            'requested_amount' => ['required', 'numeric', 'min:0'],
            'tenure_months' => ['required', 'integer', 'min:1'],
        ]);

        $loan = LoanApplication::create([
            'user_id' => auth()->id(),
            'loan_type' => $validated['loan_type'],
            'requested_amount' => $validated['requested_amount'],
            'tenure_months' => $validated['tenure_months'],
            'status' => 'draft',
        ]);

        return redirect()->route('loans.show', $loan)->with('success', 'Application created');
    }

    public function show(LoanApplication $loan): View
    {
        Gate::authorize('view', $loan);

        return view('loans.show', compact('loan'));
    }

    public function edit(LoanApplication $loan): View
    {
        Gate::authorize('update', $loan);

        return view('loans.edit', compact('loan'));
    }

    public function update(Request $request, LoanApplication $loan): RedirectResponse
    {
        Gate::authorize('update', $loan);

        $validated = $request->validate([
            'loan_type' => ['required', 'string', 'max:50'],
            'requested_amount' => ['required', 'numeric', 'min:0'],
            'tenure_months' => ['required', 'integer', 'min:1'],
        ]);

        $loan->update($validated);

        return redirect()->route('loans.show', $loan)->with('success', 'Application updated');
    }

    public function destroy(LoanApplication $loan): RedirectResponse
    {
        Gate::authorize('update', $loan);

        $loan->delete();

        return redirect()->route('loans.index')->with('success', 'Application deleted');
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
