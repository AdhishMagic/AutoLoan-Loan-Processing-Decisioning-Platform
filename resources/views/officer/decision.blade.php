<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Underwriting Decision Preview
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('officer.loans.show', $loan) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Back to Loan</a>
                <a href="{{ route('officer.review') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Review Queue</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Reference</div>
                        <div class="font-medium">{{ $loan->application_number ?? $loan->id }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Engine Decision</div>
                        <div class="font-medium">{{ $result->decision }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Score</div>
                        <div class="font-medium">{{ $result->score }}</div>
                    </div>
                </div>
                <div class="mt-3 text-sm text-gray-600">
                    Active Rule: <span class="font-medium">{{ $activeRule?->name ?? '—' }}</span>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-2">Confidence Chart</h3>
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
                        <div class="mb-2 text-gray-600">Thresholds</div>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Approve at: <span class="font-medium">{{ $approveAt }}</span></li>
                            @if($manualReviewAt !== null)
                                <li>Manual review at: <span class="font-medium">{{ $manualReviewAt }}</span></li>
                            @endif
                            <li>Reject at: <span class="font-medium">{{ $rejectAt }}</span></li>
                        </ul>
                        <div class="mt-4 text-gray-700">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-2">Reasons</h3>
                @if(empty($result->reasons))
                    <div class="text-sm text-gray-500">—</div>
                @else
                    <pre class="text-xs whitespace-pre-wrap">{{ json_encode($result->reasons, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-2">Facts Snapshot</h3>
                <pre class="text-xs whitespace-pre-wrap">{{ json_encode($facts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('decisionChart');
        const data = {
            labels: ['Approve Confidence', 'Reject Confidence', 'Hold Confidence'],
            datasets: [{
                data: [{{ $approveConfidence }}, {{ $rejectConfidence }}, {{ $holdConfidence }}],
                backgroundColor: ['#22c55e', '#ef4444', '#f59e0b'],
                borderColor: ['#16a34a', '#dc2626', '#d97706'],
                borderWidth: 1
            }]
        };
        new Chart(ctx, {
            type: 'doughnut',
            data,
            options: {
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { enabled: true }
                },
                cutout: '60%'
            }
        });
    </script>
</x-app-layout>
