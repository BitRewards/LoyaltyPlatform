<script>
@if ($isEmailOrPhoneRequired)
    window.opener.loyalty.openEmailOrPhoneRequest();
@else
    window.opener.location.href = window.opener.URLS.INDEX;
@endif

window.close();
</script>