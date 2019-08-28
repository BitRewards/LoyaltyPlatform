<?php
if (!isset($level)) {
  $level = 'h1';
}

?>

<tr>
  <td class="w600" width="600">
    <table class="w600" border="0" cellpadding="0" cellspacing="0" width="600">
      <tbody>
        <tr class="large_only"><td class="w600" height="20" width="600"></td></tr>
        <tr class="mobile_only"><td class="w600" height="15" width="600"></td></tr>
        <tr>
          <td class="600" width="600">
            <div align="left">
              <{{ $level }} class="page-title">{!! $heading !!}</{{ $level }}>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </td>
</tr>
<tr class="large_only"><td class="w600" height="20" width="600"></td></tr>
<tr class="mobile_only"><td class="w600" height="15" width="600"></td></tr>
