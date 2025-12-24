<?php

namespace App\Services\Underwriting;

use App\Models\UnderwritingRule;
use Illuminate\Support\Arr;

class UnderwritingEngine
{
    public const DEFAULT_ENGINE_NAME = 'underwriting_engine';
    public const DEFAULT_ENGINE_VERSION = '1';

    /**
     * Evaluate a rule-set (from DB or provided array) against facts.
     *
     * Supported JSON structures (tolerant):
     *
     * A) Structured rule format:
     * - hard_rules: [{ when: { field, op, value }, then: { decision, reason } }]
     * - score_rules: [{ conditions: [{ field, op, value }, ...], weight, reason }]
     * - thresholds: { approve: int, reject: int, hold?: int }
     *
     * B) Expression rule format (matches the currently active DB rules):
     * - hard_rules: [{ condition: "dti > 55", decision: "rejected", reason: "..." }]
     * - score_rules: [{ condition: "credit_score >= 750", points: 40, label: "..." }]
     * - thresholds: { approve: int, manual_review: int }
     */
    public function evaluate(array $rulesJson, array $facts, ?UnderwritingRule $ruleModel = null): UnderwritingResult
    {
        $trace = [];
        $reasons = [];

        $hardRules = Arr::get($rulesJson, 'hard_rules', []);
        $scoreRules = Arr::get($rulesJson, 'score_rules', []);
        $thresholds = Arr::get($rulesJson, 'thresholds', []);

        // 1) Hard rules
        foreach ((array) $hardRules as $rule) {
            $evaluation = $this->evaluateRule($rule, $facts);
            $trace[] = $evaluation['trace'];

            if (($evaluation['matched'] ?? false) && isset($evaluation['decision'])) {
                if (!empty($evaluation['reason'])) {
                    $reasons[] = [
                        'type' => 'HARD_RULE',
                        'rule_id' => $evaluation['rule_id'],
                        'rule_name' => $evaluation['rule_name'],
                        'message' => $evaluation['reason'],
                    ];
                }

                return new UnderwritingResult(
                    decision: (string) $evaluation['decision'],
                    score: (int) ($evaluation['score'] ?? 0),
                    reasons: $reasons,
                    trace: $trace,
                    underwritingRuleId: $ruleModel?->id,
                    underwritingRuleName: $ruleModel?->name,
                );
            }
        }

        // 2) Score rules
        $score = 0;
        foreach ((array) $scoreRules as $rule) {
            $evaluation = $this->evaluateRule($rule, $facts);
            $trace[] = $evaluation['trace'];

            if (($evaluation['matched'] ?? false)) {
                $weight = (int) ($evaluation['weight'] ?? 0);
                $score += $weight;

                if (!empty($evaluation['reason'])) {
                    $reasons[] = [
                        'type' => 'SCORE_RULE',
                        'rule_id' => $evaluation['rule_id'],
                        'rule_name' => $evaluation['rule_name'],
                        'weight' => $weight,
                        'message' => $evaluation['reason'],
                    ];
                }
            }
        }

        $approveAt = $this->intOrNull(Arr::get($thresholds, 'approve'));

        // Expression-rule format uses manual_review threshold instead of reject/hold.
        $manualReviewAt = $this->intOrNull(Arr::get($thresholds, 'manual_review'));
        $rejectAt = $this->intOrNull(Arr::get($thresholds, 'reject'));
        $holdAt = $this->intOrNull(Arr::get($thresholds, 'hold'));

        // Defaults if thresholds not provided.
        $approveAt ??= 70;
        $manualReviewAt ??= null;
        $rejectAt ??= 40;

        $decision = 'HOLD';
        if ($score >= $approveAt) {
            $decision = 'APPROVE';
        } elseif ($manualReviewAt !== null) {
            $decision = $score >= $manualReviewAt ? 'HOLD' : 'REJECT';
        } elseif ($score <= $rejectAt) {
            $decision = 'REJECT';
        } elseif ($holdAt !== null && $score >= $holdAt) {
            $decision = 'HOLD';
        }

        return new UnderwritingResult(
            decision: $decision,
            score: $score,
            reasons: $reasons,
            trace: $trace,
            underwritingRuleId: $ruleModel?->id,
            underwritingRuleName: $ruleModel?->name,
        );
    }

