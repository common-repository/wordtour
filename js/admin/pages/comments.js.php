<script>
jQuery(function($){
	$("#wordtour-button-delete").toolbarbutton({
		cls: "trash",
		innerText1: "<span class='count'></span> Comments Selected",
		disabled: true,
		click   : function() {
			$("#the-comment-list").wordtourlist("deleteAll",{
				action  : "delete_all_comments",
				_nonce  : "<?php echo wp_create_nonce(WT_Comment::NONCE_DELETE);?>"
			},"Comments");
		}
	});

	
	$("#the-comment-list").wordtourlist({
		approve   :$.noop,
		unapprove:$.noop
	})
	.bind("wordtourlistapprove", function(e,target) {
		$(this).wordtourlist("updateRow",null,target);		
	})
	.bind("wordtourlistunapprove", function(e,target) {
		$(this).wordtourlist("updateRow",null,target);		
	})
	.bind("wordtourlistquickedit", function(e,target) {
		openEditCommentDialog(getDataFromStr($(target).attr("class")),function(){
			var form = $(this).find("form");
			var data = $.extend({action:"update_comment"},form.wordtourform("serialize"));
			var dialog = this;						
			form.wordtourform("ajax",data,function(r){
				$(e.target).wordtourlist("replaceRowHtml",$(target).parents("tr:first"),r.html)
				$(dialog).dialog('close');
			});
		});		
	});
});

</script>

