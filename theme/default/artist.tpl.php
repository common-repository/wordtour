<!--
	Values:
	-----------------------------------------
		@id - Artist ID  
		@bio Ð Full Artist bio.
		@short_bio Ð  Summary Artist bio.
		@flickr_markup Ð Flickr widget markup.
		@gallery_markup Ð Gallery widget markup.
		@genre Ð Musical genre of artist.
		@label Ð Label name of artist.
		@name Ð Artist name
		@poster Ð Thumbnail poster of artist as HTML tag.
		@poster_url Ð Artist poster thumbnail URL.
		@poster_id
		@tour_markup Ð Artist upcoming events.
		@total Ð Total events of artist.
		@url - artist page URL.
		@news_markup
		@gallery_markup
		@flickr_markup
		@video_markup
		@website
		@email
		@flickr - Flicker URL
		@youtube - YouTube URL
		@vimeo - Vimeo URL
		@facebook - Facebook URL
		@twitter = Twitter URL
		@lastfm - Last.FM URL
		@myspace - MySpace URL
	Filter - Use in Theme Folder - function.php: 
	--------------------------------------------
		@add_filter("artist_single_template","function name");
		@add_filter("artist_events_shortcode_params","function name") - Change artist configuration, by shortcode params;
 --> 

<div class="wt-panel wt-panel-artist">
	<h1>{$name}</h1>
	
	<div class="links ui-helper-clearfix">
		{if $facebook!=""}
			<a href="{$facebook}"><div class="facebook"></div></a>
		{/if}
		{if $twitter!=""}
			<a href="{$twitter}"><div class="twitter"></div></a>
		{/if}
		{if $myspace!=""}
			<a href="{$myspace}"><div class="myspace"></div></a>
		{/if}
		{if $flickr!=""}
			<a href="{$flickr}"><div class="flickr"></div></a>
		{/if}
		{if $youtube!=""}
			<a href="{$youtube}"><div class="youtube"></div></a>
		{/if}
		{if $vimeo!=""}
			<a href="{$vimeo}"><div class="vimeo"></div></a>
		{/if}
		{if $lastfm!=""}
			<a href="{$lastfm}"><div class="lastfm"></div></a>
		{/if}
	</div>
		
	<p style="margin-bottom:10px;">
		<div>
		<img src='{$poster_url}' width="100" height="100" align="right"></img>
		{$bio}
		</div>
	</p>
	
	{$news_markup}
	{$gallery_markup}
	{$flickr_markup}
	{$video_markup}
	<div class="wt-panel wt-panel-tour">
		<h2>Upcoming Shows</h2>
		{$tour_markup}
	</div>
</div>

	
