<div class="wt-videos-widget wt-margin-bottom">
	<div class="ui-widget-header ui-corner-all wt-padding-all wt-margin-bottom">	
		{$total} Videos
	</div>
	{if $total>0}	
	<div class="ui-widget-content ui-corner-all wt-padding-all">
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





