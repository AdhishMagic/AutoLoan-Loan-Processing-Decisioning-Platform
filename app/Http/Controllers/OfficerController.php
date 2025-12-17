<?php

namespace App\Http\Controllers;

use App\Models\LoanApplication;
use Illuminate\View\View;

class OfficerController extends Controller
{
    public function index(): View
    {
        $loans = LoanApplication::query()
            ->with('primaryApplicant')
            ->where('status', 'under_review')
            ->orderByDesc('submitted_at')
            ->paginate(15);

        return view('officer.review', compact('loans'));
    }
}
