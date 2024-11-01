<!--
	Values:
	-----------------------------------------
		@id - Album ID  
		@about Ð Full Album Info.
		@short_about Ð  Summary Album info.
		@genre Ð Comma seperated musical genre of album.
		@genre_array Ð Array of album genre.
		@label Ð Label name of album.
		@title Ð Album name
		@poster Ð Thumbnail poster array
		     @url - Poster URL
		     @imgTag - HTML IMG Tag
		     @id - Thumbnail ID 
		     @width 
		     @height 
		@total_tracks Ð Total tracks for album.
		@tracks Ð Tracks array.
		@url - album page URL.
		@credits
		@release - Album release date
		@type - Album type
		@amazon - Link to Amazon
		@amazonmp3 - Link to Amazon MP3
		@itunes - Link to iTunes
		@buylink1 - Link
		@buylink2 - Link
		@buylink3 = Link
		@paypal - HTML code for PayPal Button
	Filter - Use in Theme Folder - function.php: 
	--------------------------------------------
		@add_filter("album_single_template","function name");
		@add_filter("album_events_shortcode_params","function name") - Change artist configuration, by shortcode params;
 --> 


<div class="wt-artist">
	<h1 class="wt-page-title entry-title ui-helper-clearfix wt-margin-bottom">{$title}<br/><small>{$artist}</small></h1>
	<div class="ui-helper-clearfix wt-margin-bottom">
		{if $poster.url}
			<div class="wt-float-right"><img src='{$poster.url}' width="100" height="100" align="right"></img></div>
		{/if}
		<div class="wt-float-left wt-description wt-margin-top">
			<table>
				{if $release!=''}
				<tr>
					<th>Release Date:</th>
					<td>
						{$release}
					</td>
				</tr>
				{/if}
				{if $label!=''}
				<tr>
					<th>Label:</th>
					<td>
						{$label}
					</td>
				</tr>
				{/if}
				{if $credits!=''}
				<tr>
					<th>Credits:</th>
					<td>
						{$credits}
					</td>
				</tr>
				{/if}
				{if $genre!=''}
				<tr>
					<th>Genre:</th>
					<td>
						{$genre}
					</td>
				</tr>
				{/if}
				
				<tr>
					<th>Buy:</th>
					<td>
						{if $amazon!=""}
							<a href="{$amazon}">Amazon</a> 
						{/if}
						{if $amazonmp3!=""}
							<a href="{$amazon}">Amazon MP3</a> 
						{/if}
						{if $itunes!=""}
							<a href="{$amazon}">iTunes</a> 
						{/if}
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	{if $about!=''}
	<div class="wt-margin-bottom">
		<div class="ui-widget-header ui-corner-tl ui-corner-tr wt-padding-all">
		About {$title}
		</div>
		<div class="wt-padding-all">
			{$about}
		</div>
	</div>
	{/if}
	
	{$tracks_markup}
	{$similar_markup}
</div>

	
