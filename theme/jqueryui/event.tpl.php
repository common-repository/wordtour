<!--
	Values:
	-----------------------------------------
		@artist Ð Array of artist data - refer to artist parameters
		@admission Ð Ticket price
		@attending_markup Ð RSVP widget markup
		@comments_markup Ð Comments widget markup
		@comments Ð Total comments for event
		@date Ð Start date as defined in WordPress admin. This parameter supports values of tomorrow, yesterday, today. In case the original date format is required, please use the date_raw variable.
		@date_raw Ð Date as mysql format; for modifying the format to specific requirements access the template files, click here for example
		@end_date Ð End date as defined in WordPress admin. This parameter supports values of tomorrow, yesterday, today. In case the original date format is required, please use the end_date_raw variable.
		@end_date_raw Ð End date as mysql format; for modifying the format to specific requirements access the template files, click here for example.
		@end_time Ð End time as defined in WordPress admin.
		@flickr_markup Ð Flickr widget markup.
		@gallery_markup Ð Gallery widget markup.
		@artists Ð List of additional artists, seperated by comma.
		@artists_array Ð Array of additional artists. The array contains three key values: url,id,name
		@onsale Ð On sale mysql format.
		@poster Ð Thumbnail poster array
		     @url - Poster URL
		     @imgTag - HTML IMG Tag
		     @id - Thumbnail ID 
		     @width 
		     @height 
		@phone Ð Ticket booking phone number.
		@rsvp Ð Total users attending the event
		@show_comments Ð Displays comments widget; return values of 0 or 1.
		@show_rsvp Ð Displays RSVP widget; return values of 0 or 1.
		@show_gallery Ð Displays gallery widget; return values of 0 or 1.
		@show_flickr Ð Displays Flickr widget; return values of 0 or 1.
		@status Ð Status of event: cancelled, onsale, active, soldout.
		@description Ð Additional information for event.
		@short_description
		@title Ð Event title.
		@time Ð Start time.
		@tickets Ð Buy tickets URL.
		@tour Ð Array of tour data - refer to tour parameters
		@url Ð Event URL.
		@venue Ð Array of venue data - refer to venue parameters.
		@news_markup
		@video_markup
		@phone
		@comments - Total comments
	Filter - Use in Theme Folder - function.php: 
	--------------------------------------------
		@add_filter("event_single_template","function name");
 --> 
  
<div class="wt-event">

	<h1 class="wt-page-title entry-title ui-helper-clearfix">
		<div class='wt-float-left'>{$title}</div>
		<!-- AddThis Button BEGIN -->
		<div class="wt-float-right wt-margin-left addthis_toolbox addthis_default_style">
			<a class="addthis_counter addthis_pill_style"></a>
		</div>
		<!-- AddThis Button END -->
	</h1>
	
	<div class="entry-meta ui-helper-clearfix">
		<div class='wt-float-left wt-margin-right'><a href='#comments'><span id="wt-total-comments">{$comments}</span> Comments</a> -</div>  
		<div class='wt-float-left wt-margin-right'><a href='#rsvp'><span id="wt-total-rsvp">{$rsvp}</span> Attendees</a></div>
		{if ($status==active || $status==onsale) && $tickets!='' }
		<div class='wt-float-left wt-margin-right'>- <a href='{$tickets}' class='status buy'>Buy Tickets</a></div>
		{else}
			{if $status!=active}
		<div class='wt-float-left wt-margin-right'>- <span class='status {$status}'>{upper($status)}</span></div>
			{/if}	
		{/if}
		</span>
	</div>

	<div class="ui-helper-clearfix wt-margin-bottom">
		{if $poster!=''}
		<div class="wt-float-right">{if $poster.url!=""}{$poster.imgTag}{/if}</div>
		{/if}
		<div class="wt-float-left wt-description wt-margin-top">
			<table>
				{if $artists!=''}
				<tr>
					<th>Opening Act:</th>
					<td>
					{loop $artists_array}
						<a href="{$url}">{$name}</a><br/>
					{/loop}
					</td>
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
						{if $venue.url!=''}
							<a href="{$venue.url}" target="_blank">{$venue.name}</a>
						{else}
							{$venue.name}
						{/if}
					</td>
				</tr>
				{if $venue.address!=''}
				<tr>
					<th>Address</th>
					<td>{$venue.address}</td>
				</tr>
				{/if}
				{if $venue.city!=''}
				<tr>
					<th>City:</th>
					<td>{$venue.city}</td>
				</tr>
				{/if}
				{if $venue.state!=''}
				<tr>
					<th>State:</th>
					<td>{$venue.state}</td>
				</tr>
				{/if}
				{if $venue.country!=''}
				<tr>
					<th>Country:</th>
					<td>{$venue.country}</td>
				</tr>
				{/if}
				{if $venue.zip!=''}
				<tr>
					<th>Zip:</th>
					<td>{$venue.zip}</td>
				</tr>
				{/if}
				{if $venue.phone!=''}
				<tr>
					<th>Phone:</th>
					<td>{$venue.phone}</td>
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
	
	<div class="ui-widget wt-margin-bottom">
		<div class="ui-widget-header ui-corner-all wt-padding-all wt-margin-bottom">	
			Map - <a href="http://maps.google.com?q={$venue.google_map}" target="_blank">Get Directions</a>
		</div>
		<div class="ui-widget-content ui-corner-all wt-padding-all">
			<input type="hidden" id="google-map-address" value="{$venue.google_map}"></input>
			<input type="hidden" id="google-map-country" value="{$venue.country_code}"></input>
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




