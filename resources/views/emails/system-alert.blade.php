<x-mail::message>
# {{ $title }}

{{ $bodyContent }}

@if(count($details) > 0)
<x-mail::table>
| Key | Value |
| :--- | :--- |
@foreach($details as $key => $value)
| {{ ucwords(str_replace('_', ' ', $key)) }} | {{ is_array($value) ? json_encode($value) : $value }} |
@endforeach
</x-mail::table>
@endif

Terima kasih,<br>
{{ config('app.name') }} System Monitor
</x-mail::message>
