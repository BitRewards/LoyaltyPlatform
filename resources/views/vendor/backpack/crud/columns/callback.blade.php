{{-- custom return value via attribute --}}
<td>
	<?php
	    echo call_user_func($column['callback'], $entry);
    ?>
</td>