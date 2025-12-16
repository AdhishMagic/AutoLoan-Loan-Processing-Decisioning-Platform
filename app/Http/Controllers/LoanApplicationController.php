<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Requests\UpdateLoanApplicationRequest;
use App\Models\LoanApplication;
use App\Services\LoanApplicationService;
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

    public function store(StoreLoanApplicationRequest $request, LoanApplicationService $service): RedirectResponse
    {
        $loan = $service->create($request->user(), $request->validated());

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

    public function update(UpdateLoanApplicationRequest $request, LoanApplication $loan, LoanApplicationService $service): RedirectResponse
    {
        Gate::authorize('update', $loan);
        $service->update($loan, $request->validated());

        return redirect()->route('loans.show', $loan)->with('success', 'Application updated');
    }

    public function destroy(LoanApplication $loan, LoanApplicationService $service): RedirectResponse
    {
        Gate::authorize('update', $loan);

        $service->delete($loan);

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
