<?php

namespace Bitrewards\ReferralTool\Traits;

trait DefaultRangesTrait
{
    public function ranges(): array
    {
        return [
            30 => __('Last Months'),
            7 => __('Last Week'),
            14 => __('Last 2 Weeks'),
            60 => __('Last 2 Months'),
            180 => __('Last 6 Months'),
            365 => __('Last Year'),
        ];
    }

    public function getDefaultRange(): int
    {
        return key($this->ranges());
    }
}
