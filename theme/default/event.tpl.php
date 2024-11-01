<!--
	Values:
	-----------------------------------------
		@artist � Artist name
		@artist_poster � Poster URL
		@address � Venue address
		@admission � Ticket price
		@artist_url � Artist page URL
		@attending_markup � RSVP widget markup
		@city � City name
		@comments_markup � Comments widget markup
		@country_code � Country short-name
		@country � Venue country
		@zip � Venue zip code
		@comments � Total comments for event
		@directions � Venue directions URL
		@date � Start date as defined in WordPress admin. This parameter supports values of tomorrow, yesterday, today. In case the original date format is required, please use the date_raw variable.
		@date_raw � Date as mysql format; for modifying the format to specific requirements access the template files, click here for example
		@end_date � End date as defined in WordPress admin. This parameter supports values of tomorrow, yesterday, today. In case the original date format is required, please use the end_date_raw variable.
		@end_date_raw � End date as mysql format; for modifying the format to specific requirements access the template files, click here for example.
		@end_time � End time as defined in WordPress admin.
		@flickr_markup � Flickr widget markup.
		@google_map � Google Map venue address string.
		@gallery_markup � Gallery widget markup.
		@opening � Opening act.
		@onsale � On sale mysql format.
		@poster � Poster path (in WordPress thumbnail size 150X150).
		@poster_id
		@poster_orig � Array of original sized image uploaded to WordPress. The array contains three key values: URL, width and height. For Example:
		@phone � Ticket booking phone number.
		@rsvp � Total users attending the event
		@show_comments � Displays comments widget; return values of 0 or 1.
		@show_rsvp � Displays RSVP widget; return values of 0 or 1.
		@show_gallery � Displays gallery widget; return values of 0 or 1.
		@show_flickr � Displays Flickr widget; return values of 0 or 1.
		@state_code � State short-name.
		@state � Venue state.
		@status � Status of event: cancelled, onsale, active, soldout.
		@description � Additional information for event.
		@short_description
		@title � Event title.
		@time � Start time.
		@tickets � Buy tickets URL.
		@tour � Tour name.
		@tour_url � Tour page URL.
		@url � Event URL.
		@venue � Venue name.
		@venue_url � Venue website URL.
		@venue_phone � Venue phone number.
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




