<div class="wt-rsvp-widget wt-margin-bottom">
	<div class="ui-widget-header ui-corner-all wt-padding-all wt-margin-bottom">	
		{$total} Attending 
		{if $is_login} 
			-   {if $attending}
				You Are Attending (<a class="not-attending" href="#">Not Attending?</a>)
				{else}
				<a class="attending" href="#">Attending This Event?</a>
				{/if}
		{/if}
	</div>
	<div class="ui-helper-clearfix">
		{loop $users}
		<div class="wt-float-left wt-padding-all ui-state-default ui-corner-all">{$nickname}</div>
		{/loop}
	</div> 
		
</div>