<div class="wt-panel wt-panel-flickr">	
	<h2>{$total} Flickr Photos - <a href="http://www.flickr.com/photos/tags/{$machinetag}/interesting/" target="_blank">See more on Flickr</a></h2>
	<div class="content">
		{if $total>0}
		<div class="ui-helper-clearfix thumbnail-wrap">   
	   		{loop $photo}
	   		<div class="thumbnail-item">
	   			<a href="{$href}" title="{$title}" target="_blank">
                	<img src="{$thumb}"/>
            	</a>	
			</div>
			{/loop}
		</div>
		{/if}
		<div class="infobox">
		Tag your photos on Flickr by tagging them on <a href="http://www.flickr.com">flickr.com</a> with the following tag name <span class='machinetag'>{$machinetag}</span>
		</div>
	</div>
</div>





