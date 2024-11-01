<div class="wt-videos-widget wt-margin-bottom">
	{if $total>0}	
	<div>
		<div class="ui-helper-clearfix thumbnail-wrap" id="ytvideos">   
	   		{loop $videos}
	   		<div class="wt-float-left wt-margin-all thumbnail-item ui-corner-all">
	   			<a href="{$id}" title="">
                	<img src="{$thumbnail}"/>
            	</a>	
			</div>
			{/loop}
		</div>
	</div>
	{/if}
</div>





