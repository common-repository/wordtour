<script>
jQuery(function($) {

	$("#wordtour-button-add").toolbarbutton({
		cls: "add",
		click: function(e){
			openInsertGalleryDialog({},function(r){
				$("#gallery_list").wordtourlist("addRowHtml",r.html);
			});	
		}
	});
	
	$("#wordtour-button-delete").toolbarbutton({
		cls: "trash",
		innerText1: "<span class='count'></span> Gallery Selected",
		disabled: true,
		click   : function() {
			$("#gallery_list").wordtourlist("deleteAll",{
				action  : "delete_all_galleries",
				_nonce  : "<?php echo wp_create_nonce(WT_Gallery::NONCE_DELETE);?>"
			},"Galleries");
		}
	});
	
	$("#gallery_list").wordtourlist({})
	.bind("wordtourlistedit", function(e,target) {
		openEditGalleryDialog(getDataFromStr($(target).attr("class")),function(){
			var form = $(this).find("form");
			var data = $.extend({action:"update_gallery"},form.wordtourform("serialize"));
			var dialog = this;						
			form.wordtourform("ajax",data,function(r){
				$(e.target).wordtourlist("replaceRowHtml",$(target).parents("tr:first"),r.html)
				$(dialog).dialog('close');
			});
		});		
	});
});
</script>
