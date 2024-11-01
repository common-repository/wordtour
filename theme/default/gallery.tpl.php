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

<div class="wt-panel wt-panel-gallery">	
	<h2>{$total} Photos</h2>
	<div class="content">
		{if $total>0}
		<div class="ui-helper-clearfix thumbnail-wrap" id="thumbnails">   
	   		{loop $thumbnails}
	   		<div class="thumbnail-item">
	   			<a href="{$large.url}" title="">
                	<img title="{$thumbnail.title}" src="{$thumbnail.url}"/>
                	<!-- available value for usage: title,contnet, -->
            	</a>	
			</div>
			{/loop}
		</div>
		{/if}
	</div>
</div>





