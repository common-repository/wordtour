<!--
	Values:
	-----------------------------------------
		@description � Description of tour.
		@short_description � Short description of tour.
		@name � Tour name.
		@id � Tour ID.
		@total � Total events of tour.
		@url � Tour page URL.
		@poster � Thumbnail poster of artist as HTML tag.
		@poster_url � Artist poster thumbnail URL.
		@poster_id � Thumbnail poster of artist as HTML tag.
		@poster_markup � Latest posts widget markup.
		@tour_markup � Tour widget markup.
		@videos_markup � YouTube videos widget markup.
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
	<h1>{$name}</h1>
	<p>
		{$description}
	</p>
	{$news_markup}
	{$gallery_markup}
	{$flickr_markup}
	{$video_markup}
	<div class="wt-block wt-tour-dates">
		<h2>Shows</h2>
		{$tour_markup}
	</div>
</div>
