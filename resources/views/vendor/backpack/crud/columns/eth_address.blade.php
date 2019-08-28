<?php
$wrapper = $column['viewer'] ?? null;
$url = null;

switch ($wrapper) {
    case 'ropsten.etherscan':
        $url = 'https://ropsten.etherscan.io/address/' . urlencode($entry->{$column['name']});
        break;
    case 'etherscan':
        $url = 'https://etherscan.io/address/' . urlencode($entry->{$column['name']});
        break;
    default:
        //
        break;
}


?>
<td>
    @if ($url)
        <a href="{{ $url  }}" rel="nofollow noopener" target="_blank">
            @endif
            {{ str_limit(strip_tags($entry->{$column['name']}), 16, "...") }}
            @if ($url)
        </a>
    @endif
</td>