    /**
     * @return array{matched: bool, decision?: string, score?: int, weight?: int, reason?: string, rule_id: string, rule_name: ?string, trace: array<string, mixed>}
     */
    private function evaluateRule(array $rule, array $facts): array
    {
        $ruleId = (string) (Arr::get($rule, 'id') ?? Arr::get($rule, 'key') ?? 'rule');
        $ruleName = Arr::get($rule, 'name') ?? Arr::get($rule, 'label');

        $when = Arr::get($rule, 'when');
        $conditions = Arr::get($rule, 'conditions');
        $expression = Arr::get($rule, 'condition');

        $matched = false;
        $details = [];

        if (is_string($expression) && $expression !== '') {
            [$matched, $details] = $this->evaluateExpression($expression, $facts);
        } elseif (is_array($when)) {
            [$matched, $details] = $this->evaluateCondition($when, $facts);
        } elseif (is_array($conditions)) {
            $matched = true;
            $details = [];
            foreach ((array) $conditions as $cond) {
                if (!is_array($cond)) {
                    $matched = false;
                    continue;
                }
                [$ok, $condDetails] = $this->evaluateCondition($cond, $facts);
                $details[] = $condDetails;
                if (!$ok) {
                    $matched = false;
                }
            }
        } else {
            // If no condition, treat as not matched.
            $matched = false;
        }

        $decision = Arr::get($rule, 'then.decision')
            ?? Arr::get($rule, 'decision');

        $reason = Arr::get($rule, 'then.reason')
            ?? Arr::get($rule, 'reason')
            ?? Arr::get($rule, 'label')
            ?? Arr::get($rule, 'message');

        $weight = Arr::get($rule, 'then.weight')
            ?? Arr::get($rule, 'weight')
            ?? Arr::get($rule, 'points');

        $trace = [
            'rule_id' => $ruleId,
            'rule_name' => $ruleName,
            'matched' => $matched,
            'condition' => $when,
            'conditions' => $conditions,
            'expression' => $expression,
            'condition_details' => $details,
            'weight' => $weight,
            'decision' => $decision,
        ];

        $out = [
            'rule_id' => $ruleId,
            'rule_name' => is_string($ruleName) ? $ruleName : null,
            'matched' => $matched,
            'trace' => $trace,
        ];

        if ($matched && is_string($decision) && $decision !== '') {
            $out['decision'] = $this->normalizeDecision($decision);
        }

        if ($matched && $reason !== null) {
            $out['reason'] = (string) $reason;
        }

        if ($matched && $weight !== null) {
            $out['weight'] = (int) $weight;
        }

        return $out;
    }

    private function normalizeDecision(string $decision): string
    {
        $d = strtolower(trim($decision));
        return match (true) {
            str_contains($d, 'approve') => 'APPROVE',
            str_contains($d, 'reject') => 'REJECT',
            str_contains($d, 'manual') => 'HOLD',
            str_contains($d, 'hold') => 'HOLD',
            default => strtoupper($decision),
        };
    }

    /**
     * Safely evaluate a simple boolean expression like:
     * - "credit_score >= 650 && credit_score < 750"
     * - "dti > 55"
     *
     * Supported operators: <, <=, >, >=, ==, !=, &&, || and parentheses.
     *
     * @return array{0: bool, 1: array<string, mixed>}
     */
    private function evaluateExpression(string $expression, array $facts): array
    {
        $tokens = $this->tokenizeExpression($expression);
        $rpn = $this->toRpn($tokens);

        $stack = [];
        $used = [];

        foreach ($rpn as $token) {
            if ($token['type'] === 'number') {
                $stack[] = (float) $token['value'];
                continue;
            }

            if ($token['type'] === 'identifier') {
                $name = (string) $token['value'];
                $value = Arr::get($facts, $name);
                $used[$name] = $value;
                $stack[] = $value;
                continue;
            }

            $op = (string) $token['value'];

            if (in_array($op, ['&&', '||'], true)) {
                $b = array_pop($stack);
                $a = array_pop($stack);
                $av = (bool) $a;
                $bv = (bool) $b;
                $stack[] = $op === '&&' ? ($av && $bv) : ($av || $bv);
                continue;
            }

            // Comparisons
            $right = array_pop($stack);
            $left = array_pop($stack);

            if (!is_numeric($left) || !is_numeric($right)) {
                $stack[] = false;
                continue;
            }

            $l = (float) $left;
            $r = (float) $right;

            $stack[] = match ($op) {
                '>' => $l > $r,
                '>=' => $l >= $r,
                '<' => $l < $r,
                '<=' => $l <= $r,
                '==' => $l == $r,
                '!=' => $l != $r,
                default => false,
            };
        }

        $result = (bool) (array_pop($stack) ?? false);

        return [$result, [
            'expression' => $expression,
            'tokens' => $tokens,
            'used_facts' => $used,
            'matched' => $result,
        ]];
    }

