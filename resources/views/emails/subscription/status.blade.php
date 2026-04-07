<x-mail::message>
# Hello {{ $user->name }},

@if($type === 'expired')
Your subscription plan **{{ $subscription->plan->name }}** has expired on **{{ $subscription->expires_at->format('d M Y') }}**.
Some premium features may have been disabled. Please renew your subscription to restore full access.
@else
Your subscription plan **{{ $subscription->plan->name }}** is expiring soon!
It will expire on **{{ $subscription->expires_at->format('d M Y') }}** ({{ $daysLeft }} days left).
Don't let your service be interrupted.
@endif

<x-mail::button :url="config('app.url') . '/admin/login'">
Renew Subscription
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
