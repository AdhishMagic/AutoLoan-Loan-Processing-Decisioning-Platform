<x-mail::message>

# Application update

We reviewed your loan application and we’re unable to proceed at this time.

**Application Number:** {{ $applicationNumber }}

If you believe this is unexpected, you can submit a new application later or contact support.

<x-mail::button :url="$loanShowUrl">
View application
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