    /**
     * @return array<int, array{type: string, value: string|float}>
     */
    private function tokenizeExpression(string $expr): array
    {
        $expr = trim($expr);
        $len = strlen($expr);
        $i = 0;
        $tokens = [];

        while ($i < $len) {
            $ch = $expr[$i];

            if (ctype_space($ch)) {
                $i++;
                continue;
            }

            // Two-char operators
            $two = $i + 1 < $len ? $ch.$expr[$i + 1] : '';
            if (in_array($two, ['>=', '<=', '==', '!=', '&&', '||'], true)) {
                $tokens[] = ['type' => 'op', 'value' => $two];
                $i += 2;
                continue;
            }

            // Single-char operators / parens
            if (in_array($ch, ['>', '<', '(', ')'], true)) {
                $tokens[] = ['type' => $ch === '(' || $ch === ')' ? 'paren' : 'op', 'value' => $ch];
                $i++;
                continue;
            }

            // Number
            if (ctype_digit($ch) || $ch === '.') {
                $start = $i;
                $i++;
                while ($i < $len && (ctype_digit($expr[$i]) || $expr[$i] === '.')) {
                    $i++;
                }
                $num = substr($expr, $start, $i - $start);
                $tokens[] = ['type' => 'number', 'value' => (float) $num];
                continue;
            }

            // Identifier (letters, underscores)
            if (ctype_alpha($ch) || $ch === '_') {
                $start = $i;
                $i++;
                while ($i < $len && (ctype_alnum($expr[$i]) || $expr[$i] === '_' || $expr[$i] === '.')) {
                    $i++;
                }
                $id = substr($expr, $start, $i - $start);
                $tokens[] = ['type' => 'identifier', 'value' => $id];
                continue;
            }

            // Unknown char => skip (keeps evaluator safe)
            $i++;
        }

        return $tokens;
    }

    /**
     * Convert tokens to Reverse Polish Notation using shunting-yard.
     *
     * @param array<int, array{type: string, value: string|float}> $tokens
     * @return array<int, array{type: string, value: string|float}>
     */
    private function toRpn(array $tokens): array
    {
        $out = [];
        $ops = [];

        $precedence = fn (string $op): int => match ($op) {
            '>', '>=', '<', '<=', '==', '!=' => 3,
            '&&' => 2,
            '||' => 1,
            default => 0,
        };

        foreach ($tokens as $t) {
            if ($t['type'] === 'number' || $t['type'] === 'identifier') {
                $out[] = $t;
                continue;
            }

            if ($t['type'] === 'paren' && $t['value'] === '(') {
                $ops[] = $t;
                continue;
            }

            if ($t['type'] === 'paren' && $t['value'] === ')') {
                while (!empty($ops)) {
                    $op = array_pop($ops);
                    if ($op['type'] === 'paren' && $op['value'] === '(') {
                        break;
                    }
                    $out[] = $op;
                }
                continue;
            }

            if ($t['type'] === 'op') {
                $opVal = (string) $t['value'];
                while (!empty($ops)) {
                    $top = $ops[count($ops) - 1];
                    if ($top['type'] !== 'op') {
                        break;
                    }
                    if ($precedence((string) $top['value']) >= $precedence($opVal)) {
                        $out[] = array_pop($ops);
                        continue;
                    }
                    break;
                }
                $ops[] = $t;
            }
        }

        while (!empty($ops)) {
            $out[] = array_pop($ops);
        }

        return $out;
    }

    /**
     * @return array{0: bool, 1: array<string, mixed>}
     */
    private function evaluateCondition(array $condition, array $facts): array
    {
        $field = (string) (Arr::get($condition, 'field') ?? '');
        $op = strtolower((string) (Arr::get($condition, 'op') ?? Arr::get($condition, 'operator') ?? ''));
        $expected = Arr::get($condition, 'value');

        $actual = $field !== '' ? Arr::get($facts, $field) : null;

        $ok = match ($op) {
            'eq', '==' => $actual == $expected,
            'neq', '!=' => $actual != $expected,
            'gt', '>' => is_numeric($actual) && is_numeric($expected) ? ((float) $actual > (float) $expected) : false,
            'gte', '>=' => is_numeric($actual) && is_numeric($expected) ? ((float) $actual >= (float) $expected) : false,
            'lt', '<' => is_numeric($actual) && is_numeric($expected) ? ((float) $actual < (float) $expected) : false,
            'lte', '<=' => is_numeric($actual) && is_numeric($expected) ? ((float) $actual <= (float) $expected) : false,
            'in' => is_array($expected) ? in_array($actual, $expected, true) : false,
            'not_in' => is_array($expected) ? !in_array($actual, $expected, true) : false,
            'contains' => $this->contains($actual, $expected),
            'exists' => $actual !== null,
            'not_exists' => $actual === null,
            'regex' => is_string($actual) && is_string($expected) ? (bool) preg_match($expected, $actual) : false,
            default => false,
        };

        return [$ok, [
            'field' => $field,
            'op' => $op,
            'expected' => $expected,
            'actual' => $actual,
            'matched' => $ok,
        ]];
    }

    private function contains(mixed $actual, mixed $expected): bool
    {
        if (is_array($actual)) {
            return in_array($expected, $actual, true);
        }

        if (is_string($actual) && is_string($expected)) {
            return str_contains($actual, $expected);
        }

        return false;
    }

    private function intOrNull(mixed $v): ?int
    {
        if ($v === null) {
            return null;
        }

        if (is_numeric($v)) {
            return (int) $v;
        }

        return null;
    }
}
