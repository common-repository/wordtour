<script>

jQuery(function($){
	$("#wordtour-button-add").toolbarbutton({
		cls: "add",
		click: function(){
			window.location = $CONSTANT["PAGE_NEW_EVENT"];
		}
	});

	$("#wordtour-button-delete").toolbarbutton({
		cls: "trash",
		innerText1: "<span class='count'></span> Events Selected",
		disabled: true,
		click   : function() {
			$("#events-list").wordtourlist("deleteAll",{
				action  : "delete_all_events",
				_nonce  : "<?php echo wp_create_nonce(WT_Event::NONCE_DELETE);?>"
			},"Events");
		}
	});
	
	$("#events-list").wordtourlist({
		unpublish:$.noop,
		publish  :$.noop,
		facebook :$.noop,
		twitter  :$.noop
	})
	.bind( "wordtourlistunpublish", function(e,target) {
		var action = $(".subsubsub .all .current").length>0 ? "updateRow" : "removeRow"; 
		$(this).wordtourlist(action,null,target,function(){
			var unp = $(".subsubsub .unpublished .countNum"),countUnp = unp.html();
			var pub = $(".subsubsub .published .countNum"),countPub = pub.html();
			unp.html(parseInt(countUnp)+1);
			pub.html(parseInt(countPub)-1);
		});	
	})
	.bind( "wordtourlistpublish", function(e,target) {
		var action = $(".subsubsub .all .current").length>0 ? "updateRow" : "removeRow"; 
		$(this).wordtourlist(action,null,target,function(){
			var unp = $(".subsubsub .unpublished .countNum"),countUnp = unp.html();
			var pub = $(".subsubsub .published .countNum"),countPub = pub.html();
			unp.html(parseInt(countUnp)-1);
			pub.html(parseInt(countPub)+1);	
		});	
	})
	.bind("wordtourlistquickedit", function(e,target) {
		openEditEventDialog(getDataFromStr($(target).attr("class")),function(){
			var form = $(this).find("form");
			var data = $.extend({action:"quickupdate_event"},form.wordtourform("serialize"));
			var dialog = this;						
			form.wordtourform("ajax",data,function(r){
				$(e.target).wordtourlist("replaceRowHtml",$(target).parents("tr:first"),r.html)
				$(dialog).dialog('close');
				try {
					if(r.eventbrite.error) {
						var msg = "<b>Event saved succefully</b>, however there was an error updating Eventbrite event information <i>["+r.eventbrite.error.error_type+":"+r.eventbrite.error.error_message+"]";
						if($("#dialog-eventbrite-error").length>0) {
							$("#dialog-eventbrite-error").html(msg);
						} else {
							$("body").append(["<div id='dialog-eventbrite-error' title='Eventbrite Alert'>",
												"<p class='ui-helper-clearfix'>",
													"<span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 50px 0;'></span>",
													msg,
												"</p>",
											"</div>"].join(""));
						}

						$("#dialog-eventbrite-error").dialog({
							modal: true,
							width: 350,
							buttons: {
								Ok: function() {
									$( this ).dialog( "close" );
								}
							}
						});				
					}
				} catch(e){};
			});
		});		
	});

	
});
</script>