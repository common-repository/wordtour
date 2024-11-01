<!--
	Values:
	-----------------------------------------
		@tracks - (Array)
			@id - TrackID  
			@about � Full Track Info.
			@short_about �  Summary track info.
			@genre � Comma seperated musical genre of track.
			@genre_array � Array of track genre.
			@label � Label name of track.
			@lyrics � Track lyrics.
			@author � Track lyrics author.
			@title � Track name
			@poster
			@credits
			@release - Track release date
			@release_raw - Track release date
			@playcount � Track playcount.
		@total - Total tracks
	Filter - Use in Theme Folder - function.php: 
	--------------------------------------------
		@add_filter("tracks_template_params","function name");
 --> 

<div class="wt-gallery-widget wt-margin-bottom">
	<div class="ui-widget-header ui-corner-all wt-padding-all wt-margin-bottom">	
		{$total} Tracks
	</div>
	{if $total>0}
	<div class="ui-corner-all wt-padding-all">
		<div class="ui-helper-clearfix thumbnail-wrap" id="thumbnails">   
	   		{loop $tracks}
	   		<div class="ui-helper-clearfix wt-margin-bottom ui-corner-all ui-state-default wt-padding-all">
	   			<div class="wt-float-left">{$title}</div>
	   			{if $lyrics!=''}
	   				<div class="wt-float-right">
	   					<a href="#" rel="#wt-lyrics-overlay">Lyrics</a>
	   					<textarea style='display:none;'>{$title}</textarea><textarea style='display:none;'>{$lyrics}</textarea>
	   				</div>
	   			{/if}
			</div>
			{/loop}
		</div>
	</div>
	{/if}
</div>





