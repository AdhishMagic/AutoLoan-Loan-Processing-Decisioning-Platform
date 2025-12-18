<x-mail::message>

# New loan received for verification

A new loan application has been submitted and is ready for verification.

<x-mail::panel>
**Applicant:** {{ $applicantName }}

**Application Number:** {{ $applicationNumber }}
</x-mail::panel>

<x-mail::button :url="$officerDashboardUrl">
Open verification dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
