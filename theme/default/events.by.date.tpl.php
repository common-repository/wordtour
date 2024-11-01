<div id="wordtour-content">
	<div class="wt-list wt-events-by-date">
	<table cellspacing='0'>
	{loop $tpl.group}
		<tr>
			<td colspan="4"><h2>{date_format $name "%A, %B %d, %Y"}</h2></td>
		</tr>
		
		{capture "index"}{counter start=1 skip=1}{/capture}
		{loop $data}
		{capture "index"}{counter}{/capture}	
		<tr class="row-{$.capture.index%2}">
			<td>
				{if $artist_poster != ''}
				<div class='poster'>
					{if $artist_poster!=''}
						<img src='{$artist_poster}'></img>
					{else}
						
					{/if}
				</div>
				{/if}
			</td>
			<td><a class='artist' href='{$url}'>{$artist}</a></td>
			<td>
				<div class='venue'>{$venue}</div>
				<span class='venue_info'>
					{if $country_code == "US" && $state_code!=''}
						{$state},
					{elseif $city!=''}
						{$city},
					{/if}
					{$country}
				</span>
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
		<tr class="row-{$.capture.index%2}">
			<td class='activity' colspan='4'>
				<div>
					<a href='{$url}'>{$comments} Comments</a> - 
					<a href='{$url}'>{$rsvp} Attendees</a> -
					<a rel="#wt-venue-overlay" href='#'>Venue Information</a>
					<!-- DO NOT REMOVE - START-->
					{include(file='venue.overlay.tpl.php' venue=$venue country_code=$country_code google_map=$google_map directions=$directions address=$address city=$city state=$state country=$country zip=$zip venue_phone=$venue_phone venue_website=$venue_website)}
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
