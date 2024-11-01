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

<div class="wt-similiar-albums-widget wt-margin-bottom">
	<div class="ui-widget-header ui-corner-all wt-padding-all wt-margin-bottom">	
		{$total} Similar Albums
	</div>
	{if $total>0}
	<div class="ui-widget-content ui-corner-all wt-padding-all">
		<div class="ui-helper-clearfix" id="thumbnails">   
	   		{loop $albums}
	   		<div class="wt-float-left wt-margin-all wt-align-center ui-corner-all">
	   			<a href="{$url}" title="{$title}">
                	<img src="{$poster_url}" style="width:70px;height:70px;" border="0"></img><br/>
                	<small>{$artist} - {$title}</small>
            	</a>	
			</div>
			{/loop}
		</div>
	</div>
	{/if}
</div>





