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
		@poster Ð Thumbnail poster array
		     @url - Poster URL
		     @imgTag - HTML IMG Tag
		     @id - Thumbnail ID 
		     @width 
		     @height 
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


<div class="wt-artist">
	<h1 class="wt-page-title entry-title ui-helper-clearfix wt-margin-bottom">{$name}</h1>
	<div class="links ui-helper-clearfix wt-margin-bottom">
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
		
	<p class="wt-margin-bottom">
		{if $poster.url!=""}{$poster.imgTag}{/if}
		{$bio}
	</p>
	
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

	
