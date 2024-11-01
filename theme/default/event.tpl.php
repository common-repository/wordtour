<!--
	Values:
	-----------------------------------------
		@artist Ð Artist name
		@artist_poster Ð Poster URL
		@address Ð Venue address
		@admission Ð Ticket price
		@artist_url Ð Artist page URL
		@attending_markup Ð RSVP widget markup
		@city Ð City name
		@comments_markup Ð Comments widget markup
		@country_code Ð Country short-name
		@country Ð Venue country
		@zip Ð Venue zip code
		@comments Ð Total comments for event
		@directions Ð Venue directions URL
		@date Ð Start date as defined in WordPress admin. This parameter supports values of tomorrow, yesterday, today. In case the original date format is required, please use the date_raw variable.
		@date_raw Ð Date as mysql format; for modifying the format to specific requirements access the template files, click here for example
		@end_date Ð End date as defined in WordPress admin. This parameter supports values of tomorrow, yesterday, today. In case the original date format is required, please use the end_date_raw variable.
		@end_date_raw Ð End date as mysql format; for modifying the format to specific requirements access the template files, click here for example.
		@end_time Ð End time as defined in WordPress admin.
		@flickr_markup Ð Flickr widget markup.
		@google_map Ð Google Map venue address string.
		@gallery_markup Ð Gallery widget markup.
		@opening Ð Opening act.
		@onsale Ð On sale mysql format.
		@poster Ð Poster path (in WordPress thumbnail size 150X150).
		@poster_id
		@poster_orig Ð Array of original sized image uploaded to WordPress. The array contains three key values: URL, width and height. For Example:
		@phone Ð Ticket booking phone number.
		@rsvp Ð Total users attending the event
		@show_comments Ð Displays comments widget; return values of 0 or 1.
		@show_rsvp Ð Displays RSVP widget; return values of 0 or 1.
		@show_gallery Ð Displays gallery widget; return values of 0 or 1.
		@show_flickr Ð Displays Flickr widget; return values of 0 or 1.
		@state_code Ð State short-name.
		@state Ð Venue state.
		@status Ð Status of event: cancelled, onsale, active, soldout.
		@description Ð Additional information for event.
		@short_description
		@title Ð Event title.
		@time Ð Start time.
		@tickets Ð Buy tickets URL.
		@tour Ð Tour name.
		@tour_url Ð Tour page URL.
		@url Ð Event URL.
		@venue Ð Venue name.
		@venue_url Ð Venue website URL.
		@venue_phone Ð Venue phone number.
		@news_markup
		@video_markup
		@phone
		@venue
		@venue_url
		@venue_phone
		@comments - Total comments
	Filter - Use in Theme Folder - function.php: 
	--------------------------------------------
		@add_filter("event_single_template","function name");
 --> 
 
<div class="wt-event">
	<h1>
		{$title}
	</h1>
	
	<div class="subtitle">
		<a href='#comments'><span id="wt-total-comments">{$comments}</span> Comments</a> -  
		<a href='#rsvp'><span id="wt-total-rsvp">{$rsvp}</span> Attendees</a>
		{if ($status==active || $status==onsale) && $tickets!='' }
			- <a href='{$tickets}' class='status buy'>Buy Tickets</a>
		{else}
			{if $status!=active}
			- <span class='status {$status}'>{upper($status)}</span>
			{/if}	
		{/if}
		</span>
	</div>
	
	<div class="share">
		<input type="hidden" name="title" value="{$title}"></input>
		<input type="hidden" name="url" value="{$url}"></input>
	</div>
	
	<div class="ui-helper-clearfix">
		{if $poster!=''}
		<div class="poster-wrap"><img src='{$poster}' width="100" height="100"></img></div>
		{/if}
		<div class="info-wrap">
			<table>
				{if $opening!=''}
				<tr>
					<th>Opening Act:</th>
					<td>{$opening}</td>
				</tr>
				{/if}
				<tr>
					<th>Start Time:</th>
					<td>
						{$date}{if $time!=''} at {$time}{/if}
					</td>
				</tr>
				{if $end_date != ""}
				<tr>
					<th>End Time:</th>
					<td>
						{$end_date} {if $end_time!=''} at {$end_time}{/if}	
					</td>
				</tr>	
				{/if}
				<tr>
					<th>Location:</th>
					<td>
						{if $venue_url!=''}
							<a href="{$venue_url}" target="_blank">{$venue}</a>
						{else}
							{$venue}
						{/if}
					</td>
				</tr>
				{if $address!=''}
				<tr>
					<th>Address</th>
					<td>{$address}</td>
				</tr>
				{/if}
				{if $city!=''}
				<tr>
					<th>City:</th>
					<td>{$city}</td>
				</tr>
				{/if}
				{if $state!=''}
				<tr>
					<th>State:</th>
					<td>{$state}</td>
				</tr>
				{/if}
				{if $country!=''}
				<tr>
					<th>Country:</th>
					<td>{$country}</td>
				</tr>
				{/if}
				{if $zip!=''}
				<tr>
					<th>Zip:</th>
					<td>{$zip}</td>
				</tr>
				{/if}
				{if $venue_phone!=''}
				<tr>
					<th>Phone:</th>
					<td>{$venue_phone}</td>
				</tr>
				{/if}
				{if $admission!=''}
				<tr>
					<th>Admission:</th>
					<td>{$admission}</td>
				</tr>
				{/if}
			</table>
		</div>
	</div>
	
	<div class="wt-panel wt-panel-map">	
		<h2>Map - <a href="http://maps.google.com?q={$google_map}" target="_blank">Get Directions</a></h2>
		<div class="content">
			<input type="hidden" id="google-map-address" value="{$google_map}"></input>
			<input type="hidden" id="google-map-country" value="{$country_code}"></input>
			<div id="directions-wrap" style="height:170px;"></div>
		</div>
	</div>
	
	{$news_markup}
	{$gallery_markup}
	{$flickr_markup}
	{$video_markup}
	{$attending_markup}
	{$comments_markup}
</div>




