<?php
/**
 * @var App\Models\User
 */
?>

@extends('backpack::layout')

@section('content-header')
    <section class="content-header">
      <h1>
        {{ trans('backpack::crud.preview') }} <span class="text-lowercase">{{ $crud->entity_name }}</span>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
        <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
        <li class="active">{{ trans('backpack::crud.preview') }}</li>
      </ol>
    </section>
@endsection

@section('content')
    @if ($crud->hasAccess('list'))
        <a href="{{ url($crud->route) }}"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }}</a><br><br>
    @endif

    <!-- Default box -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            {!! __("User ") . "<b>" . $entry->name . "</b>" !!}
          </h3>
        </div>
        <div class="box-body">
          <div class="col-lg-8">
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td>ID</td>
                <td>{{ $entry->id }}</td>
              </tr>
              <tr>
                <td>{{ __('Name') }}</td>
                <td>{{ $entry->name }}</td>
              </tr>
              <tr>
                <td>{{ __('Balance') }}</td>
                <td>{{ HAmount::pointsWithPartner($entry->balance, $entry->partner) }}</td>
              </tr>
              <tr>
                <td>Email</td>
                <td>{{ $entry->email }}</td>
              </tr>
              <tr>
                <td>{{ __('Phone') }}</td>
                <td>{{ $entry->phone }}</td>
              </tr>
              <tr>
                <td>{{ __('Created')}}</td>
                <td>{{ $entry->created_at }}</td>
              </tr>
              <tr>
                <td>{{ __('Referral link')}}</td>
                <td>{!! $entry->referral_link ? \Html::link($entry->referral_link) : null !!}</td>
              </tr>
              <tr>
                <td>{{ __('Referral promo code')}}</td>
                <td>{{ $entry->referral_promo_code }}</td>
              </tr>
              <tr>
                <td>{{ __('Referrer') }}</td>
                <td>
                  @if (is_null($entry->referrer))
                    &mdash;
                  @else
                    <a href="/admin/user/{{ $entry->referrer->id }}">{{ $entry->referrer->getTitle() }}</a>
                  @endif
                </td>
              </tr>
              <tr>
                <td>{{ __('Signup type')}}</td>
                <td>{!! $entry->signup_type !!}</td>
              </tr>
              <tr>
                <td>{{ __("Other data") }}</td>
                <td><pre><?= json_encode($entry->data, JSON_PRETTY_PRINT); ?></pre></td>
              </tr>

            </tbody>
          </table>
          </div>
          <div class="col-lg-4">
            <img style="max-width: 220px; max-height: 220px;" src="<?= HUser::getPictureOrPlaceholder($entry); ?>" />
          </div>
        </div>
      </div>

      @if (!$entry->partner->isBitrewardsEnabled() || \App::environment() != 'production')
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            {{ __('Actions')}}
          </h3>
        </div>
        <div class="box-body">
          <div class="col-lg-4">
            {!! Form::open(['url' => route('admin.user.giveBonus')]) !!}
            @if (isset($customBonusActions) && count($customBonusActions) > 0)
              <div class="form-group">
                <label for="custom_bonus_action_id">{{ __('You can choose Action for which bonus points is issued') }}</label>
                <select name="action_id" id="custom_bonus_action_id" class="form-control">
                  <option value="0">{{ __('Choose Action') }}</option>
                  @foreach ($customBonusActions as $action)
                    <option value="{{ $action['id'] }}" data-points="{{ $action['raw_value'] }}" data-system="{{ $action['is_system'] }}">
                      {{ $action['title'] }}
                    </option>
                  @endforeach
                </select>
              </div>
            @endif
            <div class="form-group">
              <label for="custom_bonus_comment">{{ __('Reason for issuing a bonus') }}</label>
              <input type="text" class="form-control" name="comment" id="custom_bonus_comment" placeholder="{{ __('Comment')  }}" required>
            </div>
            <div class="input-group input-group-sm">
              <input type="hidden" value="{{ $entry->id }}" name="user_id">
              <input type="text" class="form-control" name="bonus" id="bonus_points_value" placeholder="{{ __('Points') }}" required>
              <div class="input-group-btn">
                <button type="submit" class="btn btn-info btn-flat">{{ __('Give bonus') }}</button>
              </div>
            </div>
            {!! Form::close() !!}
          </div>
        </div>
      </div>
      @endif

      @if (count($entry->transactions))
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ __('Transactions')}}
            </h3>
          </div>
          <div class="box-body">
              <table class="table table-bordered">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>{{ __('Status') }}</th>
                  <th>{{ __('Date') }}</th>
                  <th>{{ __('Transaction') }}</th>
                  <th>{{ __('Balance change') }}</th>
                  <th>{{ __('CMS Order Number') }}</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                  <?php foreach ($entry->transactions as $transaction) {
    /*
     * @var \App\Models\Transaction $transaction
     */ ?>
                    <tr>
                      <td>{{ $transaction->id }}</td>
                      <td><?=  HTransaction::getStatusStr($transaction); ?></td>
                      <td>{{ $transaction->created_at }}</td>
                      <td><?= HTransaction::getTitle($transaction); ?></td>
                      <td>
                          <?= ($transaction->balance_change > 0 ? '+' : '').HAmount::pointsWithPartner($transaction->balance_change, $transaction->partner); ?>
                          <?php if ($transaction->action_id && $transaction->action->isOrderBased()) {
        ?>
                            (<?= mb_strtolower(__('Total Amount')); ?> <?= HAmount::fShort($transaction->sourceStoreEntity->getDataProcessor()->getAmountTotal(), $transaction->partner->currency); ?>)
                          <?php
    } ?>
                      </td>
                      <td>
                        <?= $transaction->sourceStoreEvent && $transaction->sourceStoreEvent->converter_type ? $transaction->sourceStoreEvent->getConverter()->getOriginalOrderid() : null; ?>
                      </td>
                      <td>
                        <?php if (App\Models\Transaction::STATUS_CONFIRMED != $transaction->status) {
        ?>
                          <a href="<?= route('admin.transaction.confirm', ['id' => $transaction->id]); ?>" class="btn btn-xs btn-default tipped" data-tooltip="Подтвердить транзакцию" title=""><i class="fa fa-check"></i></a>
                        <?php
    } ?>

                        <?php if (App\Models\Transaction::STATUS_REJECTED != $transaction->status) {
        ?>
                          <a href="<?= route('admin.transaction.reject', ['id' => $transaction->id]); ?>" class="btn btn-xs btn-default tipped" data-tooltip="Отклонить транзакцию" title=""><i class="fa fa-close"></i></a>
                        <?php
    } ?>
                      </td>
                    </tr>
                  <?php
} ?>
                </tbody>
              </table> 
          </div><!-- /.box-body -->
        </div><!-- /.box -->
      @endif

@endsection

@section('after_scripts')
  <script>
    $(function () {
        $('#custom_bonus_action_id').on('change', function () {
            var pointsInput = $('#bonus_points_value');
            pointsInput.removeAttr('readonly');

            if ($(this).val() == 0) {
                return pointsInput.val('0');
            }

            var selected = $('option:selected', $(this));
            var points = parseInt(selected.data('points'), 10);

            pointsInput.val(points);

            if (parseInt(selected.data('system')) === 0) {
                pointsInput.attr('readonly', 'readonly');
            }
        });
    })
  </script>
@endsection
