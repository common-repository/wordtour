<div id="wordtour-content">
	<div class="wt-list wt-events-by-date">
	<table cellspacing='0'>
	{loop $tpl.group}
		<tr>
			<td colspan="4" class="ui-widget-header wt-padding-all wt-no-border">
					{date_format $name "%A, %B %d, %Y"}	
			</td>
		</tr>
		{loop $data}
		<tr>
			<td width="5%">
				{if $artist.poster.url != ''}
				<div class='thumbnail-item'>
					{$artist.poster.imgTag}
				</div>
				{/if}
			</td>
			<td ><a href='{$url}'>{$artist.name}</a><br/></td>
			<td>
				
				<div><a href='{$venue_url}'>{$venue.name}</a></div>
				<small>
					{if $venue.country_code == "US" && $venue.state_code!=''}
						{$venue.state},
					{elseif $venue.city!=''}
						{$venue.city},
					{/if}
					{$venue.country}
				</small>
				
			</td>
			<td>
				{if ($status==active || $status==onsale) && $tickets!='' }
					<a href='{$tickets}' class='status buy'>TICKETS</a>
					{if $onsale!=''}<div class="sale"><div class="sale-head">On Sale: <span class="sale-date">{date_format $onsale "%a, %B %d, %Y"}</span></div>{/if}
				{else}
					{if $status!=active}
						<div class='status {$status}'>{upper($status)}</div>
					{/if}	
				{/if}
			</td>
		</tr>
		<tr class="wt-no-border">
			<td class='wt-no-border' colspan='4'>
				<div>
					<small><a href='{$url}'>{$comments} Comments</a> -</small> 
					<small><a href='{$url}'>{$rsvp} Attendees</a> - </small>
					<small><a rel="#wt-venue-overlay" href='#'>Directions</a></small>
					<!-- DO NOT REMOVE - START-->
					{include(file='venue.overlay.tpl.php' venue=$venue.name country_code=$venue.country_code google_map=$venue.google_map directions=$venue.directions address=$venue.address city=$venue.city state=$venue.state country=$venue.country zip=$venue.zip venue_phone=$venue.phone venue_website=$venue.website)}
					<!-- DO NOT REMOVE - END-->
				</div>
			</td>
		</tr>
		{/loop}
		
	{/loop}
	{if !$tpl.group} 
		No events have been scheduled at this time
	{/if}
	</table>
	</div>
</div>
