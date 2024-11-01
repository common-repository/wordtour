<div class="wt_pagination ui-helper-clearfix">
	<div class="wt-float-right">
		<div class="ui-helper-clearfix">
			{for i 1 $pages}
				<div class="wt-float-left wt-margin-all ui-corner-all ui-state-default wt-padding-all"><a href="{$url}{$i}" {if $i==$page}class="selected"{/if}>{$i}</a></div> 
			{/for}
		</div>
	</div>
</div>
