<script>
jQuery(function($) {
	$(".wordtour-column,").sortable({
		connectWith: '.wordtour-column',
		handle     : '.wordtour-panel-hndl-header',
		stop     : function(){
			setPanelsOrder("track");
		}	
	});
	
	$("#wordtour-panel-more").formpanel({page:"track"});
	$("#wordtour-panel-genre").formpanel({page: "track"});
	$("#wordtour-panel-lyrics").formpanel({
		page:"track",
		buttons: [{
				title : "Search Lyrics",
				text  : true,
				icon  : "ui-icon-search",
				click : function(){}
		}]
	});


	$("#show_all_artists").click(function(){
		openAllDataDialog("show-all-artists-dialog","#track_artist_name",$CONSTANT.DIALOG_ALL_ARTISTS,"All Artists");
		return false;
	});

	$("#add-artist").click(function(){
		openInsertArtistDialog({},function(r){
			$("#track_artist_name").val(r.result.artist_name).focus();
		});
		return false;
	});
		
	function loadTrack(id){
		var f = $(this); 
		var id = id || "";
		f.wordtourform("ajax",{action:"get_track",track_id:id},function(r){
			f.wordtourform("set",r);
		});
	}
	
	$("#track-form").wordtourform({
		init     : function(){
			loadTrack.call(this,$(this).find("[name=track_id]").val());
		},
		complete : function(e,response) {
			$("#wordtour-button-save").toolbarbutton("enable");	
		},
		beforeSend: function(){
			$("#wordtour-button-save").toolbarbutton("disable");
		},
		items: [
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-save",
				options   : {
					cls : "save",
					innerText1: "Updated <span id='track_publish_date'></span>",
					disabled : true,
					click    : function(e){
						var form = $("#track-form");
						var isNew = form.find("[name=track_id]").val() == "";
						var action =  isNew ? "insert_track" : "update_track";
						var data = $.extend({action:action},form.wordtourform("serialize"));						
						form.wordtourform("ajax",data,function(r){
							if(r.type=="success") {
								if(!isNew) {
									form.wordtourform("set",r.result);
									this.alert("show","success","Track updated Successfully");
								} else {
									loadTrack.call(form);
									this.alert("show","success","Track \""+r.result.track_title+"\" added Successfully, <a href='"+$CONSTANT.ADMIN_URL+"page=wt_tracks&action=edit&track_id="+r.result.track_id+"'>Click here to Edit</a>");
								}
							} else {
								
							}
						});	
					}
				}
			},
			{ 
				type      : "readonlytext",
				selector  : "#track_publish_date",
				jPath     : "track_publish_date",
				renderer  : function(v){
					$("#wordtour-button-save .update").css({visibility:(v!="") ? "visible" : "hidden"});
					return v;
				}
			},
			{
				selector : "#wordtour-poster-panel .wordtour-panel-content",
				name     : "track_thumbnail_id",
				jPath    : "track_thumbnail_id,track_thumbnail",
				type     : "postermanager",
				options  : {
					mediaUrl: $CONSTANT["MEDIA_LIBRARY"]
				}
			},
			{
				type    : "genremanager",
				name    : "track_genre",
				selector: "#wordtour-panel-genre .wordtour-panel-content"
			},	
			{
				type       : "datepicker",
				name       : "track_release_date",
				selector   : "#track_release_date",
				options    : {
					dateFormat : $CONSTANT.ADMIN_DATE_FORMAT == "m/d/y" ? "mm/dd/yy" : "dd/mm/yy" 
				} 
			},
			{ 
				type     : "autocomplete",
				name     : "artist_name",
				selector : "#track_artist_name",
				required : false,
				options  : {
					source   : $CONSTANT.AUTOCOMPLETE+"?type=artists&maxRows=10",
					minLength: 1
				}
			},
			{ 
				type     : "autocomplete",
				name     : "track_title",				
				options  : {
					source:function( request, response ) {
						$.ajax({
							url: $CONSTANT.AUTOCOMPLETE_YQL,
							dataType: "json",
							cache    : false,
							data: {
								maxRows: 5,
								term   : request.term,
								artist : $("#track_artist_name").val(),
								method : "track" 
							},
							success: function(data) {
								response($.map(data,function(item) {
									var label = [item.title];
									if(item.artist!="") label.push("<small style='color:#BBBBBB;'><br/>Artist:"+item.artist+"</small>");
									if(item.album!="") label.push("<small style='color:#BBBBBB;'><br/>Album:"+item.album+"</small>");
									return {
										label: label.join(""),
										value: item.title,
										term : item
									}
								}));
							}
						});
					},
					select: function(e,ui){
						var data = ui.item.term;
						$("#track_artist_name").val(data.artist);
						$("#track_release_date").val(data.album_release_date);
						console.log(data);
						$("#track_label").val(data.label);
					},
					minLength: 1
				}
			},
			{type: "inputtext",name: "track_label"},
			{type: "inputtext",name: "track_about"},
			{type: "inputtext",name: "track_credits"},
			{type: "inputtext",name: "track_lyrics"},
			{type: "inputtext",name: "track_lyrics_author"},
			{type: "inputtext",name: "_nonce",selector:"[name=_track_nonce]"},
			{type: "inputtext",name: "track_id"},	
		]
	});
});
</script>