<script>
jQuery(function($) {
	$("#wordtour-button-add").toolbarbutton({
		cls: "add",
		click: function(){
			window.location = $CONSTANT["PAGE_NEW_ALBUM"];
		}
	});

	$("#wordtour-button-delete").toolbarbutton({
		cls: "trash",
		innerText1: "<span class='count'></span> Albums Selected",
		disabled: true,
		click   : function() {
			$("#albums-list").wordtourlist("deleteAll",{
				action  : "delete_all_albums",
				_nonce  : "<?php echo wp_create_nonce(WT_Album::NONCE_DELETE);?>"
			},"Albums");
		}
	});

	$("#albums-list").wordtourlist({
		
	})
	.bind("wordtourlistquickedit", function(e,target) {
		openEditAlbumDialog(getDataFromStr($(target).attr("class")),function(){
			var form = $(this).find("form");
			var data = $.extend({action:"quickupdate_album"},form.wordtourform("serialize"));
			var dialog = this;						
			form.wordtourform("ajax",data,function(r){
				$(e.target).wordtourlist("replaceRowHtml",$(target).parents("tr:first"),r.html);
				$(dialog).dialog('close');
			});
		});		
		
	});
});
</script>

