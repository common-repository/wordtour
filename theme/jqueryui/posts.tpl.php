<div class="wt-news-widget wt-margin-bottom">
	<div class="ui-widget-header ui-corner-all wt-padding-all wt-margin-bottom">	
		News
	</div>
	<div class="ui-widget-content wt-padding-all ui-corner-all">
		<ul>
			{loop $posts}
			<li>
				<h2 class="entry-title"><a href="$guid}">{$post_title}</a></h2>
				<small class="entry-meta">{date_format $post_date "%A, %B %d, %Y"}</small>
				<div class="entry-content">{$post_content}</div>
			</li>
			{else}
			<li>No News</li>
			{/loop}
		</ul>
	</div>	
</div>
