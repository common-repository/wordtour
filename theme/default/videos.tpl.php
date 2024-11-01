<div class="wt-panel wt-panel-videos">	
	<h2>{$total} Videos</h2>
	<div class="content">
		{if $total>0}
		<div class="ui-helper-clearfix thumbnail-wrap" id="ytvideos">   
		   		{loop $videos}
		   		<div class="thumbnail-item">
		   			<a href="{$id}" title="">
                		<img src="{$thumbnail}"/>
            		</a>	
				</div>
				{/loop}
				<div class="clear"></div>
		</div>
		<div class="clear"></div>
		{/if}
	</div>
</div>





