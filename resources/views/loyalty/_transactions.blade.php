<?php
/**
 * @var \App\DTO\PartnerPageData
 */
?>

@if (empty($partnerPage->transactions))
  @include('loyalty/_empty-reward')
@else
  @foreach ($partnerPage->transactions as $transaction)
    @include('loyalty/_transaction-row')
  @endforeach
@endif
