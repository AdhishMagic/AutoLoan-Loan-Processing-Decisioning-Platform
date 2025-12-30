<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-text-primary leading-tight">
                Underwriting Decision Preview
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('officer.loans.show', $loan) }}" class="text-sm text-text-secondary hover:text-text-primary">Back to Loan</a>
                <a href="{{ route('officer.review') }}" class="text-sm text-text-secondary hover:text-text-primary">Review Queue</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-lg bg-status-warning/10 p-4 text-status-warning ring-1 ring-app-border">
                <div class="text-sm">
                    <span class="font-semibold">Final decision is manual.</span>
                    The system suggests <span class="font-semibold">{{ $result->decision }}</span> based on rules and score. Please review the reasons and choose Approve, Reject, or Hold.
                </div>
                @if(!empty($result->reasons))
                    @php
                        $topReasons = array_slice((array)($result->reasons ?? []), 0, 3);
                    @endphp
                    @if(!empty($topReasons))
                        <div class="mt-2">
                            <div class="text-sm font-medium text-text-primary">Top reasons</div>
                            <ul class="list-disc ml-5 text-sm space-y-1">
                                @foreach($topReasons as $r)
                                    <li>
                                        @if(is_array($r))
                                            {{ $r['message'] ?? $r['reason'] ?? $r['rule'] ?? $r['code'] ?? \Illuminate\Support\Str::limit(json_encode($r, JSON_UNESCAPED_SLASHES), 120) }}
                                        @else
                                            {{ $r }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-1 text-xs text-status-warning/80">See full details under "Reasons" below.</div>
                        </div>
                    @endif
                @endif
            </div>
            <div class="bg-app-surface overflow-hidden shadow-sm sm:rounded-lg p-6 ring-1 ring-app-border">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-text-secondary">Reference</div>
                        <div class="font-medium">{{ $loan->application_number ?? $loan->id }}</div>
                    </div>
                    <div>
                        <div class="text-text-secondary">Engine Decision</div>
                        <div class="font-medium">{{ $result->decision }}</div>
                    </div>
                    <div>
                        <div class="text-text-secondary">Score</div>
                        <div class="font-medium">{{ $result->score }}</div>
                    </div>
                </div>
                <div class="mt-3 text-sm text-text-secondary">
                    Active Rule: <span class="font-medium">{{ $activeRule?->name ?? '—' }}</span>
                </div>
            </div>

            <div class="bg-app-surface overflow-hidden shadow-sm sm:rounded-lg p-6 ring-1 ring-app-border">
                <h3 class="text-base font-semibold text-text-primary mb-2">Confidence Chart</h3>
                @php
                    $score = (int) ($result->score ?? 0);
                    $thresholds = (array) ($activeRule?->rules_json['thresholds'] ?? []);
                    $approveAt = (int) ($thresholds['approve'] ?? 70);
                    $rejectAt = isset($thresholds['reject']) ? (int) $thresholds['reject'] : 40;
                    $manualReviewAt = isset($thresholds['manual_review']) ? (int) $thresholds['manual_review'] : null;
                    $clamp = fn($v) => max(0, min(100, (int) $v));
                    $approveConfidence = $clamp($approveAt > 0 ? round(($score / $approveAt) * 100) : $score);
                    $rejectConfidence = $clamp($rejectAt > 0 ? round(((max($rejectAt - $score, 0)) / $rejectAt) * 100) : 0);
                    $holdConfidence = $manualReviewAt !== null ? $clamp($score >= $manualReviewAt ? round((($score - $manualReviewAt) / max(1, 100 - $manualReviewAt)) * 100) : 0) : 0;
                @endphp
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <canvas id="decisionChart" height="160"></canvas>
                    </div>
                    <div class="text-sm">
                        <div class="mb-2 text-text-secondary">Thresholds</div>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Approve at: <span class="font-medium">{{ $approveAt }}</span></li>
                            @if($manualReviewAt !== null)
                                <li>Manual review at: <span class="font-medium">{{ $manualReviewAt }}</span></li>
                            @endif
                            <li>Reject at: <span class="font-medium">{{ $rejectAt }}</span></li>
                        </ul>
                        <div class="mt-4 text-text-primary">
                            The computer suggests <span class="font-semibold">{{ $result->decision }}</span> based on rules and score. The loan officer makes the final decision.
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <form method="POST" action="{{ route('loans.approve', $loan) }}">
                                @csrf
                                <x-primary-button type="submit">Approve</x-primary-button>
                            </form>
                            <form method="POST" action="{{ route('loans.reject', $loan) }}">
                                @csrf
                                <x-danger-button type="submit">Reject</x-danger-button>
                            </form>
                            <form method="POST" action="{{ route('loans.hold', $loan) }}">
                                @csrf
                                <x-secondary-button type="submit">Hold</x-secondary-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-app-surface overflow-hidden shadow-sm sm:rounded-lg p-6 ring-1 ring-app-border">
                <h3 class="text-base font-semibold text-text-primary mb-2">Reasons</h3>
                @if(empty($result->reasons))
                    <div class="text-sm text-text-muted">—</div>
                @else
                    <pre class="text-xs whitespace-pre-wrap">{{ json_encode($result->reasons, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                @endif
            </div>

            <div class="bg-app-surface overflow-hidden shadow-sm sm:rounded-lg p-6 ring-1 ring-app-border">
                <h3 class="text-base font-semibold text-text-primary mb-2">Facts Snapshot</h3>
                <pre class="text-xs whitespace-pre-wrap">{{ json_encode($facts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('decisionChart');

        function cssRgb(varName) {
            const raw = getComputedStyle(document.documentElement).getPropertyValue(varName).trim();
            return raw ? `rgb(${raw})` : undefined;
        }

        function getChartTheme() {
            return {
                success: cssRgb('--status-success'),
                danger: cssRgb('--status-danger'),
                warning: cssRgb('--status-warning'),
                surface: cssRgb('--app-surface'),
                divider: cssRgb('--app-divider'),
                textPrimary: cssRgb('--text-primary'),
                textSecondary: cssRgb('--text-secondary'),
            };
        }

        function applyChartTheme(chart) {
            const t = getChartTheme();

            chart.data.datasets[0].backgroundColor = [t.success, t.danger, t.warning];
            chart.data.datasets[0].borderColor = t.surface;

            chart.options.plugins.legend.labels.color = t.textSecondary;
            chart.options.plugins.tooltip.backgroundColor = t.surface;
            chart.options.plugins.tooltip.borderColor = t.divider;
            chart.options.plugins.tooltip.titleColor = t.textPrimary;
            chart.options.plugins.tooltip.bodyColor = t.textSecondary;

            chart.update();
        }

        const data = {
            labels: ['Approve Confidence', 'Reject Confidence', 'Hold Confidence'],
            datasets: [{
                data: [{{ $approveConfidence }}, {{ $rejectConfidence }}, {{ $holdConfidence }}],
                borderWidth: 2,
            }]
        };

        const decisionChart = new Chart(ctx, {
            type: 'doughnut',
            data,
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: cssRgb('--text-secondary'),
                        },
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: cssRgb('--app-surface'),
                        borderColor: cssRgb('--app-divider'),
                        borderWidth: 1,
                        titleColor: cssRgb('--text-primary'),
                        bodyColor: cssRgb('--text-secondary'),
                        displayColors: true,
                    }
                },
                cutout: '60%'
            }
        });

        applyChartTheme(decisionChart);

        window.addEventListener('theme-changed', () => applyChartTheme(decisionChart));
    </script>
</x-app-layout>
