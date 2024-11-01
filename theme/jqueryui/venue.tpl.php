<!--
	Values:
	-----------------------------------------
		@description Ð Description of venue.
		@short_description Ð Short description of venue.
		@name Ð Venue name.
		@id Ð Venue ID.
		@address
		@city
		@zip
		@state
		@country
		@country_code
		@state_code
		@phone
		@website - Venue website URL
		@googlemap - Google Map address query
		@url Ð Venue page URL.
		@poster Ð Thumbnail poster array
		     @url - Poster URL
		     @imgTag - HTML IMG Tag
		     @id - Thumbnail ID 
		     @width 
		     @height 
		@poster_markup Ð Latest posts widget markup.
		@tour_markup Ð Tour widget markup.
		@videos_markup Ð YouTube videos widget markup.
		@news_markup
		@gallery_markup
		@flickr_markup
		@video_markup
	Filter - Use in Theme Folder - function.php: 
	--------------------------------------------
		@add_filter("venue_single_template","function name") - Manipule current content;
		@add_filter("venue_events_shortcode_params","function name") - Change venue configuration, by shortcode params;		
 --> 

<div class="wt-venue">
	<h1 class="wt-page-title entry-title ui-helper-clearfix">{$name}</h1>
	<div class="ui-helper-clearfix wt-margin-bottom">
		{if $poster!=''}
		<div class="wt-float-right">{if $poster.url!=""}{$poster.imgTag}{/if}</div>
		{/if}
		<div class="wt-float-left wt-description wt-margin-top">
			<table>
				{if $address!=""}
				<tr>
					<th>Address:</th>
					<td>{$address}</td>
				</tr>
				{/if}
				{if $city!=""}
				<tr>
					<th>City:</th>
					<td>{$city}</td>
				</tr>
				{/if}
				{if $state!=""}
				<tr>
					<th>State:</th>
					<td>{$state}</td>
				</tr>
				{/if}
				<tr>
					<th>Country:</th>
					<td>{$country}</td>
				</tr>
				{if $zip!=""}
				<tr>
					<th>Zip:</th>
					<td>{$zip}</td>
				</tr>
				{/if}
				{if $phone!=""}
				<tr>
					<th>Phone:</th>
					<td>{$phone}</td>
				</tr>
				{/if}
				{if $website!=""}
				<tr>
					<th>Website:</th>
					<td><a href='{$website}' target='_blank'>{$website}</a></td>
				</tr>
				{/if}
			</table>
		</div>
	</div>
	
	<p class='wt-margin-bottom'>
		{$description}
	</p>
	
	<div class="ui-widget wt-margin-bottom">
		<div class="ui-widget-header ui-corner-all wt-padding-all wt-margin-bottom">	
			Map - <a href="http://maps.google.com?q={$google_map}" target="_blank">Get Directions</a>
		</div>
		<div class="ui-widget-content ui-corner-all wt-padding-all">
			<input type="hidden" id="google-map-address" value="{$google_map}"></input>
			<input type="hidden" id="google-map-country" value="{$country_code}"></input>
			<div id="directions-wrap" style="height:170px;"></div>
		</div>
	</div>
	{$news_markup}
	{$gallery_markup}
	{$flickr_markup}
	{$video_markup}
	<div class="wt-events-widget wt-margin-bottom">
		<div class="ui-widget-header ui-corner-tl ui-corner-tr wt-padding-all">		
		Upcoming Shows
		</div>
		{$tour_markup}
	</div>
</div>
