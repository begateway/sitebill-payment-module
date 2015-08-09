{if isset($payment_error) }
<div class="error">{$payment_error}</div>
{else}
{$payment_text}
<br>
{$payment_description}
<form id="payment" name="payment" method="post" action="{$payment_params.url}" enctype="utf-8">
<input type="hidden" name="token" value="{$payment_params.token}" />
<input type="submit" value="{$payment_button}">
</form>
{/if}
