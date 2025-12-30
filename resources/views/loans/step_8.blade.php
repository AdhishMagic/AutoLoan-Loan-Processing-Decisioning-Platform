<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-app-border pb-5">
            <h3 class="text-base font-semibold leading-6 text-text-primary">Step 8: Documents & Submit</h3>
            <p class="mt-2 text-sm text-text-secondary">Upload required documents to complete your application.</p>
            <p class="mt-1 text-sm text-text-secondary">Fields marked <span class="text-status-danger">*</span> are mandatory.</p>
        </div>

        <div class="space-y-6">
            @foreach($documentTypes as $type => $label)
                <div class="bg-app-surface p-4 rounded-lg border border-app-border shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-bold text-text-primary">{{ $label }} @if(in_array($type, $requiredDocumentTypes)) <span class="text-status-danger">*</span> @endif</h4>
                        @php
                            $existing = $documentsByType[$type] ?? null;
                        @endphp
                        @if($existing)
                            <div class="text-sm text-text-secondary">Uploaded: {{ $existing->original_name ?? $existing->file_path }}</div>
                        @else
                            <div class="text-sm text-text-muted">Not uploaded</div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 items-center">
                        <div class="sm:col-span-2">
                            <form method="POST" action="{{ route('loan.document.upload', ['loan' => $loan->id]) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="document_type" value="{{ $type }}">
                                <div class="flex gap-3 items-center">
                                    <input type="file" name="file" accept="application/pdf,image/*" class="block w-full text-sm text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-app-bg file:text-text-primary" />
                                    <button type="submit" class="rounded-md bg-brand-accent px-3 py-2 text-sm font-semibold text-text-onDark shadow-sm hover:bg-brand-accent/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-focus">Upload</button>
                                </div>
                            </form>
                        </div>

                        <div class="sm:col-span-1 text-right">
                            @if($existing)
                                <a href="{{ route('loan.document.signed-link', ['document' => $existing->id]) }}" class="text-sm text-brand-secondary hover:underline">Download</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-app-surface p-4 rounded-lg border border-app-border shadow-sm">
            <p class="text-sm text-text-secondary">Once all required documents are uploaded, click <strong>Save & Continue</strong> to submit your application. By submitting you agree to our Terms & Conditions and the application will be sent for processing.</p>
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-app-border">
            <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 7]) }}" class="text-sm font-semibold leading-6 text-brand-secondary hover:underline">
                <span aria-hidden="true">&larr;</span> Back
            </a>

            <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 8]) }}">
                @csrf
                <button type="submit" class="rounded-md bg-brand-accent px-3 py-2 text-sm font-semibold text-text-onDark shadow-sm hover:bg-brand-accent/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-focus">Save & Continue</button>
            </form>
        </div>
    </div>
</x-wizard-layout>