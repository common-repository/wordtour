<script>
jQuery(function($) {
	$(".wordtour-column,").sortable({
		connectWith: '.wordtour-column',
		handle     : '.wordtour-panel-hndl-header',
		stop     : function(){
			setPanelsOrder("artist");
		}	
	});
	
	$("#wordtour-panel-order").formpanel({page:"artist"});
	$("#wordtour-panel-info").formpanel({page:"artist"});
	$("#wordtour-panel-genre").formpanel({page:"artist"});
	$("#wordtour-panel-social").formpanel({page:"artist"});
	$("#wordtour-panel-category").formpanel({page:"artist"});
	$("#wordtour-panel-bio").formpanel({
		page:"artist",
		buttons: [
			{
				title : "Search Bio",
				text  : true,
				icon  : "ui-icon-search",
				click : function(){
					if($CONSTANT["LASTFM_API_KEY"]) {
						var form = $("form"),button=this;
						var artistName = form.find("[name=artist_name]").val();
						var origLabel = $(this).button("option","label");
						var origIcon = $(this).button("option","icons").primary;
						if(artistName !="") {
							$(this)
							.button("option","icons",{primary:'ui-icon-refresh'})
							.button("option","label","Searching...");
							
							$.getJSON("http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist="+artistName+"&format=json&api_key="+$CONSTANT["LASTFM_API_KEY"]+"&callback=?",
								function(data){
									if(data && form.length > 0) {
										try {
											var bio = data.artist.bio.content;
											if(bio == "") bio = "Can't find artist, please check again";
											form.find("[name=artist_bio]").val(bio);
											
										} catch(e) {
											return "";
										}
									}
									$(button)
									.button("option","icons",{primary:origIcon})
									.button("option","label",origLabel);
						    	}
							);
						}
					} else {
						openAlertDialog("Last.FM Key is missing","In order to search for bio, you need to setup your Last.FM key. For more information go settings page	");
					};
				}
			}
  		]
	});
	$("#wordtour-panel-video").formpanel({
		page:"artist",
		buttons: [
			{
				title : "Search and Add Videos",
				text  : false,
				icon  : "ui-icon-search"
			}
  		]
	});
	
	$("#wordtour-panel-gallery").formpanel({
		page:"artist",
		buttons: [
			{
				title : "Add New Gallery",
				text  : false,
				icon  : "ui-icon-plus"
			}
  		]
	});
	
	function loadArtist(id){
		var f = $(this); 
		var id = id || "";
		f.wordtourform("ajax",{action:"get_artist",artist_id:id},function(r){
			f.wordtourform("set",r);
		});
	}
	
	$("#artist-form").wordtourform({
		init     : function(){
			loadArtist.call(this,$(this).find("[name=artist_id]").val());
		},
		complete : function(e,response) {
			$("#wordtour-button-save").toolbarbutton("enable");
			if($(this).find("[name=artist_id]").val()!="") {
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
				type    : "genremanager",
				name    : "artist_genre",
				selector: "#wordtour-panel-genre .wordtour-panel-content"
			},		
			{
				type    : "gallerymanager",
				name    : "artist_gallery",
				selector: "#wordtour-panel-gallery .wordtour-panel-content",
				jPath   : "artist_gallery",
				options : {
					dialogHandler: "#wordtour-panel-gallery button:first"
				}
			},
			{
				type    : "categorymanager",
				name    : "artist_category",
				selector: "#wordtour-panel-category .wordtour-panel-content",
				jPath   : "artist_category"
			},
			{
				type      : "videomanager",
				name      : "artist_videos",
				selector  : "#wordtour-panel-video .wordtour-panel-content",
				jPath     : "artist_videos", 
				options   : {
					dialogHandler: "#wordtour-panel-video button:first"
				}
			},
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-save",
				options   : {
					cls : "save",
					disabled : true,
					click    : function(e){
						var form = $("#artist-form");
						var isNew = form.find("[name=artist_id]").val() == "";
						var action =  isNew ? "insert_artist" : "update_artist";
						var data = $.extend({action:action},form.wordtourform("serialize"));						
						form.wordtourform("ajax",data,function(r){
							if(r.type=="success") {
								if(!isNew) {
									form.wordtourform("set",r.result);
									this.alert("show","success","Artist updated Successfully");
								} else {
									loadArtist.call(form);
									this.alert("show","success","Artist \""+r.result.artist_name+"\" added Successfully, <a href='"+$CONSTANT.ADMIN_URL+"page=wt_artists&action=edit&artist_id="+r.result.artist_id+"'>Click here to Edit</a>");
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
						var form = $("#artist-form");
						var id = form.find("[name=artist_id]").val();
						var data = {action:"default_artist",artist_id:id};						
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
					disabled : true,
					click    : function(e){
						var form = $("#artist-form");
						var id = form.find("[name=artist_id]").val();
						var data = {action:"remove_default_artist",artist_id:id};						
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
				selector  : "#artist_publish_date",
				jPath     : "artist_publish_date",
				renderer  : function(v){
					$("#wordtour-button-save .update").css({visibility:(v!="") ? "visible" : "hidden"});
					return v;
				}
			},
			{ 
				name      : "artist_gallery_status",
				selector  : "#artist_gallery_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "artist_flickr_status",
				type      : "button",
				selector  : "#artist_flickr_status",
				inputType :"checkbox"
			},
			{ 
				name      : "artist_video_status",
				selector  : "#artist_video_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "artist_post_status",
				selector  : "#artist_post_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "artist_tour_status",
				selector  : "#artist_tour_status",
				type      : "button",
				inputType :"checkbox"
			},
			{
				selector : "#wordtour-poster-panel .wordtour-panel-content",
				name     : "artist_thumbnail_id",
				jPath    : "artist_thumbnail_id,artist_thumbnail",
				type     : "postermanager",
				options  : {
					mediaUrl: $CONSTANT["MEDIA_LIBRARY"]
				}
			},
			{ 
				type     : "autocomplete",
				name     : "artist_name",
				options  : {
					source: function(request,response) {
						if($CONSTANT["LASTFM_API_KEY"]!="") {
							var term =  request.term;
							$.ajax({
								url: "http://ws.audioscrobbler.com/2.0/",
								dataType: "jsonp",
								data: {
									format : "json",
									api_key: $CONSTANT["LASTFM_API_KEY"],
									limit  : 10,
									method :"artist.search",
									artist  : term
								},
								success: function(data) {
									var artists = data.results.artistmatches.artist;
									if(artists) {
										if(!$.isArray(artists)) artists = [artists];
										response($.map(artists, function(artist) {
											var r = {
												name    : artist.name,
												img     : artist.image[0]["#text"]
											};
											
											return {
												label: "<span><img src='"+r.img+"' width='20' height='20'/> "+r.name+"<span>",
												value: r.name,
												term : r.name
											}
										}));
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
						}
					},
					minLength: 1
				}
			},
			{ 
				type      : "component",
				selector  : "#artist_permalink",
				jPath     : "permalink",
				options   : {
					setValue  : function(v){
						var target = $("#artist_permalink");
						if(v!="") {
							this.find("a").attr("href",v).text(v);
							target.show();
						} else{
							target.hide();
						}
					}
				}
			},
			{type: "inputtext",name: "artist_order"},
			{type: "inputtext",name: "artist_bio"},
			{type: "inputtext",name: "artist_email"},
			{type: "inputtext",name: "artist_website_url"},
			{type: "inputtext",name: "artist_record_company"},
			{type: "inputtext",name: "_nonce",selector:"[name=_artist_nonce]"},
			{type: "inputtext",name: "artist_id"},
			{type: "inputtext",name: "artist_flickr"},
			{type: "inputtext",name: "artist_youtube"},
			{type: "inputtext",name: "artist_facebook"},
			{type: "inputtext",name: "artist_twitter"},
			{type: "inputtext",name: "artist_myspace"},
			{type: "inputtext",name: "artist_lastfm"},
			{type: "inputtext",name: "artist_vimeo"},
			{type: "inputtext",name: "artist_bandcamp"},
			{type: "inputtext",name: "artist_tumblr"},
			{type: "inputtext",name: "artist_reverbnation"}	

		]
	});
});
</script>