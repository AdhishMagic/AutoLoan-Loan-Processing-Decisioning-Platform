<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\LoanApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OfficerController extends Controller
{
    public function index(): View
    {
        $loans = LoanApplication::query()
            ->with(['primaryApplicant', 'user'])
            ->whereIn('status', ['SUBMITTED', 'UNDER_REVIEW', 'PENDING_APPROVAL'])
            ->orderByDesc('submitted_at')
            ->paginate(15);

        return view('officer.review', compact('loans'));
    }

    public function show(LoanApplication $loan): View|RedirectResponse
    {
        // Loan officers should be able to open full details.
        // If a loan is unassigned, claim it on first view.
        if (auth()->user()?->isLoanOfficer() && empty($loan->assigned_officer_id)) {
            $loan->assigned_officer_id = auth()->id();
            $loan->save();
        }

        $this->authorize('view', $loan);

        $loan->loadMissing([
            'user',
            'primaryApplicant',
            'documents.user',
            'statusHistory',
            'applicants.addresses',
            'applicants.employmentDetails',
            'applicants.incomeDetails',
            'applicants.bankAccounts',
            'properties.addresses',
            'references',
            'declarations',
        ]);

        $primaryApplicant = $loan->primaryApplicant()->first();
        $documents = $loan->documents;

        return view('officer.loan_show', compact('loan', 'primaryApplicant', 'documents'));
    }

    public function decision(LoanApplication $loan): View
    {
        $this->authorize('view', $loan);

        $activeRule = \App\Models\UnderwritingRule::query()->where('active', true)->first();

        $facts = (new \App\Services\Underwriting\UnderwritingFactsBuilder())->build($loan);
        $result = (new \App\Services\Underwriting\UnderwritingEngine())->evaluate(
            (array) ($activeRule?->rules_json ?? []),
            $facts,
            $activeRule,
        );

        return view('officer.decision', compact('loan', 'activeRule', 'facts', 'result'));
    }
}
