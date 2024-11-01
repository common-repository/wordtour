<div class="wt-panel wt-panel-rsvp" id="rsvp-block">	
	<h2>{$total} Attending 
		{if $is_login} 
			-   {if $attending}
				You Are Attending (<a class="not-attending" href="#">Not Attending?</a>)
				{else}
				<a class="attending" href="#">Attending This Event?</a>
				{/if}
		{/if}
	</h2>
	<div class="content">
		<div class="ui-helper-clearfix rsvp-wrap">
			{loop $users}
			<div class="rsvp-user">{$nickname}</div>
			{/loop}
		</div> 
	</div>	
</div>