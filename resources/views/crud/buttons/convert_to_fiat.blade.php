@if ($crud->hasAccess('convert_to_fiat'))
    <a href="{{ url($crud->route.'/convertRewards/'.$entry->getKey()) }}" class="btn btn-xs btn-default" onclick="if (!confirm('<?= __('Convert value of all rewards in fiat') ?>')) return false;" data-tooltip="<?= __('Convert value of all rewards in fiat')?>"><i class="fa fa-money"></i></a>
@endif