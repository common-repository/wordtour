<div class="wt-flickr-widget wt-margin-bottom">
	<div class="ui-widget-header ui-corner-all wt-padding-all wt-margin-bottom">	
		{$total} Flickr Photos - <a href="http://www.flickr.com/photos/tags/{$machinetag}/interesting/" target="_blank">See more on Flickr</a>
	</div>
	<div class="ui-widget-content wt-padding-all ui-corner-all">
		{if $total>0}
		<div class="ui-helper-clearfix thumbnail-wrap">   
	   		{loop $photo}
	   		<div class="wt-float-left wt-margin-all thumbnail-item ui-corner-all">
	   			<a href="{$href}" title="{$title}" target="_blank">
                	<img src="{$thumb}"/>
            	</a>	
			</div>
			{/loop}
		</div>
		{/if}
		<div class="ui-state-highlight ui-corner-all wt-margin-all wt-padding-all">
			<small>Tag your photos on Flickr by tagging them on <a href="http://www.flickr.com">flickr.com</a> with the following tag name <strong>{$machinetag}</strong></small>
		</div>
	</div>
</div>





