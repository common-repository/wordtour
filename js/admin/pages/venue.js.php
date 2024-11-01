<script>
jQuery(function($) {
	$(".wordtour-column").sortable({
		connectWith: '.wordtour-column',
		handle     : '.wordtour-panel-hndl-header',
		receive     : function(){},
		stop : function(e,ui){
			setPanelsOrder("venue");
			ui.item.formpanel('option', 'dragstop').call(ui.item);
		}	
	});

	$("#wordtour-panel-order").formpanel({page:"venue"});
	$("#wordtour-panel-more").formpanel({page:"venue"});
	$("#wordtour-panel-map").formpanel({
		page:"venue",
		dragstop : function(){
			$(this).find(".wordtour-panel-content").googlemap("resize");		
		},
		buttons: [
			{
				title : "Refresh Map",
				text  : false,
				icon  : "ui-icon-refresh",
				click : function(){
					$("#wordtour-panel-map").block();
					var data = {action:"map",
							  venue_address : $("#venue_address").val(),
							  venue_city:$("#venue_city").val(),
							  venue_state:$("#venue_state").val(),
							  venue_country:$("#venue_country").val()}
					$.ajax({
						url: $CONSTANT["PLUGIN_AJAX"],
						data : data,
						type : "post",
						dataType : "json",
						success : function(r){
							try{
								if(r){
									$("#wordtour-panel-map .wordtour-panel-content").googlemap("value",r.address,r.country);	
								}
							} catch(e){

							}
						},
						complete: function(){
							$("#wordtour-panel-map").unblock();
						}
					})	
				}
			}
  		]
	}).bind("formpanelexpand",function(){
		$(this).find(".wordtour-panel-content").googlemap("resize");
	});
	
	$("#wordtour-panel-info").formpanel({page:"venue"});
	$("#wordtour-panel-category").formpanel({page:"venue"});
	$("#wordtour-panel-video").formpanel({
		page:"venue",
		buttons: [
			{
				title : "Search and Add Videos",
				text  : false,
				icon  : "ui-icon-search"
			}
  		]
	});
	
	$("#wordtour-panel-gallery").formpanel({
		page:"venue",
		buttons: [
			{
				title : "Add New Gallery",
				text  : false,
				icon  : "ui-icon-plus"
			}
  		]
	});


	$("#show_all_countries").click(function(){
		openAllDataDialog("show-all-countries-dialog","#venue_country",$CONSTANT.DIALOG_ALL_COUNTRIES,"All Countries");
		return false;
	});

	$("#show_all_states").click(function(){
		openAllDataDialog("show-all-states-dialog","#venue_state",$CONSTANT.DIALOG_ALL_STATES,"All States");
		return false;
	});
	
	
	function loadVenue(id){
		var f = $(this); 
		var id = id || "";
		f.wordtourform("ajax",{action:"get_venue",venue_id:id},function(r){
			f.wordtourform("set",r);
		});
	}
	
	$("#venue-form").wordtourform({
		init     : function(){
			loadVenue.call(this,$(this).find("[name=venue_id]").val());
		},
		complete : function(e,response) {
			$("#wordtour-button-save").toolbarbutton("enable");
			if($(this).find("[name=venue_id]").val()!="") {
				$("#wordtour-button-undo-default").toolbarbutton("enable");
				$("#wordtour-button-default").toolbarbutton("enable");
			}	
		},
		beforeSend: function(){
			$("#wordtour-button-save").toolbarbutton("disable");
			$("#wordtour-button-default").toolbarbutton("disable");
			$("#wordtour-button-undo-default").toolbarbutton("disable");
		},
		items: [
			{		
				type    : "googlemap",
				selector: "#wordtour-panel-map .wordtour-panel-content",
				jPath   : "venue_address,venue_city,venue_state_code,venue_country_code",
				options : {
					
				}
			},
			{
				type    : "gallerymanager",
				name    : "venue_gallery",
				selector: "#wordtour-panel-gallery .wordtour-panel-content",
				jPath   : "venue_gallery",
				options : {
					dialogHandler: "#wordtour-panel-gallery button:first"
				}
			},
			{
				type    : "categorymanager",
				name    : "venue_category",
				selector: "#wordtour-panel-category .wordtour-panel-content",
				jPath   : "venue_category"
			},
			{
				type      : "videomanager",
				name      : "venue_videos",
				selector  : "#wordtour-panel-video .wordtour-panel-content",
				jPath     : "venue_videos", 
				options   : {
					dialogHandler: "#wordtour-panel-video button:first"
				}
			},
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-save",
				options   : {
					cls : "save",
					innerText1: "Updated <span id='venue_publish_date'></span>",
					disabled : true,
					click    : function(e){
						var form = $("#venue-form");
						var isNew = form.find("[name=venue_id]").val() == "";
						var action =  isNew ? "insert_venue" : "update_venue";
						var data = $.extend({action:action},form.wordtourform("serialize"));						
						form.wordtourform("ajax",data,function(r){
							var that = this,msg="";
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
														"Eventbrite venue updated succefully <i>["+r.eventbrite.process.message+"]</i>",
													"</div>",
												"</div>"].join("");
											
									}
								} catch(e) {}
								if(!isNew) {
									form.wordtourform("set",r.result);
									this.alert("show","success","Venue updated Successfully"+addMsg);
								} else {
									loadVenue.call(form);
									this.alert("show","success","Venue \""+r.result.venue_name+"\" added Successfully, <a href='"+$CONSTANT["PAGE_VENUE"]+"&action=edit&venue_id="+r.result.venue_id+"'>Click here to Edit</a>"+addMsg);
								}
							} else {
								
							}
						});	
					}
				}
			},
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-default",
				jPath     : "is_default",
				options   : {
					cls : "default",
					disabled : true,
					click    : function(e){
						var form = $("#venue-form");
						var id = form.find("[name=venue_id]").val();
						var data = {action:"default_venue",venue_id:id};						
						form.wordtourform("ajax",data,function(r){
							if(r.type == "success") {
								if(r.result.is_default == 1) {
									$("#wordtour-button-default").hide();
									$("#wordtour-button-undo-default").show();
								} else {
									$("#wordtour-button-default").show();
									$("#wordtour-button-undo-default").hide();
								}
								 
							}
						});
					}
				},
				renderer : function(v){
					if(v == 1) {
						$("#wordtour-button-default").hide();
					} else {
						$("#wordtour-button-default").show();
					}
				}
			},
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-undo-default",
				jPath     : "is_default",
				options   : {
					cls : "undo-default",
					innerText1: "Venue set as default",
					showInnerText : true,
					disabled : true,
					click    : function(e){
						var form = $("#venue-form");
						var id = form.find("[name=venue_id]").val();
						var data = {action:"remove_default_venue",venue_id:id};						
						form.wordtourform("ajax",data,function(r){
							if(r.type == "success") {
								if(r.result.is_default == 1) {
									$("#wordtour-button-default").hide();
									$("#wordtour-button-undo-default").show();
								} else {
									$("#wordtour-button-default").show();
									$("#wordtour-button-undo-default").hide();
								}
							}
						});	
					}
				},
				renderer : function(v){
					if(v == 1) {
						$("#wordtour-button-undo-default").show();
					} else {
						$("#wordtour-button-undo-default").hide();
					}
				}
			},                 	
			{ 
				type      : "readonlytext",
				selector  : "#venue_publish_date",
				jPath     : "venue_publish_date",
				renderer  : function(v){
					$("#wordtour-button-save .update").css({visibility:(v!="") ? "visible" : "hidden"});
					return v;
				}
			},
			{ 
				name      : "venue_gallery_status",
				selector  : "#venue_gallery_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "venue_flickr_status",
				type      : "button",
				selector  : "#venue_flickr_status",
				inputType :"checkbox"
			},
			{ 
				name      : "venue_video_status",
				selector  : "#venue_video_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "venue_post_status",
				selector  : "#venue_post_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "venue_tour_status",
				selector  : "#venue_tour_status",
				type      : "button",
				inputType :"checkbox"
			},
			{
				selector : "#wordtour-poster-panel .wordtour-panel-content",
				name     : "venue_thumbnail_id",
				jPath    : "venue_thumbnail_id,venue_thumbnail",
				type     : "postermanager",
				options  : {
					mediaUrl: $CONSTANT["MEDIA_LIBRARY"]
				}
			},
			{ 
				type     : "autocomplete",
				name     : "venue_name",
				options  : {
					source: function(request, response) {
						if($CONSTANT["LASTFM_API_KEY"]!="") {
							var term =  request.term;
							$.ajax({
								url: "http://ws.audioscrobbler.com/2.0/",
								dataType: "jsonp",
								data: {
									format : "json",
									api_key: $CONSTANT["LASTFM_API_KEY"],
									limit  : 10,
									method :"venue.search",
									venue  : term
								},
								success: function(data) {
									var venues = data.results.venuematches.venue ;
									if(venues) {
										if(!$.isArray(venues)) venues = [venues];
										response($.map(data.results.venuematches.venue, function(venue) {
											var r = {
												name    : venue.name,
												phone   : venue.phonenumber,
												url     : venue.website,
												country : venue.location.country,
												city    : venue.location.city,
												address  : venue.location.street
											}
										
											var location = [];
											if(r.address!="") location.push(r.address);
											if(r.city!="") location.push(r.city);
											if(r.country!="") location.push(r.country);
											
											return {
												label: r.name+"<div style='font-size:90%;color:#AAAAAA'>"+location.join(", ")+"</div>",
												value: r.name,
												term : r
											}
										}))
									} else {
										response([
								          {
											label: "No Results",
											value: term,
											term : term
								          }
										]);
									}
								}
							});
						};
					},
					select: function(event, ui) {
						var term = ui.item.term;
						if(term) {
							$("#venue_url").val(term.url);
							$("#venue_phone").val(term.phone);
							$("#venue_address").val(term.address);
							$("#venue_city").val(term.city);
							$("#venue_state").val("");
							$("#venue_country").val(term.country);
						}
					},
					minLength: 1
				}
			},
			{ 
				type     : "autocomplete",
				name     : "venue_country",
				selector : "#venue_country",
				options  : {
					source   : $CONSTANT.AUTOCOMPLETE_COUNTRY,
					minLength: 1
				}
			},
			{type: "inputtext",name: "venue_city",selector: "#venue_city"},
			
			{ 
				type     : "autocomplete",
				name     : "venue_state",
				selector : "#venue_state",
				options  : {
					source   : $CONSTANT.AUTOCOMPLETE_STATE,
					minLength: 1
				}
			},
			{ 
				type      : "component",
				selector  : "#venue_permalink",
				jPath     : "permalink",
				options   : {
					setValue  : function(v){
						var target = $("#venue_permalink");
						if(v!="") {
							this.find("a").attr("href",v).text(v);
							target.show();
						} else{
							target.hide();
						}
					}
				}
			},
			{type: "inputtext",name: "venue_address"},
			{type: "inputtext",name: "_nonce",selector:"[name=_venue_nonce]"},
			{type: "inputtext",name: "venue_id"},
			{type: "inputtext",name: "venue_order"},
			{type: "inputtext",name: "venue_info"},
			{type: "inputtext",name: "venue_phone"},
			{type: "inputtext",name: "venue_url"},
			{type: "inputtext",name: "venue_zip"}
		]
	});
});
</script>

