<script>
Lib.Facebook.load($CONSTANT.FACEBOOK_API_KEY);
Lib.Twitter.load($CONSTANT.TWITTER_API_KEY);

jQuery(function($) {

	$(".wordtour-column,").sortable({
		connectWith: '.wordtour-column',
		handle     : '.wordtour-panel-hndl-header',
		stop     : function(){
			setPanelsOrder();	
		}	
	});

	$("#wordtour-panel-title").formpanel({page:"event"});
	$("#wordtour-panel-social").formpanel({page:"event"});
	$("#wordtour-panel-notes").formpanel({page:"event"});
	$("#wordtour-panel-genre").formpanel({page:"event"});
	$("#wordtour-panel-category").formpanel({page:"event"});
	$("#wordtour-panel-tickets").formpanel({page:"event"});
	$("#wordtour-panel-comments").formpanel({page:"event"});
	$("#wordtour-panel-status2").formpanel({page:"event"});
		
	$("#wordtour-panel-moreartists").formpanel({
		page   : "event",
		buttons: [
			{
				title : "Add",
				text  : true,
				icon  : "ui-icon-plus"
			}
  	]});
  	
	$("#wordtour-panel-video").formpanel({
		page   : "event",
		buttons: [
			{
				title : "Search and Add Videos",
				text  : false,
				icon  : "ui-icon-search"
			}
  		]
	});
	
	$("#wordtour-panel-gallery").formpanel({
		page   : "event",
		buttons: [
			{
				title : "Add New Gallery",
				text  : false,
				icon  : "ui-icon-plus"
			}
  		]
	});
	$("#wordtour-panel-rsvp").formpanel();
	// Show All Dialog
	$("#show_all_venues").click(function(){
		openAllDataDialog("show-all-venues-dialog","#event_venue_name",$CONSTANT.DIALOG_ALL_VENUES,"All Venues");
		return false;
	});
	$("#show_all_artists").click(function(){
		openAllDataDialog("show-all-artists-dialog","#event_artist_name",$CONSTANT.DIALOG_ALL_ARTISTS,"All Artists");
		return false;
	});
	$("#show_all_tour").click(function(){
		openAllDataDialog("show-all-tour-dialog","#event_tour_name",$CONSTANT.DIALOG_ALL_TOUR,"All Tour");
		return false;
	});
	$("#show_all_event_type").click(function(){
		openAllDataDialog("show_all_event_type-dialog","#event_type",$CONSTANT.DIALOG_ALL_TYPE,"All Type");
		return false;
	});

	// Insert
	$("#add-artist").click(function(){
		openInsertArtistDialog({},function(r){
			$("#event_artist_name").val(r.result.artist_name).focus();
		});
		return false;
	});

	$("#add-venue").click(function(){
		openInsertVenueDialog({},function(r){
			$("#event_venue_name").val(r.result.venue_name).focus();
		});
		return false;
	});

	$("#add-tour").click(function(){
		openInsertTourDialog({},function(r){
			$("#event_tour_name").val(r.result.tour_name).focus();
		});
		return false;
	});
	
	
	<?php
		$galleries_array = array(); 
		foreach(wt_galleries() as $gallery) {
			$go = new WT_Gallery();
			$go->db_out($gallery);
			array_push($galleries_array,array("id"=>$gallery["gallery_id"],"name"=>$gallery["gallery_name"]));
		}
	?>

	function loadEvent(id){
		var id = id || "";
		$(this).wordtourform("ajax",{action:"get_event",event_id:id},function(r){
			if($.isArray(r)) {
				openAlertDialog("Error Loading Event","Event doesn't exist in the system",function(){
					window.location = $CONSTANT.PAGE_EVENTS;
					$("#wordtour-button-save").toolbarbutton("disable");
					$("#wordtour-button-trash").toolbarbutton("disable");
					$("#wordtour-button-publish").toolbarbutton("disable");
					$("#wordtour-button-twitter").toolbarbutton("disable");
					$("#wordtour-button-facebook").toolbarbutton("disable");
					$("#wordtour-button-eventbrite").toolbarbutton("disable");
					$("#wordtour-button-post").toolbarbutton("disable");
				});
			} else {
				this.element.wordtourform("set",r);
			}
			return;
		});
	}

	
	
	$("#load-comments").click(function(e){
		var link = $(e.target); 
		var eventId = $("#event-form [name=event_id]").val();
		if(eventId!="" && eventId!==undefined) {
			$.post($CONSTANT.PLUGIN_AJAX,{action: "get_event_comments",event_id:eventId},function(r){
				$("#wordtour-panel-comments .wordtour-panel-content").html(r);
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
			})
		}
		return false;
	});

	$("#load-rsvp").click(function(e){
		var link = $(e.target); 
		var eventId = $("#event-form [name=event_id]").val();
		if(eventId!="" && eventId!==undefined) {
			$.post($CONSTANT.PLUGIN_AJAX,{action: "get_event_rsvp",event_id:eventId},function(r){
				if($.isArray(r)) {
					if(r.length>0) {
						$("#wordtour-panel-rsvp .wordtour-panel-content").rsvpmanager().rsvpmanager("value",r);
					} else {
						$("#wordtour-panel-rsvp .wordtour-panel-content").html("No RSVP Listed");
					}
				}
				
			},"json")
		}
		return false;
	});

	
	$("#event-form").wordtourform({
		init     : function(){
			loadEvent.call(this,$(this).find("[name=event_id]").val());
		},
		// events
		success: function(that,r){
			var response = r.type  ? r.result : r;
			if(response.event_published == "1"){
				$("#wordtour-button-trash").show();
				$("#wordtour-button-publish").hide();
			} else {
				$("#wordtour-button-trash").hide();
				$("#wordtour-button-publish").show();
			}
		},
		complete : function(e,response) {
			$("#wordtour-button-save").toolbarbutton("enable");
			var eventId = $(this).find("[name=event_id]").val(); 
			if(eventId!="") {
				$("#wordtour-button-post").toolbarbutton("enable");
				$("#wordtour-button-trash").toolbarbutton("enable");
				$("#wordtour-button-publish").toolbarbutton("enable");
			}	
			if($CONSTANT["FACEBOOK_API_KEY"]=="" || !$CONSTANT["FACEBOOK_API_KEY"]) {	
				$("#wordtour-button-facebook").toolbarbutton("disable").find(".noregister").show().end().find(".update").hide();					
			} else {
				$("#wordtour-button-facebook").toolbarbutton(eventId=="" ? "disable": "enable").find(".noregister").hide().end();
			}
			
			if(!$CONSTANT.EVENTBRITE_ENABLED) {	
				$("#wordtour-button-eventbrite").toolbarbutton("disable").find(".noregister").show().end().find(".update").hide();					
			} else {
				$("#wordtour-button-eventbrite").toolbarbutton(eventId=="" ? "disable": "enable").find(".noregister").hide().end();
			}
			
			if($CONSTANT["TWITTER_API_KEY"]=="" || !$CONSTANT["TWITTER_API_KEY"]) {
				$("#wordtour-button-twitter").toolbarbutton("disable").find(".noregister").show().end().find(".update").hide();					
			} else {
				$("#wordtour-button-twitter").toolbarbutton(eventId=="" ? "disable": "enable").find(".noregister").hide().end();
			}			
		},
		beforeSend: function(){
			$("#wordtour-button-save").toolbarbutton("disable");
			$("#wordtour-button-trash").toolbarbutton("disable");
			$("#wordtour-button-publish").toolbarbutton("disable");
			$("#wordtour-button-twitter").toolbarbutton("disable");
			$("#wordtour-button-facebook").toolbarbutton("disable");
			$("#wordtour-button-eventbrite").toolbarbutton("disable");
			$("#wordtour-button-post").toolbarbutton("disable");
		},
		items: [	
			{
				type    : "genremanager",
				name    : "event_genre",
				selector: "#wordtour-panel-genre .wordtour-panel-content"
			},		
			{
				type    : "artistsmanager",
				name    : "event_more_artists",
				selector: "#wordtour-panel-moreartists .wordtour-panel-content .more-artists-wrap",
				options : {
					addArtistSelector: "#wordtour-panel-moreartists button:first",
				}
			},	
			{
				type    : "gallerymanager",
				name    : "event_gallery",
				selector: "#wordtour-panel-gallery .wordtour-panel-content",
				jPath   : "event_gallery",
				options : {
					dialogHandler: "#wordtour-panel-gallery button:first",
					items : <?php echo json_encode($galleries_array); ?>
				}
			},
			{
				type    : "categorymanager",
				name    : "event_category",
				selector: "#wordtour-panel-category .wordtour-panel-content",
				jPath   : "event_category"
			},
	        {
				type      : "videomanager",
				name      : "event_videos",
				selector  : "#wordtour-panel-video .wordtour-panel-content",
				jPath     : "event_videos", 
				options   : {
	        		dialogHandler: "#wordtour-panel-video button:first"
	        	}
	        },
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-save",
				options   : {
					cls : "save",
					innerText1: "Updated <span id='event_publish_date'></span>",
					disabled : true,
					click    : function(e){
						var form = $("#event-form");
						var isNew = form.find("[name=event_id]").val() == "";
						var action =  isNew ? "insert_event" : "update_event";						
						var data = $.extend({action:action},form.wordtourform("serialize"));						
						form.wordtourform("ajax",data,function(r){
							if(r.type=="success") {
								var addMsg = "";
								try {
									if(r.eventbrite.error) {
										addMsg +=["<div class='ui-helper-clearfix' style='margin-top:5px;'>",
														"<div class='ui-icon ui-icon-alert' style='float:left;'></div>",
														"<div style='float:left;font-size:11px;'>",
															"There was an error updating eventbrite venue information <i>["+r.eventbrite.error.error_type+":"+r.eventbrite.error.error_message+"]",
														"</div>",
													"</div>"].join("");
									}

									if(r.eventbrite.process.status=="OK") {
										addMsg +=["<div class='ui-helper-clearfix' style='margin-top:5px;'>",
													"<div class='ui-icon ui-icon-check' style='float:left;'></div>",
													"<div style='float:left;font-size:11px;'>",
														"Eventbrite event updated succefully <i>["+r.eventbrite.process.message+"]</i>",
													"</div>",
												  "</div>",
												  "<div class='ui-helper-clearfix' style='margin-top:5px;'>",
													"<div class='ui-icon ui-icon-info' style='float:left;'></div>",
													"<div style='float:left;font-size:11px;'>",
														"Your Ticket Url has been updated, Click save to update your event",
													"</div>",
												  "</div>"].join("");
											
									}
								} catch(e) {}
								if(!isNew) {
									form.wordtourform("set",r.result);
									this.alert("show","success","Event updated Successfully"+addMsg);
								} else {
									loadEvent.call(form);
									this.alert("show","success","Event added Successfully, <a href='"+$CONSTANT.ADMIN_URL+"page=wordtour/navigation.php&action=edit&event_id="+r.result.event_id+"'>Click here to Edit</a>");
								}
							} else {
								
							}
						});
					}
				}
			},
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-eventbrite",
				options   : {
					cls : "eventbrite",
					prefix : "wt-social-buttons",
					innerTextStart : "<div class='noregister'><a href='http://www.eventbrite.com/r/wt/' target='_blank'>Register To Eventbrite</a></div>", 
					innerText1: "Updated <span id='eventbrite_status_date'></span>",
					innerText2: "<a id='eventbrite_ref_id' target='_blank' href='#'>View Event On Eventbrite</a>",
					disabled : true,
					click    : function(e){
						var eventId = $("#event-form").find("[name=event_id]").val();
						if(eventId!="") {
							openEventbriteDialog({event_id:eventId});
						}
					}
				}
			},
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-post",
				options   : {
					cls : "post",
					prefix : "wt-social-buttons", 
					innerText1: "<a id='post_ref_id' target='_blank' href='#'>Click to Edit Post</a>",
					disabled : true,
					click    : function(e){
						var eventId = $("#event-form").find("[name=event_id]").val();
						if(eventId!="") {
							openPostDialog({event_id:eventId});
						}
					}
				}
			},
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-facebook",
				options   : {
					cls    : "facebook",
					prefix : "wt-social-buttons", 
					innerTextStart : "<div class='noregister'><a href='http://developers.facebook.com/setup/' target='_blank'>Get Facebook Application Key</a></div>",
					innerText1: "Updated <span id='facebook_status_date'></span>",
					innerText2: "<a id='facebook_event_id' class='status' target='_blank' href='#'>View Event on Facebook</a></span>",
					disabled : true,
					click    : function(e){
						var eventId = $("#event-form").find("[name=event_id]").val();
						if(eventId!="" && $(e.originalTarget).attr("id")!="facebook_event_id") {
							openFacebookDialog({event_id:eventId});
						}
					}
				}
			},
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-twitter",
				options   : {
					cls : "twitter",
					prefix : "wt-social-buttons",
					innerTextStart : "<div class='noregister'><a href='http://dev.twitter.com/anywhere' target='_blank'>Get Twitter @Anywhere API Key</a></div>",
					innerText1: "Updated <span id='twitter_status_date'></span>",
					disabled : true,
					click    : function(e){
						var eventId = $("#event-form").find("[name=event_id]").val();
						if(eventId!="") {
							openTwitterDialog({event_id:eventId});
						}
					}
				}
			},
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-publish",
				options   : {
					cls : "publish",
					innerText1 : "Event is not published",
					showInnerText : true,
					disabled : true,
					click    : function(){
						var form = $("#event-form");
						var id = form.find("[name=event_id]").val();
						var data = {action:"publish_event",event_id:id,_nonce:"<?php echo wp_create_nonce(WT_Event::NONCE_PUBLISH);?>"};						
						form.wordtourform("ajax",data,function(r){});
					}	
				}
			},        
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-trash",
				options   : {
					cls : "trash",
					innerText1 : "Event is published",
					showInnerText : true,
					disabled : true,
					click    : function(){
						var form = $("#event-form");
						var id = form.find("[name=event_id]").val();
						var data = {action:"unpublish_event",event_id:id,_nonce:"<?php echo wp_create_nonce(WT_Event::NONCE_UNPUBLISH);?>"};						
						form.wordtourform("ajax",data,function(r){});
					}	
				}
			},            	
			{ 
				type      : "readonlytext",
				selector  : "#event_publish_date",
				jPath     : "event_publish_date",
				renderer  : function(v){
					$("#wordtour-button-save .update").css({visibility:(v!="") ? "visible" : "hidden"});
					return v;
						
				}
			},
			{ 
				type      : "readonlytext",
				selector  : "#facebook_status_date",
				jPath     : "facebook_status_date",
				renderer  : function(v){
					var target = $("#wordtour-button-facebook .update:first"); 
					if($CONSTANT["FACEBOOK_API_KEY"]!="" && v!="") {
						target.show();
					} else {
						target.hide();
					}
					return v;
				}
			},
			{ 
				type      : "readonlytext",
				selector  : "#eventbrite_status_date",
				jPath     : "eventbrite_status_date",
				renderer  : function(v){
					var target = $("#wordtour-button-eventbrite .update:first"); 
					if($CONSTANT["EVENTBRITE_ENABLED"]!="" && v!="") {
						target.show();
					} else {
						target.hide();
					}
					return v;
				}
			},
			{ 
				type      : "component",
				selector  : "#eventbrite_ref_id",
				jPath     : "eventbrite_event_id",
				options   : {
					setValue  : function(v){
						var target = $("#wordtour-button-eventbrite .update:last"); 
						if($CONSTANT["EVENTBRITE_ENABLED"]!="" && v!="") {
							this.attr("href",v);
							target.show();
						} else {
							target.hide();
						}
						return v; 
					}
				}
			},
			{ 
				type      : "readonlytext",
				selector  : "#twitter_status_date",
				jPath     : "twitter_status_date",
				renderer  : function(v){
					var target = $("#wordtour-button-twitter .update"); 
					if($CONSTANT["TWITTER_API_KEY"]!="" && v!="") {
						target.show();
					} else {
						target.hide();
					}					
					return v;	
				}
			},
			{ 
				type      : "component",
				selector  : "#facebook_event_id",
				jPath     : "facebook_event_id",
				options   : {
					setValue  : function(v){
						var target = $("#wordtour-button-facebook .update:last"); 
						if($CONSTANT["FACEBOOK_API_KEY"]!="" && v!="") {
							this.attr("href",v);
							target.show();
						} else {
							target.hide();
						}
						return v; 
					}
				}
			},
			{ 
				type      : "component",
				selector  : "#post_ref_id",
				jPath     : "post_ref_id",
				options   : {
					setValue  : function(v){
						var target = $("#wordtour-button-post .update");
						if(v!="") {
							this.attr("href",v);
							target.show();
						} else{
							target.hide();
						}
					}
				}
			},
			{ 
				type      : "component",
				selector  : "#event_permalink",
				jPath     : "permalink",
				options   : {
					setValue  : function(v){
						var target = $("#event_permalink");
						if(v!="") {
							this.find("a").attr("href",v).text(v);
							target.show();
						} else{
							target.hide();
						}
					}
				}
			},
			{ 
				name      : "gallery_status",
				selector  : "#gallery_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "post_status",
				selector  : "#post_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "comment_status",
				selector  : "#comment_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "rsvp_status",
				selector  : "#rsvp_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "flickr_status",
				type      : "button",
				selector  : "#flickr_status",
				inputType :"checkbox"
			},
			{ 
				name      : "video_status",
				selector  : "#video_status",
				type      : "button",
				inputType :"checkbox"
			},
			{
				selector : "#wordtour-poster-panel .wordtour-panel-content",
				name     : "event_thumbnail_id",
				jPath    : "event_thumbnail_id,event_thumbnail",
				type     : "postermanager",
				options  : {
					mediaUrl: $CONSTANT["MEDIA_LIBRARY"]
				}
			},
			//autocomplete
			{ 
				type     : "autocomplete",
				name     : "venue_name",
				selector : "#event_venue_name",
			  	required : false,
			  	options  : {
					source   : $CONSTANT.AUTOCOMPLETE+"?type=venues&maxRows=10",
					minLength: 1
			  	}
			}
			,
			{ 
				type     : "autocomplete",
				name     : "artist_name",
				selector : "#event_artist_name",
				required : false,
				options  : {
					source   : $CONSTANT.AUTOCOMPLETE+"?type=artists&maxRows=10",
					minLength: 1
				}
			},
			{
				name     : "tour_name",
				selector : "#event_tour_name",
				type     : "autocomplete",
				options  : {
					source   : $CONSTANT.AUTOCOMPLETE+"?type=tour&maxRows=10",
					minLength: 1
				}
			},
			{ 
				type     : "autocomplete",
				name     : "event_type",
				required : false,
				options  : {
					source   : $CONSTANT.AUTOCOMPLETE_EVENT_TYPE,
					minLength: 1
				}
			},
			{
				type     : "autocomplete",
				name     : "event_start_time",
				inputType: "time",
				format   : "hh:mm tt",
				options  : {
					source   : $CONSTANT["TIME"],
					minLength: 1
				}
				
			},
			{
				selector : "#event_end_time",
				name     : "event_end_time",
				type     : "autocomplete",
				inputType: "time",
				format   : "hh:mm tt",
				options  : {
					source   : $CONSTANT["TIME"],
					minLength: 1	
				}
			},
			//datepicker
			{
				type       : "datepicker",
				name       : "event_start_date",
				selector   : "#event_start_date",
				options    : {
					dateFormat : $CONSTANT.ADMIN_DATE_FORMAT == "m/d/y" ? "mm/dd/yy" : "dd/mm/yy" 
				} 
			},
			{
				type       : "datepicker",
				selector   : "#event_end_date",
				name       : "event_end_date",
				options    : {
					dateFormat : $CONSTANT.ADMIN_DATE_FORMAT == "m/d/y" ? "mm/dd/yy" : "dd/mm/yy"
				} 
			},
			{
				type       : "datepicker",
				selector   : "#event_on_sale",
				name       : "event_on_sale",
				options    : {
					dateFormat : $CONSTANT.ADMIN_DATE_FORMAT == "m/d/y" ? "mm/dd/yy" : "dd/mm/yy"
				} 
			},
			{
				type: "dropdown",
				name: "event_status",
				options : {
					change: function(){
						if($(this).val() == "onsale") {
							$("#event_on_sale_block").show();	
						} else {
							$("#event_on_sale_block").hide();
						}
					}
				}
			},
			{type: "inputtext",name: "event_title"},
			{type: "inputtext",name: "event_notes" },
			{type: "inputtext",name: "event_opening_act"},
			{type: "inputtext",name: "tkts_url"},
			{type: "inputtext",name: "tkts_phone"},
			{type: "inputtext",name: "tkts_price"},
			{type: "inputtext",name: "event_id"},
			{type: "inputtext",name: "event_meta_id"},
			{type: "inputtext",name: "_nonce",selector:"[name=_event_nonce]"}
		]
	});
});
</script>