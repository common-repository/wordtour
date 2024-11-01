<div class="wt_pagination">
{for i 1 $pages}
	<a href="{$url}{$i}" {if $i==$page}class="selected"{/if}>{$i}</a> 
{/for}
</div>
