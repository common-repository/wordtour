<!--
	Values:
	-----------------------------------------
		@thumbnails - (Array)
			@thumbnail - (Object) Thumbnail Details ((150X150)
				@id - Image ID
				@url
				@width
				@height
				@title
				@content
				@excerpt
			@large - (Object) Large Image Details (Original Size)
				@id - Image ID
				@url
				@width
				@height
				@title
				@content
				@excerpt
		@totla - Total images in gallery
	Filter - Use in Theme Folder - function.php: 
	--------------------------------------------
		@add_filter("gallery_template_params","function name");
 --> 

<div class="wt-gallery-widget wt-margin-bottom">
	<div class="ui-widget-header ui-corner-all wt-padding-all wt-margin-bottom">	
		{$total} Photos
	</div>
	{if $total>0}
	<div class="ui-widget-content ui-corner-all wt-padding-all">
		<div class="ui-helper-clearfix thumbnail-wrap" id="thumbnails">   
	   		{loop $thumbnails}
	   		<div class="wt-float-left wt-margin-all thumbnail-item ui-corner-all">
	   			<a href="{$large.url}" title="">
                	<img title="{$thumbnail.title}" src="{$thumbnail.url}" width="48" height="48"/>
                	<!-- available value for usage: title,contnet, -->
            	</a>	
			</div>
			{/loop}
		</div>
	</div>
	{/if}
</div>





