<x-mail::message>
# Halo, {{ $owner->name }}!

{!! $content !!}

Terima kasih,<br>
{{ config('app.name') }} Team
</x-mail::message>
