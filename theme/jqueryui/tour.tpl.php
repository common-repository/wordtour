<!--
	Values:
	-----------------------------------------
		@description Ð Description of tour.
		@short_description Ð Short description of tour.
		@name Ð Tour name.
		@id Ð Tour ID.
		@total Ð Total events of tour.
		@url Ð Tour page URL.
		@poster Ð Thumbnail poster array
		     @url - Poster URL
		     @imgTag - HTML IMG Tag
		     @id - Thumbnail ID 
		     @width 
		     @height 
		@new_markup Ð Latest posts widget markup.
		@tour_markup Ð Tour widget markup.
		@videos_markup Ð YouTube videos widget markup.
		@news_markup
		@gallery_markup
		@flickr_markup
		@video_markup
	Filter - Use in Theme Folder - function.php: 
	--------------------------------------------
		@add_filter("tour_single_template","function name") - Manipule current content;
		@add_filter("tour_events_shortcode_params","function name") - Change tour configuration, by shortcode params;
		
 --> 
 <div class="wt-tour">
	<h1 class="wt-page-title entry-title ui-helper-clearfix">{$name}</h1>
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
			Shows
		</div>
		{$tour_markup}
	</div>
</div>
