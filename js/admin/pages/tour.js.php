<script>

jQuery(function($) {

	$(".wordtour-column,").sortable({
		connectWith: '.wordtour-column',
		handle     : '.wordtour-panel-hndl-header',
		stop     : function(){
			setPanelsOrder("tour");
		}	
	});
	
	$("#wordtour-panel-order").formpanel({page:"tour"});
	$("#wordtour-panel-info").formpanel({page:"tour"});
	$("#wordtour-panel-genre").formpanel({page:"tour"});
	$("#wordtour-panel-category").formpanel({page:"tour"});
	$("#wordtour-panel-video").formpanel({
		page:"tour",
		buttons: [
			{
				title : "Search and Add Videos",
				text  : false,
				icon  : "ui-icon-search"
			}
  		]
	});
	
	$("#wordtour-panel-gallery").formpanel({
		page:"tour",
		buttons: [
			{
				title : "Add New Gallery",
				text  : false,
				icon  : "ui-icon-plus"
			}
  		]
	});
	
	function loadTour(id){
		var f = $(this); 
		var id = id || "";
		f.wordtourform("ajax",{action:"get_tour",tour_id:id},function(r){
			f.wordtourform("set",r);
		});
	}
	
	$("#tour-form").wordtourform({
		init     : function(){
			loadTour.call(this,$(this).find("[name=tour_id]").val());
		},
		complete : function(e,response) {
			$("#wordtour-button-save").toolbarbutton("enable");
			if($(this).find("[name=tour_id]").val()!="") {
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
				name    : "tour_genre",
				selector: "#wordtour-panel-genre .wordtour-panel-content"
			},		
			{
				type    : "gallerymanager",
				name    : "tour_gallery",
				selector: "#wordtour-panel-gallery .wordtour-panel-content",
				jPath   : "tour_gallery",
				options : {
					dialogHandler: "#wordtour-panel-gallery button:first"
				}
			},
			{
				type    : "categorymanager",
				name    : "tour_category",
				selector: "#wordtour-panel-category .wordtour-panel-content",
				jPath   : "tour_category"
			},
			{
				type      : "videomanager",
				name      : "tour_videos",
				selector  : "#wordtour-panel-video .wordtour-panel-content",
				jPath     : "tour_videos", 
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
						var form = $("#tour-form");
						var isNew = form.find("[name=tour_id]").val() == "";
						var action =  isNew ? "insert_tour" : "update_tour";
						var data = $.extend({action:action},form.wordtourform("serialize"));						
						form.wordtourform("ajax",data,function(r){
							if(r.type=="success") {
								if(!isNew) {
									form.wordtourform("set",r.result);
									this.alert("show","success","Tour updated Successfully");
								} else {
									loadTour.call(form);
									this.alert("show","success","Tour \""+r.result.tour_name+"\" added Successfully, <a href='"+$CONSTANT.ADMIN_URL+"page=wt_tour&action=edit&tour_id="+r.result.tour_id+"'>Click here to Edit</a>");
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
						var form = $("#tour-form");
						var id = form.find("[name=tour_id]").val();
						var data = {action:"default_tour",tour_id:id};						
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
						var form = $("#tour-form");
						var id = form.find("[name=tour_id]").val();
						var data = {action:"remove_default_tour",tour_id:id};						
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
				selector  : "#tour_publish_date",
				jPath     : "tour_publish_date",
				renderer  : function(v){
					$("#wordtour-button-save .update").css({visibility:(v!="") ? "visible" : "hidden"});
					return v;
				}
			},
			{ 
				name      : "tour_gallery_status",
				selector  : "#tour_gallery_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "tour_flickr_status",
				type      : "button",
				selector  : "#tour_flickr_status",
				inputType :"checkbox"
			},
			{ 
				name      : "tour_video_status",
				selector  : "#tour_video_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "tour_post_status",
				selector  : "#tour_post_status",
				type      : "button",
				inputType :"checkbox"
			},
			{ 
				name      : "tour_tour_status",
				selector  : "#tour_tour_status",
				type      : "button",
				inputType :"checkbox"
			},
			{
				selector : "#wordtour-poster-panel .wordtour-panel-content",
				name     : "tour_thumbnail_id",
				jPath    : "tour_thumbnail_id,tour_thumbnail",
				type     : "postermanager",
				options  : {
					mediaUrl: $CONSTANT["MEDIA_LIBRARY"]
				}
			},
			{ 
				type      : "component",
				selector  : "#tour_permalink",
				jPath     : "permalink",
				options   : {
					setValue  : function(v){
						var target = $("#tour_permalink");
						if(v!="") {
							this.find("a").attr("href",v).text(v);
							target.show();
						} else{
							target.hide();
						}
					}
				}
			},
			{type: "inputtext",name: "tour_name"},
			{type: "inputtext",name: "tour_order"},
			{type: "inputtext",name: "tour_description"},
			{type: "inputtext",name: "_nonce",selector:"[name=_tour_nonce]"},
			{type: "inputtext",name: "tour_id"}
		]
	});
});
</script>