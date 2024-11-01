
<script>
jQuery(function($){
	
	$("#wordtour-button-add").toolbarbutton({
		cls: "add",
		click: function(){
			window.location = $CONSTANT["PAGE_NEW_TOUR"];
		}
	});

	$("#wordtour-button-delete").toolbarbutton({
		cls: "trash",
		innerText1: "<span class='count'></span> Tour Selected",
		disabled: true,
		click   : function() {
			$("#tours-list").wordtourlist("deleteAll",{
				action  : "delete_all_tour",
				_nonce  : "<?php echo wp_create_nonce(WT_Tour::NONCE_DELETE);?>"
			},"Tour");
		}
	});
	
	$("#tours-list").wordtourlist({
		setdefault   :$.noop,
		removedefault:$.noop
	})
	.bind("wordtourlistsetdefault", function(e,target) {
		var currentDef = $(this).find("tr.tr-default");
		$(this).wordtourlist("updateRow",null,target,function(r){
			currentDef.removeClass("tr-default");
		});		
	})
	.bind("wordtourlistremovedefault", function(e,target) {
		var currentDef = $(this).find("tr.tr-default");
		$(this).wordtourlist("updateRow",null,target,function(r){
			currentDef.removeClass("tr-default");
		});		
	})
	.bind("wordtourlistquickedit", function(e,target) {
		openEditTourDialog(getDataFromStr($(target).attr("class")),function(){
			var form = $(this).find("form");
			var data = $.extend({action:"quickupdate_tour"},form.wordtourform("serialize"));
			var dialog = this;						
			form.wordtourform("ajax",data,function(r){
				$(e.target).wordtourlist("replaceRowHtml",$(target).parents("tr:first"),r.html)
				$(dialog).dialog('close');
			});
		});		
	});

});
</script>