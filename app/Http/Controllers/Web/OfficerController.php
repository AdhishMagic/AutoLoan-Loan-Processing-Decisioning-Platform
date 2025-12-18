<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\LoanApplication;
use Illuminate\View\View;

class OfficerController extends Controller
{
    public function index(): View
    {
        $loans = LoanApplication::query()
            ->with('primaryApplicant')
            ->whereRaw('UPPER(status) = ?', ['UNDER_REVIEW'])
            ->orderByDesc('submitted_at')
            ->paginate(15);

        return view('officer.review', compact('loans'));
    }
}
