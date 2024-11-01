{loop $comments}
<li class="comment even thread-even depth-1 {$status}">
	<div class="comment-body">
		<div class="comment-author vcard">
			{$avatar}		
			<cite class="fn">{$author}</cite> 
		<span class="says">says:</span>
	</div>
	
	<div class="comment-meta commentmetadata">
		<small>{$date}</small>
	</div>
	
	{if $approved=="0"}
	<em>Your comment is awaiting moderation.</em>
	{/if}
	<p>
		<div style='font-weight:normal;'>{$content}</div>
	</p>
	
</li>
{/loop}