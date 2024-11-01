<div id="wordtour-content">
	{if $navigation!=''}
		{$navigation}
	{/if}
	
	<div class="wt-list wt-events">
	{loop $tpl.group}
		{if $name!=''}
		<h2>{$name}</h2>
		{/if}
		{if !$subgroup} 
			No events have been scheduled at this time
		{/if}
		{loop $subgroup}
			{if $name!=''}
			<div class="ui-widget-header ui-corner-tl ui-corner-tr wt-padding-all">
				{$name}
			</div>
			{/if}
			<table cellspacing='0'>
				<thead>
				<tr>
					<th>Date</th>
					<th>Venue</th>
					<th></th>
				</tr>
				</thead>
				{if !$data} 
				<tr>
					<td colspan="5">No Events Listed</td>
				</tr>
				{else}
				<!--  {capture "index"}{counter start=1 skip=1}{/capture}-->
				{loop $data}
				<!--  	{capture "index"}{counter}{/capture} -->
				<!--  {if $.capture.index%2 == 1}ui-state-default{/if}-->	
				<tr class="wt-no-border">
					<td>
						<a class='date' href='{$url}'>{$date}</a>
						<br/>{if $artists!=''}<small>Opening Act: {$artists}</small>{/if}
					</td>
					<td>
						<div class='venue'><a href="{$venue.url}">{$venue.name}</a></div>
						<small>
							{if $venue.country_code == "US" && $venue.state_code!=''}
								{$venue.state},
							{elseif $city!=''}
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
				<!--  {if $.capture.index%2 == 1}ui-state-default{/if}--> 
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
				{/if}
			</table>
		{/loop}
	{/loop}
	</div>
</div>
