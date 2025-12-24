<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-5">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Step 8: Documents & Submit</h3>
            <p class="mt-2 text-sm text-gray-500">Upload required documents to complete your application.</p>
            <p class="mt-1 text-sm text-gray-500">Fields marked <span class="text-red-500">*</span> are mandatory.</p>
        </div>

        <div class="space-y-6">
            @foreach($documentTypes as $type => $label)
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-bold text-gray-900">{{ $label }} @if(in_array($type, $requiredDocumentTypes)) <span class="text-red-500">*</span> @endif</h4>
                        @php
                            $existing = $documentsByType[$type] ?? null;
                        @endphp
                        @if($existing)
                            <div class="text-sm text-gray-600">Uploaded: {{ $existing->original_name ?? $existing->file_path }}</div>
                        @else
                            <div class="text-sm text-gray-400">Not uploaded</div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 items-center">
                        <div class="sm:col-span-2">
                            <form method="POST" action="{{ route('loan.document.upload', ['loan' => $loan->id]) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="document_type" value="{{ $type }}">
                                <div class="flex gap-3 items-center">
                                    <input type="file" name="file" accept="application/pdf,image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700" />
                                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Upload</button>
                                </div>
                            </form>
                        </div>

                        <div class="sm:col-span-1 text-right">
                            @if($existing)
                                <a href="{{ route('loan.document.signed-link', ['document' => $existing->id]) }}" class="text-sm text-indigo-600">Download</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <p class="text-sm text-gray-700">Once all required documents are uploaded, click <strong>Save & Continue</strong> to submit your application. By submitting you agree to our Terms & Conditions and the application will be sent for processing.</p>
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 7]) }}" class="text-sm font-semibold leading-6 text-gray-900">
                <span aria-hidden="true">&larr;</span> Back
            </a>

            <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 8]) }}">
                @csrf
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Save & Continue</button>
            </form>
        </div>
    </div>
</x-wizard-layout>