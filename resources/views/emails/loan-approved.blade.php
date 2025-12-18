<x-mail::message>

# Loan approved

Your loan application has been approved.

**Application Number:** {{ $applicationNumber }}

<x-mail::panel>
**Approved Amount:** {{ number_format((float) ($approvedAmount ?? 0), 2) }}

**Tenure:** {{ $tenureMonths ? $tenureMonths.' months' : '—' }}

@if(!is_null($interestRate))
**Interest Rate:** {{ $interestRate }}
@endif
</x-mail::panel>

<x-mail::button :url="$loanShowUrl">
View application
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
