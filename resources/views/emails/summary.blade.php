@component('mail::message')
{{ __('email.summary.header') }},

{{ __('email.summary.body') }}

@component('mail::table')

| {{ __('email.summary.table.header.item') }} | {{ __('email.summary.table.header.amount') }} |
|:----------------------------------------------:|:-----------------------------------------------:|
@foreach ($data as $item)
|{{ $item['name'] }}|{{ $item['amount'] }}|
@endforeach

@endcomponent

{{ __('Thanks') }},<br>
{{ config('app.name') }}
@endcomponent