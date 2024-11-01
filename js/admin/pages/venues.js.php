<script>
jQuery(function($) {
	$("#wordtour-button-add").toolbarbutton({
		cls: "add",
		click: function(){
			window.location = $CONSTANT["PAGE_NEW_VENUE"];
		}
	});

	$("#wordtour-button-delete").toolbarbutton({
		cls: "trash",
		innerText1: "<span class='count'></span> Venues Selected",
		disabled: true,
		click   : function() {
			$("#venues-list").wordtourlist("deleteAll",{
				action  : "delete_all_venues",
				_nonce  : "<?php echo wp_create_nonce(WT_Venue::NONCE_DELETE);?>"
			},"Venues");
		}
	});
	
	$("#venues-list").wordtourlist({
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
		openEditVenueDialog(getDataFromStr($(target).attr("class")),function(){
			var form = $(this).find("form");
			var data = $.extend({action:"quickupdate_venue"},form.wordtourform("serialize"));
			var dialog = this;						
			form.wordtourform("ajax",data,function(r){
				$(e.target).wordtourlist("replaceRowHtml",$(target).parents("tr:first"),r.html);
				$(dialog).dialog('close');
				try {
					if(r.eventbrite.error) {
						var msg = "<b>Venue saved succefully</b>, however there was an error updating Eventbrite venue information <i>["+r.eventbrite.error.error_type+":"+r.eventbrite.error.error_message+"]";
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
