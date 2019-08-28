@extends('emails.templates.sunny')

@section('content')
    @include('emails.templates.sunny.contentStart')
    <p>{!! __('Hello!') !!}</p>
    <?php if ($isFiatReferralEnabled): ?>
        <p>{!! __('You got %discount% discount in %shop%. This promo-code is stored in your referral program wallet associated with %login%.', [
            'discount' => $discount,
            'shop' => $shop,
            'login' => $login,
        ]) !!}</p>
    <?php else: ?>
        <p>{!! __('You got %discount% discount in %shop%. This promo-code is stored in your personal customer wallet associated with %login%.', [
            'discount' => $discount,
            'shop' => $shop,
            'login' => $login,
        ]) !!}</p>
    <?php endif; ?>

    @if ($couponExpireAt)
        <p>{!! __('This promo-code expires on %expire%.', ['expire' => $couponExpireAt]) !!}</p>
    @endif

    @include('emails.templates.sunny.contentEnd')

    @include('emails.templates.sunny.button', [
        'title' => __('Sign in to my wallet'),
    ])
@stop