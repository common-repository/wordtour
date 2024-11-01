<!--
	Values:
	-----------------------------------------
		@id - Artist ID  
		@bio � Full Artist bio.
		@short_bio �  Summary Artist bio.
		@flickr_markup � Flickr widget markup.
		@gallery_markup � Gallery widget markup.
		@genre � Musical genre of artist.
		@label � Label name of artist.
		@name � Artist name
		@poster � Thumbnail poster array
		     @url - Poster URL
		     @imgTag - HTML IMG Tag
		     @id - Thumbnail ID 
		     @width 
		     @height 
		@tour_markup � Artist upcoming events.
		@total � Total events of artist.
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

	
