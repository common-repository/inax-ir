<!--begin inax-ir plugin-->
<div style="clear:both;">
    {if isset($head_error) }<div class="alert alert-danger">{$head_error}</div>{/if}
    {if isset($test_mode) && $test_mode }<div class="alert alert-danger">حالت Test Mode در خرید از آینکس روشن است.</div>{/if}
    {if isset($gateway_test_mode) && $gateway_test_mode }<div class="alert alert-danger">{$gateway_test_mode}</div>{/if}