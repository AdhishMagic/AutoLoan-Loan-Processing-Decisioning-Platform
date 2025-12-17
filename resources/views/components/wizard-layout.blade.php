<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Loan Application') }} <span class="text-gray-500 text-sm">#{{ $loan->application_number ?? 'NEW' }}</span>
            </h2>
            <span class="px-3 py-1 text-sm rounded-full {{ $loan->status === 'DRAFT' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                {{ $loan->status ?? 'DRAFT' }}
            </span>
        </div>
    </x-slot>

    <!-- Progress Bar (Sticky Top) -->
    <div class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
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
                                        <div class="h-0.5 w-full {{ $key < $currentStep ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
                                    @endif
                                </div>
                                <a href="{{ $loan->id ? route('loans.step.show', ['loan' => $loan->id, 'step' => $key]) : '#' }}" 
                                   class="relative flex h-8 w-8 items-center justify-center rounded-full {{ $key < $currentStep ? 'bg-indigo-600 hover:bg-indigo-900' : ($key == $currentStep ? 'border-2 border-indigo-600 bg-white' : 'border-2 border-gray-300 bg-white hover:border-gray-400') }}">
                                    @if($key < $currentStep)
                                        <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                    @elseif($key == $currentStep)
                                        <span class="h-2.5 w-2.5 rounded-full bg-indigo-600" aria-hidden="true"></span>
                                    @else
                                        <!-- Future Step -->
                                    @endif
                                    <span class="absolute -bottom-8 w-32 text-center text-xs font-medium {{ $key == $currentStep ? 'text-indigo-600' : 'text-gray-500' }}">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
