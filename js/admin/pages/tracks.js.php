<script>
jQuery(function($) {
	$("#wordtour-button-add").toolbarbutton({
		cls: "add",
		click: function(){
			window.location = $CONSTANT["PAGE_NEW_TRACK"];
		}
	});

	$("#wordtour-button-delete").toolbarbutton({
		cls: "trash",
		innerText1: "<span class='count'></span> Tracks Selected",
		disabled: true,
		click   : function() {
			$("#tracks-list").wordtourlist("deleteAll",{
				action  : "delete_all_tracks",
				_nonce  : "<?php echo wp_create_nonce(WT_Track::NONCE_DELETE);?>"
			},"Tracks");
		}
	});

	$("#tracks-list").wordtourlist({
		
	})
	.bind("wordtourlistquickedit", function(e,target) {
		openEditTrackDialog(getDataFromStr($(target).attr("class")),function(){
			var form = $(this).find("form");
			var data = $.extend({action:"quickupdate_track"},form.wordtourform("serialize"));
			var dialog = this;						
			form.wordtourform("ajax",data,function(r){
				$(e.target).wordtourlist("replaceRowHtml",$(target).parents("tr:first"),r.html);
				$(dialog).dialog('close');
			});
		});		
	});
});
</script>

