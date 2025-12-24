<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UnderwritingRule;
use App\Services\Underwriting\UnderwritingEngine;
use App\Services\Underwriting\UnderwritingFactsBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UnderwritingRuleController extends Controller
{
    public function index(): View
    {
        $rules = UnderwritingRule::query()->orderByDesc('id')->paginate(15);

        return view('underwriting.rules.index', compact('rules'));
    }

    public function create(): View
    {
        $rule = new UnderwritingRule([
            'name' => 'Default Rule Set',
            'active' => false,
            'rules_json' => [
                'hard_rules' => [],
                'score_rules' => [],
                'thresholds' => [
                    'approve' => 70,
                    'reject' => 40,
                ],
            ],
        ]);

        $json = json_encode($rule->rules_json ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return view('underwriting.rules.create', compact('rule', 'json'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'active' => ['nullable', 'boolean'],
            'rules_json' => ['required', 'string'],
        ]);

        $decoded = json_decode($data['rules_json'], true);
        if (!is_array($decoded)) {
            throw ValidationException::withMessages(['rules_json' => 'Rules JSON must be valid JSON object/array.']);
        }

        $rule = UnderwritingRule::create([
            'name' => $data['name'],
            'active' => (bool) ($data['active'] ?? false),
            'rules_json' => $decoded,
        ]);

        if ($rule->active) {
            UnderwritingRule::query()->where('id', '!=', $rule->id)->update(['active' => false]);
        }

        return redirect()->route('officer.underwriting.rules.edit', $rule)->with('success', 'Underwriting rule created.');
    }

    public function edit(UnderwritingRule $rule): View
    {
        $json = json_encode($rule->rules_json ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return view('underwriting.rules.edit', compact('rule', 'json'));
    }

    public function update(Request $request, UnderwritingRule $rule): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'active' => ['nullable', 'boolean'],
            'rules_json' => ['required', 'string'],
        ]);

        $decoded = json_decode($data['rules_json'], true);
        if (!is_array($decoded)) {
            throw ValidationException::withMessages(['rules_json' => 'Rules JSON must be valid JSON object/array.']);
        }

        $rule->update([
            'name' => $data['name'],
            'active' => (bool) ($data['active'] ?? false),
            'rules_json' => $decoded,
        ]);

        if ($rule->active) {
            UnderwritingRule::query()->where('id', '!=', $rule->id)->update(['active' => false]);
        }

        $indexRoute = auth()->user()?->role?->name === 'admin'
            ? 'underwriting.rules.index'
            : 'officer.underwriting.rules.index';

        return redirect()->route($indexRoute)->with('success', 'Underwriting rule updated.');
    }

    public function activate(UnderwritingRule $rule): RedirectResponse
    {
        UnderwritingRule::query()->update(['active' => false]);
        $rule->update(['active' => true]);

        return back()->with('success', 'Activated rule set: '.$rule->name);
    }

    public function deactivate(UnderwritingRule $rule): RedirectResponse
    {
        $rule->update(['active' => false]);

        return back()->with('success', 'Deactivated rule set: '.$rule->name);
    }

    public function test(Request $request, UnderwritingRule $rule): View
    {
        $data = $request->validate([
            'loan_application_id' => ['required', 'uuid'],
        ]);

        $loan = \App\Models\LoanApplication::query()->findOrFail($data['loan_application_id']);

        $facts = (new UnderwritingFactsBuilder())->build($loan);
        $result = (new UnderwritingEngine())->evaluate($rule->rules_json ?? [], $facts, $rule);

        return view('underwriting.rules.test_result', compact('rule', 'loan', 'facts', 'result'));
    }
}
