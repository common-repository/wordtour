<div class="wt-comments-widget wt-margin-bottom">
	<div class="ui-widget-header ui-corner-all wt-padding-all wt-margin-bottom">	
		<span class='total-comments'>{$total}</span> Comments
	</div>
	<div>
		<ol id="the-comment-list" class="commentlist">
			{include('comment.tpl.php')}
		</ol>
	</div>
</div>

<div class="wt-comments-widget wt-margin-bottom" id="respond">
	<div class="ui-widget-header ui-corner-all wt-padding-all wt-margin-bottom">	
		Leave a Reply
	</div>
	<div>
		{if !$is_login && !$allow_comment}
			You must be <a href="<?php echo wp_login_url();?>">logged in</a> to post a comment.
		{else}
			<form id="comment-form">
				<input type="hidden"  name="comment_event_id" value="{$event_id}">
				<input type="hidden" name="_nonce" value="{$nonce}"></input>
				{if $is_login}
				<p class="logged-in-as">
					Logged in as <a href="<?php echo get_bloginfo("url")."/wp-admin/profile.php";?>">{$nickname}</a>. 
					<a title="Log out of this account" href="<?php echo wp_logout_url();?>">Log out >></a>
				</p>
				{/if}
				
				{if $is_login || $allow_comment}
				<p class="comment-form-comment">
					{if !$is_login}
					<p class="comment-form-author">
						<label for="author">Name</label> <span class="required">*</span>
						<input type="text" aria-required="true" size="30" value="" name="comment_author" id="author"></p>
					</p>
					<p class="comment-form-email">
						<label for="email">Email</label> <span class="required">*</span>
						<input type="text" name="comment_author_email" value="" id="email"></input>
					</p>
					{/if}
					<p class="comment-form-comment">
						<label for="comment">Comment</label> <span class="required">*</span>
						<textarea tabindex="4" rows="10" id="comment" name="comment_content"></textarea>
					</p>
					<p>
					{$captcha}
					</p>
				</p>
				{/if}
				<input type="button" value="Submit Comment" id="submit-comment">
			</form>
		{/if}
	</div>
</div>