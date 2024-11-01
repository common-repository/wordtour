<div class="wt-panel wt-panel-posts">
	<h2>News</h2>
	<ul class="content">
		{loop $posts}
		<li>
			<h4><a href="{$guid}">{$post_title}</a></h4>
			<small>{date_format $post_date "%A, %B %d, %Y"}</small>
			<div class="entry">{$post_content}</div>
		</li>
		{else}
		No News
		{/loop}
	</ul>	
</div>
