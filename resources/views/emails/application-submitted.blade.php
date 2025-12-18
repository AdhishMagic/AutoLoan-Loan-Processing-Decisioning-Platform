<x-mail::message>

# Application submitted

Hi {{ $applicantName }},

We’ve received your loan application.

**Application Number:** {{ $applicationNumber }}

<x-mail::button :url="$loanShowUrl">
View application
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
