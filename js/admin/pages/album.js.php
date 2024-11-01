<script>
jQuery(function($) {
	$(".wordtour-column,").sortable({
		connectWith: '.wordtour-column',
		handle     : '.wordtour-panel-hndl-header',
		stop     : function(){
			setPanelsOrder("album");
		}	
	});
	
	$("#wordtour-panel-more").formpanel({page:"album"});
	$("#wordtour-panel-order").formpanel({page:"album"});
	$("#wordtour-panel-buy").formpanel({page:"album"});
	$("#wordtour-panel-tracks").formpanel({
		page   : "album",
		buttons: [
			{
				title : "Add",
				text  : false,
				icon  : "ui-icong-plus"
			}
  	]});

	$("#wordtour-panel-genre").formpanel({page: "album"});
	
	$("#show_all_artists").click(function(){
		openAllDataDialog("show-all-artists-dialog","#album_artist_name",$CONSTANT.DIALOG_ALL_ARTISTS,"All Artists");
		return false;
	});

	$("#add-artist").click(function(){
		openInsertArtistDialog({},function(r){
			$("#album_artist_name").val(r.result.artist_name).focus();
		});
		return false;
	});
		
	function loadAlbum(id){
		var f = $(this); 
		var id = id || "";
		f.wordtourform("ajax",{action:"get_album",album_id:id},function(r){
			f.wordtourform("set",r);
		});
	}
	
	$("#album-form").wordtourform({
		init     : function(){
			loadAlbum.call(this,$(this).find("[name=album_id]").val());
		},
		complete : function(e,response) {
			$("#wordtour-button-save").toolbarbutton("enable");
			$("#wordtour-button-import").toolbarbutton("enable");	
		},
		beforeSend: function(){
			$("#wordtour-button-save").toolbarbutton("disable");
			$("#wordtour-button-import").toolbarbutton("disable");
		},
		items: [
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-save",
				options   : {
					cls : "save",
					innerText1: "Updated <span id='album_publish_date'></span>",
					disabled : true,
					click    : function(e){
						var form = $("#album-form");
						var isNew = form.find("[name=album_id]").val() == "";
						var action =  isNew ? "insert_album" : "update_album";
						var data = $.extend({action:action},form.wordtourform("serialize"));						
						form.wordtourform("ajax",data,function(r){
							if(r.type=="success") {
								if(!isNew) {
									form.wordtourform("set",r.result);
									this.alert("show","success","Album updated Successfully");
								} else {
									loadAlbum.call(form);
									this.alert("show","success","Album \""+r.result.album_title+"\" added Successfully, <a href='"+$CONSTANT.ADMIN_URL+"page=wt_albums&action=edit&album_id="+r.result.album_id+"'>Click here to Edit</a>");
								}
							} else {
								
							}
						});	
					}
				}
			},
			{ 
				type      : "toolbarbutton",
				selector  : "#wordtour-button-import",
				options   : {
					cls : "import",
					disabled : true,
					click    : function(e){
						var form = $("#album-form");

						new Lib.ImportAlbumInfoDialog("import-album-dialog",{
							markupData : {
								album   : $("#album_title").val(),
								artist : $("#album_artist_name").val()
							}
						}).wrap().setDialog({
							title   : "Import Album Info"
						}).open();
							
					}
				}
			},
			{ 
				name      : "album_similar_status",
				selector  : "#album_similar_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "album_tracks_status",
				type      : "button",
				selector  : "#album_tracks_status",
				inputType :"checkbox"
			},
			{
				type    : "tracksmanager",
				name    : "album_tracks",
				selector: "#wordtour-panel-tracks .wordtour-panel-content",
				options : {
					addTrackSelector: "#wordtour-panel-tracks button:first",
				}
			},
			{
				type    : "genremanager",
				name    : "album_genre",
				selector: "#wordtour-panel-genre .wordtour-panel-content"
			},		
			{ 
				type      : "readonlytext",
				selector  : "#album_publish_date",
				jPath     : "album_publish_date",
				renderer  : function(v){
					$("#wordtour-button-save .update").css({visibility:(v!="") ? "visible" : "hidden"});
					return v;
				}
			},
			{
				selector : "#wordtour-poster-panel .wordtour-panel-content",
				name     : "album_thumbnail_id",
				jPath    : "album_thumbnail_id,album_thumbnail",
				type     : "postermanager",
				options  : {
					mediaUrl: $CONSTANT["MEDIA_LIBRARY"]
				}
			},
			{
				type       : "datepicker",
				name       : "album_release_date",
				selector   : "#album_release_date",
				options    : {
					dateFormat : $CONSTANT.ADMIN_DATE_FORMAT == "m/d/y" ? "mm/dd/yy" : "dd/mm/yy" 
				} 
			},
			{ 
				type     : "autocomplete",
				name     : "artist_name",
				selector : "#album_artist_name",
				required : false,
				options  : {
					source   : $CONSTANT.AUTOCOMPLETE+"?type=artists&maxRows=10",
					minLength: 1
				}
			},
			{ 
				type     : "inputtext",
				name     : "album_title"
			},
			{ 
				type      : "component",
				selector  : "#album_permalink",
				jPath     : "permalink",
				options   : {
					setValue  : function(v){
						var target = $("#album_permalink");
						if(v!="") {
							this.find("a").attr("href",v).text(v);
							target.show();
						} else{
							target.hide();
						}
					}
				}
			},
			{type: "inputtext",name: "album_label"},
			{type: "dropdown",name: "album_type"},
			{type: "inputtext",name: "album_about"},
			{type: "inputtext",name: "album_credits"},
			{type: "inputtext",name: "album_buy_amazon"},
			{type: "inputtext",name: "album_buy_amazon_mp3"},
			{type: "inputtext",name: "album_buy_itunes"},
			{type: "inputtext",name: "album_buy_link_1"},
			{type: "inputtext",name: "album_buy_link_2"},
			{type: "inputtext",name: "album_buy_link_3"},
			{type: "inputtext",name: "album_buy_pay_pal"},
			{type: "inputtext",name: "album_order"},
			{type: "inputtext",name: "_nonce",selector:"[name=_album_nonce]"},
			{type: "inputtext",name: "album_id"},	
		]
	});
});
</script>