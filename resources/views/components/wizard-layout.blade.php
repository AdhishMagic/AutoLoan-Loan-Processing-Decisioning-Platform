<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-text-primary leading-tight">
                {{ __('Loan Application') }} <span class="text-text-muted text-sm">#{{ $loan->application_number ?? 'NEW' }}</span>
            </h2>
            @php($wizardStatus = strtoupper((string) ($loan->status ?? 'DRAFT')))
            <span class="px-3 py-1 text-sm rounded-full ring-1 ring-app-border {{ $wizardStatus === 'DRAFT' ? 'bg-app-bg text-text-secondary' : ($wizardStatus === 'APPROVED' ? 'bg-status-success/10 text-status-success' : ($wizardStatus === 'REJECTED' ? 'bg-status-danger/10 text-status-danger' : 'bg-status-info/10 text-status-info')) }}">
                {{ $wizardStatus }}
            </span>
        </div>
    </x-slot>

    <!-- Progress Bar (Sticky Top) -->
    <div class="sticky top-0 z-50 bg-app-surface border-b border-app-border shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-4">
                <nav aria-label="Progress">
                    <ol role="list" class="flex items-center">
                        @php
                            $steps = [
                                1 => 'Overview',
                                2 => 'Applicants',
                                3 => 'Income',
                                4 => 'Financials',
                                5 => 'Property',
                                6 => 'References',
                                7 => 'Declarations',
                                8 => 'Review'
                            ];
                            $currentStep = $step ?? 1;
                        @endphp

                        @foreach($steps as $key => $label)
                            <li class="relative pr-8 sm:pr-20 {{ $loop->last ? 'pr-0' : '' }}">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    @if(!$loop->last)
                                        <div class="h-0.5 w-full {{ $key < $currentStep ? 'bg-brand-secondary' : 'bg-app-border' }}"></div>
                                    @endif
                                </div>
                                <a href="{{ $loan->id ? route('loans.step.show', ['loan' => $loan->id, 'step' => $key]) : '#' }}" 
                                   class="relative flex h-8 w-8 items-center justify-center rounded-full {{ $key < $currentStep ? 'bg-brand-secondary hover:bg-brand-secondary/90' : ($key == $currentStep ? 'border-2 border-brand-secondary bg-app-surface' : 'border-2 border-app-border bg-app-surface hover:border-text-muted') }}">
                                    @if($key < $currentStep)
                                        <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                    @elseif($key == $currentStep)
                                        <span class="h-2.5 w-2.5 rounded-full bg-brand-secondary" aria-hidden="true"></span>
                                    @else
                                        <!-- Future Step -->
                                    @endif
                                    <span class="absolute -bottom-8 w-32 text-center text-xs font-medium {{ $key == $currentStep ? 'text-text-primary' : 'text-text-muted' }}">
                                        {{ $label }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-surface border border-app-border overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-text-primary">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
