@component('mail::message')
# Dear, {{$user['name']}}

You are receiving this email because we received a forgot password request for your mail account.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

If you did not request a forgot password, no further action is required.

Thanks,
{{ config('app.name') }}
@endcomponent