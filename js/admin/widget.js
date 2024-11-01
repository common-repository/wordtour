jQuery(function($){
	
window.setPanelsOrder = function(page) {
	var page = page || "event";
	var panelsOrder = {left:[],right:[]};
	$(".wordtour-column-left input.panel").each(function(){
		panelsOrder.left.push([this.value,$(this).parents(".wordtour-panel").formpanel("isCollapse") ? 0 : 1]);
	});
	$(".wordtour-column-right input.panel").each(function(){
		panelsOrder.right.push([this.value,$(this).parents(".wordtour-panel").formpanel("isCollapse") ? 0 : 1]);
	});

	$.post($CONSTANT["PLUGIN_AJAX"],{
		action :  "panel-state",
		panels :  JSON.stringify(panelsOrder),
		page   : page  
	},function(r){

	},"html");
};

$.widget("ui.video", {
   options: {
	 id        : "youtube-video",
	 searchDefault : "",
     inputName : "" 
   },
   _create: function(options) {
	   var that = this;
	   // hidden field
	   $.extend(this.options,options);
	   this.videos = [];
	   
	   this.playerWrapMarkup = ["<div id='"+this.options.id+"-swf-wrap' style='width:300;height:225px;'>",
	                            	"<img src='http://img.youtube.com/vi/"+new Date()+"/0.jpg' width='300' height='225'>",
	                            "</div>"].join(""); 
	   this.element.prepend([
         "<input type='hidden' name='"+this.options.inputName+"'>",
         "<div id='"+this.options.id+"-tabs' style='border:0px;'>",
         	"<ul>",
         		"<li><a href='#"+this.options.id+"-current'>Current</a><li>",
         		"<li><a href='#"+this.options.id+"-youtube'>YouTube</a><li>",
         		"<li><a href='#"+this.options.id+"-vimeo'>Vimeo</a><li>",
         		"<li><a href='#"+this.options.id+"-media'>Media Library</a><li>",
         	"</ul>",
         	"<div id='"+this.options.id+"-current'>",
         		"<div style='text-align:center;margin:3px;height:225px;'>"+this.playerWrapMarkup+"</div>",
         		"<div class='video-display'>",
         			"<div class='clear' style='clear:both;'></div>",
     			"</div>",
     		"</div>",
     		"<div id='"+this.options.id+"-youtube'>",
     			"<div class='video-filter'>",
     				"<input type='text' class='search' value='"+this.options.searchDefault+"'></input>",
     				"<select class='order-by'><option value='relevance'>Relevence</option><option value='published'>Upload Date</option><option value='viewCount'>View Count</option><option value='rating'>Rating</option></select>",
     				"<select class='time'><option value='all_time'>All Time</option><option value='today'>Today</option><option value='this_week'>This Week</option><option value='this_month'>This Month</option></select>",
     				"<a href='#' class='search button'>Search</a>",
     			"</div>",
     			"<div class='video-list'></div>",
     			"<div class='video-nav'>Total Results:<span class='total'></span>&nbsp;&nbsp;&nbsp;&nbsp;<a href='#' class='prev'>Prev</a>&nbsp;&nbsp;&nbsp;<a href='#' class='next'>Next</a></div>",
     		"</div>",
     		"<div id='"+this.options.id+"-vimeo'>",
     		
     		"</div>",
     		"<div id='"+this.options.id+"-media'></div>",
     	 "</div>"
       ].join(""));
	   // tabs
	   $("#"+this.options.id+"-tabs").tabs();
	   // wrapper
	   this._initCurrent();
	   // search
	   this._initSearch();
   },
   _initCurrent:function(){
	   var that = this;
	   this.wrapper = this.element.find(".video-display");
	   this.wrapper.click(function(e){
		   var elem = e.target; 
		   if(elem.tagName.toUpperCase() == "A" && $(elem).hasClass("remove")) {
			   that.remove($(elem).attr("href")); 
		   }
		   if(elem.tagName.toUpperCase() == "IMG" && $(elem).hasClass("play")) {
			   var swfId = that.options.id+"-swf-player";
			   var videoId = $(elem).parent().attr("class");
			   if(!that.player){
				   window.onYouTubePlayerReady = function(playerId) {
					   that.player = document.getElementById(swfId);
					   that.player.playVideo();
				   }
				   swfobject.embedSWF("http://www.youtube.com/v/"+videoId+"?enablejsapi=1&playerapiid=ytplayer", 
						   that.options.id+"-swf-wrap", "300","225","8", null, null, {allowScriptAccess: "always"},{id: swfId});
			   } else {
				   that.player.loadVideoById(videoId);
			   }
		   }
		   
		   return false;
	   });
   },
   search : function(params){
	   var that = this;
	   var l = $("#"+this.options.id+"-search div.video-list").html("<div style='padding:10px;'>Searching...</div>");
	   var w = this.filterWrapper;
	   var n = this.navigation;
	   var params = $.extend({
	    		v: 2,
	    		alt : "jsonc",
	    		format: 5,
	    		category: "Music",
	    		"max-result" : 25
	    },params);
	    $.ajax({
			url: "http://gdata.youtube.com/feeds/api/videos?"+$.param(params),
			dataType: "jsonp",
			//data: data,
			success: function(r) {
				l.html("");
				var html = [];
				if(r.data.totalItems == 0) {
					l.html("<div style='padding:10px;'>No Results</div>");
					return false;
				}
				(function Navigation(){
					var totalItems = r.data.totalItems,startIndex = r.data.startIndex,itemsPerPage = r.data.itemsPerPage; 
					var isNav = totalItems>r.data.itemsPerPage;
					if(isNav) {
						var nextIndex = startIndex+(itemsPerPage);
						var prevIndex = startIndex >= itemsPerPage ? startIndex - itemsPerPage: 1; 
						n.show();
						n.find(".prev")[startIndex >= itemsPerPage ? "show":"hide"]().data("nav",$.extend({},params,{"start-index":prevIndex}));
						n.find(".next")[totalItems > nextIndex ? "show":"hide"]().data("nav",$.extend({},params,{"start-index":nextIndex}));
						n.find(".total").html(totalItems);
					} else {
						n.hide();
					}
				})();
				
				$.each(r.data.items,function(){
					var isCurrent = false;
					for(var i = 0 ; i<=that.videos.length ;i++) {
						if(that.videos[i] == this.id) {
							isCurrent = true;
							break;
						}
					};
					if(!isCurrent) {
						html.push(["<div class='row'>",
			           		"<div class='image'><img src='"+this.thumbnail.sqDefault+"'></div>",
			           		"<div class='content'>",
			           			"<b>"+this.title+"</b><br/>",
			           			this.description+"<br/>",
			           			"<div class='info'>"+this.viewCount+" views | rating "+this.rating+"</div>",
			           		"</div>",
			           		"<div class='selectbutton'>",
			           			"<a href='"+this.id+"' class='button select'>Select</a>",
			           		"</div>",
			           		"<div style='clear:both;'></div>",
						"</div>"].join(""));
					}
				})
				l.html(html.join(""));
			}
		})
   },
   _initSearch: function(){
	   var that = this;
	   var w = this.filterWrapper = this.element.find(".video-filter");
	   var n = this.navigation = this.element.find(".video-nav").hide();
	   
	   n.find("a.next,a.prev").click(function(){
		   that.search($(this).data("nav"));
	   });
	   
	   w.find("a.search").click(function(){
		   n.find("a.prev,a.next").each(function(){
			   $(this).data("nav",null);
		   });
		   that.search({
			   "start-index":1,
			   format: 5,
			   time : w.find("select.time").val(),
			   q: $(this).parents("div").find("input.search").val(),
			   orderby : w.find("select.order-by").val()
		   });
	   });
	   
	   this.element.find(".video-list").click(function(e){
	   	   var elem = e.target;
	   	   if(elem.tagName.toUpperCase() == "A" && $(elem).hasClass("select")) {
		      that.add($(elem).attr("href"));
				   $(elem).parents("div.row").fadeOut('slow',function(){
					   $(this).remove();
				   });
		   }
		   return false;
		});
   },
   _getPlayerRef : function(videoId){
	   return "player-"+videoId;
   },
   destroy: function() {
	   var that = this;
       $.Widget.prototype.destroy.apply(this, arguments);
       if(that.player) swfobject.removeSWF(that.player.id);
       window.onYouTubePlayerReady = null;
   },
   remove : function(videoId){
	   var that = this;
	   var wrap = this.wrapper.find("[class="+videoId+"]").parent().fadeOut("slow",function(){
		   if(that.player) {
			   swfobject.removeSWF(that.player.id);
			   that.player = null;
			   $("#"+that.options.id+"-current").find("div:first").append(that.playerWrapMarkup);
		   }
		   that.videos = jQuery.grep(that.videos, function(v) {
			    return v!=videoId;
			});
		   that.setValue();
	   });
	   
   },
   setValue : function() {
	   this.element.find("[name="+this.options.inputName+"]").val(this.videos.join(","));
   },
   add : function(videos){
	   var that = this;
	   var w = this.wrapper;
	   if(!videos) videos = [];
	   if(!$.isArray(videos)) videos = [videos]; 
	   $.each(videos,function(){
		   var videoId = this;
		   that.videos.push(videoId);
		   var elmIdStr = that.options.id+"-"+videoId;
		   w.find("div:last").before([
              "<div class='video-item'>",
              		"<div class='"+videoId+"' id='"+elmIdStr+"'>",
              			"<img class='play' src='http://img.youtube.com/vi/"+videoId+"/2.jpg'>",
              			"<div style='text-align:center;margin-top:10px;'><a href='"+videoId+"' class='button remove'>Remove</a></div>",
              		"</div>",
              "</div>"].join(""));
		   
//		   swfobject.embedSWF("http://www.youtube.com/v/"+videoId+"?enablejsapi=1&playerapiid=ytplayer-"+videoId, 
//				   elmIdStr, "130","97","8", null, null, {allowScriptAccess: "always"},{id: "player-"+videoId},function(r){
//					   //only remove a SWF after it has been loaded, to avoid broken references) and completely 
//					   if(r.success) {
//						   $("#"+that._getPlayerRef(videoId)).after("<div style='text-align:center;margin-top:10px;'><a href='"+videoId+"' class='button remove'>Remove</a></div>");
//					   }
//				   });
	   }); 
	  this.setValue();
   }
 });

$.extend($.ui.video, {
	eventPrefix: "video"	
});

$.widget("ui.thumbnail", {
   options: {
     input_name: ""
   },
   _markup : function(url,width,height) {
	   var content = "<div style='width:150px;height:150px;background-color:transparent'></div>";
	   if(url && width && height ) content = "<img src='"+url+"' width='"+width+"' height='"+height+"'/>" ;
	   return "<div style='background-color:#EEEEEE;padding:20px;'>"+content+"</div>";
   },
   _create: function() {
	    var that = this, o = this.options, n = o.input_name ;
	    this.element
	    .addClass("ui-thumbnail")
	    .after("<input type='hidden' name='"+(n || "")+"'>");

	    this._input =  this.element.find("~ [name="+n+"]");
   },
   value: function(value,data) {
	    var elem = this.element ;
		var v = (value == 0 || value == "" || !data.url) ? 0 : value ;
		this._input.val(v);
	    elem.html(v === 0 ? this._markup() : this._markup(data.url,data.width,data.height)); 
   		return this.element ;
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); // default destroy
        // now do other stuff particular to this widget
   }
 });

$.extend($.ui.thumbnail, {
	eventPrefix: "thumbnail"	
});

$.widget("ui.poster", {
   options: {
     hiddenSelector: "",
     srcSelector   : "",
     width		   : 75,
     height		   : 75,
     iframe        : ""
   },
   _markup : function() {
	   var content = ["<table>",
	   		"<tr>",
	   			"<td>",
   					"<div class='thumbnail-wrap' style='width:"+this.options.width+"px;height:"+this.options.height+"px;'></div>",
   				"</td>",
	   			"<td>",
	   				"<a class='remove-poster button' title='Remove Poster' href='#'>Remove Poster</a> ",
	   				//"<a class='remove-poster button' title='Suggest Poster' href='#'>Suggest Poster</a>",
	   			"<td>",
	   		"</tr>",
	   "</table>",
	   "<div id='tab-library-wrap' class='library-wrap'></div>",
	   ].join("");
	   return "<div>"+content+"</div>";
   },
   _create: function() {
	    var that = this, o = this.options, n = o.name ;
	    this.element
	    .addClass("ui-poster")
	    .append(this._markup());
		this._input =  this.element.find("input[name="+this.options.hiddenSelector+"]");
		that._renderLibrary();
		this._events();
		this.val(this._input.val(),this.element.find(this.options.srcSelector).val());
		
   },
   _assignThumbnal : function(src){
	   	var wrap = this.element.find(".thumbnail-wrap");
		var tag = src ? "<img src='"+src+"' width='"+this.options.width+"' height='"+this.options.height+"'>" : "";
	   	wrap.html(tag);  
   },
   val : function(value,src) {
	   this._input.val(value);
	   this._assignThumbnal(src);	
   },
   _renderLibrary : function(){
	   var that = this;
	   var target = this.element.find(".library-wrap");
	   
	   var html = ["<iframe ",
	  				"frameborder='0' ",
	  				"class='thumbnail-library' ", 
	  				"src='"+this.options.iframe+"' hspace='0'></iframe>"];

	   
	   target.html(html.join(""));
		  				
	   target.find("iframe").load(function(){
		   	$(this).contents().find('.media-upload-header').hide();
		   	$(this).contents().find('#media-upload-header').hide();
			$(this).contents().find('#media-items').css({height:340,overflow:"auto"});
		   	$(this).contents().find('.ml-submit').remove();
		   	
			$(this).contents().find('.describe-toggle-on').each(function(){
				$(this).html("Select").hide().after("<a href='#' style='float:right;margin-right:20px;line-height:36px;display:block;' class='thumbnail-select'>Select</a>");
				var name = $(this).parent().find("input[name^=attachments]").attr("name");
				var attachId = name.slice(name.indexOf("[")+1,name.indexOf("]")) ;
				var imgSrc = $(this).parent().find("img").attr("src"); 
				$(this).next().click(function(){
					that.val(attachId,imgSrc);
				});
			});
		});
   },
   _events : function(){
	    var that = this;
		var elem = this.element;
		
		
		elem.find("a.remove-poster").click(function(){
			that.val(0,false);
		});
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

$.extend($.ui.thumbnail, {
	eventPrefix: "poster"	
});

$.widget("ui.gallery", {
   options: {
     iframe        : "",
     name          : ""
   },
   _markup : function() {
	   var content = ["<div class='wt-thumbs-wrap'><form><div style='clear:both;'></div></form></div>",
	                  "<input name='"+this.options.name+"' type='hidden' value=''>",
					  "<div class='wt-library-wrap'></div>",
				      "<div style='clear:both;'></div>"].join("");
	   return "<div>"+content+"</div>";
   },
   _create: function() {
	    var that = this, o = this.options, n = o.name ;
	    this.element
	    .addClass("wt-gallery")
	    .append(this._markup())
	    .find(".wt-thumbs-wrap").click(function(e){
	    	if(e.target.tagName.toUpperCase() == "SPAN") {
				$(e.target).parents(".wt-thumb-wrap").fadeOut('slow',function(){
					$(this).remove();
					that.val(that._getValues());
				});
			}	
	    });
		this._renderLibrary();
		//this._events();
		//this.val(this._input.val(),this.element.find(this.options.srcSelector).val());
		
   },
   addThumbnail : function(value,src) {
		var html = ["<div class='wt-thumb-wrap ui-state-default' style='display:none;'>",
						"<input type='hidden' value='"+value+"'></input>",
						"<img src='"+src+"'></img>",
						"<div class='remove'><span>Delete</span></div>",
					"</div>"].join("");
		
		var root = this.element.find(".wt-thumbs-wrap form .wt-thumb-wrap:last"),thumb,thumb_exist; 
		if(root.length>0){
			thumb_exist = this.element.find(".wt-thumbs-wrap form .wt-thumb-wrap input[value="+value+"]");
			if(thumb_exist.length>0) {
				thumb = thumb_exist.parents(".wt-thumb-wrap");
				thumb.fadeOut('fast');		
			} else {
				thumb = root.after(html).next();
			}
		} else {
			thumb = this.element.find(".wt-thumbs-wrap form").prepend(html).find(".wt-thumb-wrap:first");
		}

		if(thumb) {
			thumb.fadeIn('slow');
		};
   },
   _getValues : function() {
	   var v = [];
	   $(".wt-thumbs-wrap form input").each(function(){
		   v.push(this.value);
	   });
	   return v;
   },
   val: function(value){
	   if(!value) {
		   return this._getValues();
	   } else {
		   if(this.options.name!="") this.element.find("input[name="+this.options.name+"]").val(value.join(","));
	   }  
   },
   _renderLibrary : function(){
	   var that = this;
	   var target = this.element.find(".wt-library-wrap");
	   
	   var html = ["<iframe ",
	  				"frameborder='0' ",
	  				"class='thumbnail-library' ", 
	  				"src='"+this.options.iframe+"' hspace='0'></iframe>"];

	   target.html(html.join(""));
		  				
	   target.find("iframe").load(function(){
		   	$(this).contents().find('body').css({height:"auto"});
		   	$(this).contents().find('.media-upload-header').hide();
		   	$(this).contents().find('#media-upload-header').hide();
		   	$(this).contents().find('#media-items').css({height:340,overflow:"auto"});
		   	$(this).contents().find('.ml-submit').remove();
		   	
			$(this).contents().find('.describe-toggle-on').each(function(){
				$(this).html("Select").hide().after("<a href='#' style='float:right;margin-right:20px;line-height:36px;display:block;' class='thumbnail-select'>Select</a>");
				var name = $(this).parent().find("input[name^=attachments]").attr("name");
				var attachId = name.slice(name.indexOf("[")+1,name.indexOf("]")) ;
				var imgSrc = $(this).parent().find("img").attr("src"); 
				$(this).next().click(function(){
					that.addThumbnail(attachId,imgSrc);
					that.val(that._getValues());
				});
			});
		});
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

$.extend($.ui.gallery, {
	eventPrefix: "gallery"	
});




// new ui
$.widget("ui.tracksmanager", {
   options: {
		addTrackSelector: ""
   },
   // object of {id:int,name:"artist name"}
   _addTrack: function(track){
	    var track = track || {};
		this.element.append([
			"<div class='track-manager-row ui-helper-clearfix ui-corner-all ui-widget-header' style='margin-bottom:5px;padding:5px;'>",
				"<div class='ui-icon ui-icon-arrowthick-2-n-s' style='float:left;margin-right: 3px;margin-top:4px;cursor:pointer;'>Sort Track</div>",
				"<div style='float:left;width:80%'><input type='text' name='album-track'/></div>",
				"<div class='ui-icon ui-icon-close' style='float:left;margin-left: 3px;margin-top:4px;cursor:pointer;'>Remove Track</div>",
			"</div>"
		].join(""))
		.find("input:last").autocomplete({
				source   : $CONSTANT.AUTOCOMPLETE+"?type=tracks&maxRows=10",
				minLength: 1
		}).val(track.name||"");
   },
   _removeTrack: function(elem){
		elem.remove();
   },
   _initEvents : function(){
	   var that = this;
	   $(this.options.addTrackSelector).click(function(){
		    that._addTrack();
		    that.value();
			return false;
	   });

	   this.element.find(".ui-icon-close").live("click",function(e){
			that._removeTrack($(e.target).parents(".track-manager-row:first"));		
	   });
	   
	   
	   
	   this.element.sortable({handle:".ui-icon-arrowthick-2-n-s",placeholder: 'ui-widget-content ui-corner-all'});
	  // this.element.disableSelection();

   },
   _create: function(options) {
	   var that = this;
	   $.extend(this.options,options);
	   this._initEvents();
   },
   value : function(v){
	   var that = this;
	   if(!$.isArray(v)) {
		   var value = this.element.find("input[name=album-track]").map(function(){
				return $.trim($(this).val());
		   });
		   return JSON.stringify($.makeArray(value));
	   }
	   
	   if(v) {
		   this.element.find("div.track-manager-row").remove();
			if($.isArray(v)) {
				$.each(v,function(){
					that._addTrack(this);
				});
			}
	   }
	   
   },
   destroy: function() {
	   var that = this;
       $.Widget.prototype.destroy.apply(this, arguments);
   }
});

$.extend($.ui.tracksmanager, {
	eventPrefix: "tracksmanager"	
});

$.widget("ui.genremanager", {
   options: {
		
   },
   // object of {id:int,name:"artist name"}
   _addGenre: function(genre){
	    var genre = genre || "";
		this.element.append([
			"<div class='genre-manager-row ui-helper-clearfix ui-corner-all ui-state-default' style='margin-left:0px;margin:3px;padding:3px;display:inline-block;'>",
				"<div class='genre' style='float:left;'>"+genre+"</div>",
				"<div class='ui-icon ui-icon-close' style='float:left;margin-left: 3px;margin-top:1px;cursor:pointer;'>Remove Genre</div>",
			"</div>"
		].join(""));
   },
   _removeGenre: function(elem){
		elem.remove();
   },
   _initEvents : function(){
	   var that = this;

	   this.element.find(".ui-icon-close").live("click",function(e){
			that._removeGenre($(e.target).parents(".genre-manager-row:first"));		
	   });
	   
	   this.element.find(".add-wrapper input").autocomplete({
		   source   : $CONSTANT.AUTOCOMPLETE+"?type=genre",
		   minLength: 1
	   });
	   
		this.element.find(".add-wrapper a").click(function(){
				var value = $(this).prev().val();
				var isExist = false;
				var currentValues = that.element.find("div.genre-manager-row .genre").map(function(){
					if($(this).text() == value) isExist = true;
					return this;
				});
				
				if(value!="" && !isExist) {
					that._addGenre(value);
					$(this).prev().val("");
				}
				
				return false;
		});
		
		$("#show_all_genre").click(function(){
			console.log($CONSTANT.DIALOG_ALL_GENRE);
			openAllDataDialog("show_all_genre-dialog","#genremanager-searchbox",$CONSTANT.DIALOG_ALL_GENRE,"All Genres");
			return false;
		});
   },
   _create: function(options) {
	   var that = this;
	   $.extend(this.options,options);
	   this.element.append(["<div class='ui-helper-clearfix add-wrapper' style='margin-bottom:5px;'>",
	                        		"<input id='genremanager-searchbox' type='text' style='width:75%;'> ",
	                        		"<a class='button' href='#'>Add</a>",
	                        		"<br/>",
	                        		"<a id='show_all_genre' href='#' style='text-decoration : none;font-size:9px;font-style:italic;'>Show All Genres</a>",
	                        "</div>"].join(""));
	   this._initEvents();
   },
   value : function(v){
	   var that = this;
	   if(!$.isArray(v)) {
		   var value = this.element.find("div.genre-manager-row .genre").map(function(){
				return $.trim($(this).text());
		   });
		   return JSON.stringify($.makeArray(value));
	   }
	   
	   if(v) {
		   this.element.find("div.genre-manager-row").remove();
			if($.isArray(v)) {
				$.each(v,function(){
					that._addGenre(this);
				});
			}
	   }
	   
   },
   destroy: function() {
	   var that = this;
       $.Widget.prototype.destroy.apply(this, arguments);
   }
});

$.extend($.ui.genremanager, {
	eventPrefix: "genremanager"	
});


$.widget("ui.artistsmanager", {
   options: {
		addArtistSelector: ""
   },
   // object of {id:int,name:"artist name"}
   _addArtist: function(artist){
	    var artist = artist || {};
		this.element.append([
			"<div class='artist-manager-row ui-helper-clearfix' style='margin-bottom:5px;'>",
				"<div style='float:left;width:90%'><input type='text' name='event-more-artist'/></div>",
				"<div class='ui-icon ui-icon-close' style='float:left;margin-left: 3px;margin-top:4px;pointer:cursor;'>Remove Artist</div>",
			"</div>"
		].join(""))
		.find("input:last").autocomplete({
				source   : $CONSTANT.AUTOCOMPLETE+"?type=artists&maxRows=10",
				minLength: 1
		}).val(artist.name||"");
   },
   _removeArtist: function(elem){
		elem.remove();
   },
   _initEvents : function(){
	   var that = this;
	   $(this.options.addArtistSelector).click(function(){
		    that._addArtist();
		    that.value();
			return false;
	   });

	   this.element.find(".ui-icon-close").live("click",function(e){
			that._removeArtist($(e.target).parents(".artist-manager-row:first"));		
	   });
   },
   _create: function(options) {
	   var that = this;
	   $.extend(this.options,options);
	   this._initEvents();
   },
   value : function(v){
	   var that = this;
	   if(!$.isArray(v)) {
		   var value = this.element.find("input[name=event-more-artist]").map(function(){
				return $.trim($(this).val());
		   });
		   return JSON.stringify($.makeArray(value));
	   }
	   
	   if(v) {
		   this.element.find("div.artist-manager-row").remove();
			if($.isArray(v)) {
				$.each(v,function(){
					that._addArtist(this);
				});
			}
	   }
	   
   },
   destroy: function() {
	   var that = this;
       $.Widget.prototype.destroy.apply(this, arguments);
   }
});

$.extend($.ui.artistsmanager, {
	eventPrefix: "artistsmanager"	
});


$.widget("ui.gallerymanager", {
   options: {
	   	// array of galleries, [{id:id,name:name}]
		items : [],
		prefix: "wordtour-item",
		dialogHandler: ""
   },
   _itemMarkup : function(id,name){
	   return ["<li class='ui-selectee ui-helper-clearfix' name='attachment_type_id-"+id+"'>",
					"<div class='gallery-name'>"+name+"</div>",
					"<a class='gallery-edit' title='Edit "+name+"'></a>",
				"</li>"].join("");
   },
   addItem: function(id,name){
	   var that = this;
	   this.element.find("ol").prepend(this._itemMarkup(id,name)).find("li:first").hide().fadeIn("slow").click(function(e){
		   that._attachLiEvent($(e.target));
	   });
   },
   updateItem: function(id,name){
	   this.element.find("ol li[name*=attachment_type_id-"+id+"] .gallery-name").text(name); 
   },
   // draw available galleries
   _initItems : function(items) {
	   var that = this;
	   var markup = ["<div class='wordtour-gallery-wrap'><ol class='wordtour-selectable'>"];
	   $.each(items,function(){
		    var id = $(this).val();
		    var name = $(this).parent().text();
			markup.push(that._itemMarkup(id,name));
			$(this).parents("li:first").remove();
	   })
	   markup.push("</ol></div>");
	   this.element.append(markup.join("")).find("ol").selectable({filter:"li"});   
   },
   _initEvents : function(){
	   var that = this;
	   $(this.options.dialogHandler).click(function(){
		   try {
			   that.dialog("new");
			   return false;
		   } catch(e) {
			   return false;
		   }
	   });
	   
	   this.element.find("ol li").click(function(e){
		   e.stopPropagation();
		   return that._attachLiEvent($(e.target));
	   });
   },
   _attachLiEvent: function(elem){
	   if(elem.hasClass("gallery-edit")) {
		    this.dialog("edit",this._getId(elem.parents("li")));
		    return false;
	   }
   },
   _create: function(options) {
	   var that = this;
	   $.extend(this.options,options);
	   this.items = this.element.find("input");
	   
	   this._initItems(this.items);
	   this._initEvents();
   },
   dialog: function(type,id){
	   var that = this;
	   if(type=="new") {
		   openInsertGalleryDialog({},function(r){
			   that.addItem(r.result.gallery_id, r.result.gallery_name);
		   });	
	   }
	   
	   if(type=="edit") {
		   openEditGalleryDialog({gallery_id:id},function(){
				var form = $(this).find("form");
				var data = $.extend({action:"update_gallery"},form.wordtourform("serialize"));
				var dialog = this;						
				form.wordtourform("ajax",data,function(r){
					that.updateItem(r.result.gallery_id, r.result.gallery_name);
					$(dialog).dialog('close');
				});
			});	
	   }
   },
   // clear all selected items
   clear: function(){
	   this.element.find("ol li").removeClass("ui-selected");
   },
   _getId : function(li){
	   var data = getDataFromStr(li.attr("name"));
	   return data.attachment_type_id;
   },
   value : function(v){
	   	var that = this;
	   	if(v && v!==undefined) {
	   		this.clear();
			$.each(v,function(){
				that.element.find("ol li[name*=attachment_type_id-"+this.attachment_type_id+"]")
				.addClass("ui-selected")
				.attr("name","attachment_id-"+this.attachment_id+":attachment_type_id-"+this.attachment_type_id);
			});
	   	} else {
		   	var value = [];
		   	that.element.find("ol li.ui-selected").each(function(){
		   		var p = getDataFromStr($(this).attr("name"));
		   		value.push(p);
		   	});
		    return value;
	   	}
   },
   destroy: function() {
	   var that = this;
       $.Widget.prototype.destroy.apply(this, arguments);
   }
});

$.extend($.ui.gallerymanager, {
	eventPrefix: "gallerymanager"	
});

$.widget("ui.categorymanager");

$.extend($.ui.categorymanager.prototype,$.ui.gallerymanager.prototype,{
	_itemMarkup : function(id,name){
	   return ["<li class='ui-selectee ui-helper-clearfix' name='attachment_type_id-"+id+"'>",
					"<div class='gallery-name'>"+name+"</div>",
				"</li>"].join("");
	}
});

//$.ui.categorymanager.prototype = new $.ui.gallerymanager(); 
$.extend($.ui.categorymanager, {
	eventPrefix: "categorymanager"	
});


$.widget("ui.postermanager", {
   options: {
     prefix     : "wordtour-poster",
     mediaUrl   : ""
   },
   showEditDialog : function(){
	   	var that = this;
		$("#"+this.options.prefix+"-dialog").dialog({
			autoOpen: true,
			modal   : true,
			width   : 700,
			height  : 570,
			open    : function(obj){
				var t = $(obj.target); 
				t.html("<iframe style='width:100%;height:520px;' frameborder='0' src='"+that.options.mediaUrl+"' hspace='0' scrolling='no'></iframe>");
				t.find("iframe").load(function(){
					var content = $(this).contents();
					content.find('html').css({margin:0,padding:0,backgroundColor:"#FFFFFF"}); 
					content.find('#media-upload-header,.ml-submit,.subsubsub').hide();
					content.find('.describe-toggle-on').each(function(){
						$(this).html("Select").hide().after("<a href='#' style='float:right;margin-right:20px;line-height:36px;display:block;' class='thumbnail-select'>Select</a>");
						var name = $(this).parent().find("input[name^=attachments]").attr("name");
						var attachId = name.slice(name.indexOf("[")+1,name.indexOf("]")) ;
						var imgSrc = $(this).parent().find("img").attr("src"); 
						$(this).next().click(function(){
							that.value(attachId,imgSrc);
							t.dialog("close");
						});
					});
				})
			}
		});
   },
   value : function(value,src) {
	   var w = this.element.find("div.poster");
	   var idRegExp = new RegExp("("+this.options.prefix+"-)(\\d*)");
	   var match = w.attr("class").match(idRegExp);
	   if(value !== undefined) {
		    if(match && $.isArray(match)) w.removeClass(match[0]);
		    if(value == "" || value==0) {
		   		w.addClass("no-poster").find("img").fadeOut("fast",function(){
					$(this).remove();
			   	});
		   	} else {
		    	w.removeClass("no-poster")
		    	.addClass(this.options.prefix+"-"+value)
		    	.html("")
		    	.append("<img src='"+src+"' border='0' style='display:none;'>").find("img").fadeIn("slow");	
		   	}
		} else {
			if(match && $.isArray(match)) {
				return match[2];
			} else {
				return "empty";
			}
			return "";
	   }	
   },
   _initMarkup : function() {
	   var markup = ["<div class='wordtour-media-box wordtour-thumbnail-box ui-widget-content ui-corner-tr'>",
				"<h5 class='ui-widget-header'>Poster</h5>",
				"<div class='poster no-poster'></div>",
				"<a class='ui-icon ui-icon-pencil' title='Add\Edit Poster' href='#'>Add Poster</a>",
				"<a class='ui-icon ui-icon-trash' title='Delete this poster' href='#'>Delete Poster</a>",
				"<div style='clear:both;'></div>",
			"</div><div id='"+this.options.prefix+"-dialog'></div>"];
		this.element.append(markup.join(""));
   },
   _initEvents : function(){
	    var that = this;
		this.element.find("a.ui-icon-pencil").click(function(){
			that.showEditDialog();
			return false;
		});

		this.element.find("a.ui-icon-trash").click(function(){
			that.value(0);
			return false;
		});
   },
   _create: function(options) {
	    $.extend(this.options,options);
	    this._initMarkup();
	    this._initEvents();
   },
  
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

 $.extend($.ui.postermanager, {
	eventPrefix: "postermanager"	
 });
 
 $.widget("ui.inputtext", {
	   options: {},
	   value : function(value) {
		   var elem = this.element ;
		   if(value || value =="") {
			   $(elem).val(value);
		   } else {
			   return elem.val();
		   }
	   },
	   _create: function(options) {
		    $.extend(this.options,options);
		    this.element.attr("autocomplete","off");
	   },
	   destroy: function() {
	       $.Widget.prototype.destroy.apply(this, arguments); 
	   }
	 });

 $.extend($.ui.inputtext, {
	eventPrefix: "inputtext"	
 });
 
 $.widget("ui.dropdown", {
   options: {
	 	change: false
   },
   value : function(value) {
	   var elem = this.element ;
	   if(value) {
		   $(elem).val(value);
	   } else {
		   return elem.val() || "";
	   }
   },
   _create: function(options) {
	    var that = this;
	    $.extend(this.options,options);
	    if(this.options.change) this.element.change(function(){
	    	that.options.change.call(this);
	    });
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

$.extend($.ui.dropdown, {
	eventPrefix: "dropdown"	
});
 
$.widget("ui.readonlytext", {
   options: {},
   value : function(value) {
	   var elem = this.element ;
	   if(value) {
		   $(elem).text(value);
	   } else {
		   return elem.html();
	   }
   },
   _create: function(options) {
	   $.extend(this.options,options);
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

$.extend($.ui.readonlytext, {
	eventPrefix: "readonlytext"	
});

$.widget("ui.component", {
   options: {
		setValue : function(){},
		getValue : function(){}
   },
   value : function(value) {
	   if(value) {
		   this.options.setValue.call(this.element,value);
	   } else {
		   this.options.getValue.call(this.element,value);
	   }
   },
   _create: function(options) {
	   $.extend(this.options,options);
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

$.extend($.ui.component, {
	eventPrefix: "component"	
});

$.widget("ui.toolbarbutton", {
   options: {
	   	prefix   : "wordtour-toolbar-button",
		cls      : "",
		innerTextStart: "",
		innerText1 : "&nbsp;",
		innerText2 : "&nbsp;",
		showInnerText : false,
		disabled : false,
		title    : ""
   },
   value : function(value) {
	   
   },
   disable: function(){
	    var className = this.options.prefix+"-"+this.options.cls;
	    if(this.element.hasClass(className)) this.element.removeClass(className);
	    if(!this.element.hasClass(className+"-dis")) this.element.addClass(className+"-dis");
   },
   enable: function(){
	    var className = this.options.prefix+"-"+this.options.cls;
	    if(this.element.hasClass(className+"-dis")) this.element.removeClass(className+"-dis");
	    if(!this.element.hasClass(className)) this.element.addClass(className);
   },
   _initMarkup : function(){
	    var showInner = this.options.showInnerText ? "style='display:block'" : "none";
	    var markup = ["<div class='wrap'>",
                		"<a href='#' class='title'>"+(this.options.title || this.element.attr("title"))+"</a>",
                		this.options.innerTextStart,
                		"<div class='update' "+showInner+">"+this.options.innerText1+"</div>",
                		"<div class='update' "+showInner+">"+this.options.innerText2+"</div>", 
                	  "</div>"].join("");  
	    this.element.addClass(this.options.prefix).addClass(this.options.prefix+"-"+this.options.cls).html(markup);
		if(this.options.disabled) this.disable();
   },
   _initEvents : function(){
	   var that = this;
	   this.element.click(function(e){
		   if(!that.element.hasClass(that.options.prefix+"-"+that.options.cls+"-dis")) {
			   that._trigger("click",e,that);
		   }
	   });
	   
//	   this.element.click(function(e){
//		   if(!that.element.hasClass(that.options.prefix+"-"+that.options.cls+"-dis")) {
//			   that._trigger("click",e,that);
//		   }
//	   });
	   
   },
   _create: function(options) {
	   $.extend(this.options,options);
	   this._initMarkup();
	   this._initEvents();
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

$.extend($.ui.toolbarbutton, {
	eventPrefix: "toolbarbutton"	
});

$.widget("ui.rsvpmanager", {
   options: {
		prefix : "wordtour-rsvp",
		height: false
   },
   _rowMarkup : function(id,name){
	   var prefix = this.options.prefix;
	   return ["<div class='"+prefix+"-row'>",
	           	"<div class='"+prefix+"-delete "+prefix+"-"+id+"'></div>",
	           	"<div class='"+prefix+"-user'>"+name+"</div>",
	           	"<div class='clear'></div>",
	           	"</div>"].join("");        
   },
   value : function(value) {
	   var that = this;
	   // set value
	   if($.isArray(value)) {
		  var wrap = this.element.find("."+this.options.prefix+"-wrap"); 
		  $.each(value,function(){
			  wrap.append(that._rowMarkup(this.id,this.nickname));
		  });
	   }
	   // get value
	   if(!value) {
		   
	   }
   },
   remove : function(rsvpId,elem){
	   var that = this;
	   $.post($CONSTANT.PLUGIN_AJAX,{action:"delete-rsvp",rsvp_id:rsvpId},function(data) {
		   if(typeof data === "object") {
			   if(data.type=="success") {
				   elem.parents("."+that.options.prefix+"-row").fadeOut("fast",function(){
					   $(this).remove();
				   });
			   }
		   }
	   },"json");
   },
   _events : function() {
	   var prefix = this.options.prefix,that=this;
	   this.element.find("."+prefix+"-wrap").click(function(e){
		   	if($(e.target).hasClass(prefix+"-delete")) {
		   		var idRegExp = new RegExp(".("+prefix+"-)(\\d*)");
			   	var match = $(e.target).attr("class").match(idRegExp);
			   	if($.isArray(match)) {
			   		$("body").append(["<div id='"+prefix+"-confirm'>",
			   		                  		"<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>",
			   		                  		"<span style='font-size:10px;'>RSVP will be permanently deleted and cannot be recovered. Are you sure?</span>",
			   		                  		"</p>",
			   		                  "</div>"].join(""));
			   		$("#"+prefix+"-confirm").dialog({
						resizable: false,
						height:160,
						modal: true,
						buttons: {
							'Delete RSVP': function() {
			   					$(this).dialog('close');
			   					that.remove(match[2],$(e.target));
							},
							Cancel: function() {
								$(this).dialog('close');
							}
						},
						close: function(){
							$(this).remove();
						}
					});
				}
		   	}
	   });
   },
   _markup : function(){
	 var wrapClass = this.options.prefix+"-wrap";
	 this.element.html("<div class='"+wrapClass+"'></div>");
	 if(this.options.height) this.element.find("."+wrapClass).css("height",this.options.height); 
   },
   _create: function(options) {
	   $.extend(this.options,options);
	   this._markup();
	   this._events();
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

$.extend($.ui.rsvpmanager, {
	eventPrefix: "rsvpmanager"	
});

$.widget("ui.videomanager", {
   options: {
		prefix : "wordtour-video",
		height: false,
		value : [],
		dialogHandler : false
   },
   addItem : function(videoId,type,attachmentId){
	   var type = type || "YouTube";
	   var target = this.element.find("ul");
	   var params = "attachment_type_id-"+videoId+":attachment_info-YouTube";
	   if(attachmentId) params+=":attachment_id-"+attachmentId;
	   var markup = ["<li class='ui-widget-content ui-corner-tr' name='"+params+"'>",
	                 	"<h5 class='ui-widget-header youtube'>YouTube</h5>",
	                 	"<div>",
	                 		"<img src='http://img.youtube.com/vi/"+videoId+"/2.jpg'>",
	                 	"</div>",
	                 	"<a class='ui-icon ui-icon-video' title='Watch this video' href='action-play:"+params+"'>Watch Video</a>",
	                 	"<a class='ui-icon ui-icon-trash' title='Delete this video' href='action-delete:"+params+"'>Delete Video</a>",
	                  "</li>"].join("");
	   target.append(markup);
   },
   empty: function(){
	   this.element.find("ul").empty();
   },
   value : function(value) {
	   var that = this;
	   // set value
	   if($.isArray(value)) {
		   this.empty();
		   $.each(value,function(){
			  that.addItem(this.attachment_type_id,this.attachment_info,this.attachment_id); 
		   });
	   }
	   // get value
	   if(!value) {
		   var value = [];
		   this.element.find("ul li").each(function(){
			   var params = getDataFromStr($(this).attr("name"));
			   value.push(params); 
		   });
		   return value;
	   }
   },
   dialog: function(){
	   var that=this,prefix = this.options.prefix;
	   var dialogId = prefix+"-dialog";
	   if(!$("#"+dialogId).length) $("body").append("<div id='"+dialogId+"' style='display:none;'><div></div></div>");
	   
	   $("#"+dialogId).dialog({
		   modal: true,
		   autoopen: true,
		   title   : "Add Videos",
		   open    : function(){
		   		$(this).find("div:first").videosearch({
		   			exclude : function(items){
		   				var exclude = {"YouTube":{}};
			   			items.each(function(){
			   			   var params = getDataFromStr($(this).attr("name"));
			   			   exclude[params.attachment_info][params.attachment_type_id] = true; 
			   			});
			   			return exclude;
		   			}(that.element.find("ul li:visible")),
		   			select: function(e,data){
		   				var isExist = that.element.find("ul li[name*=attachment_type_id-"+data.attachment_type_id+":attachment_info-"+data.attachment_info+"]");
		   				if(isExist.length == 0) {
		   					that.addItem(data.attachment_type_id,data.attachment_info,null)
		   				} else {
		   					isExist.show();
		   				}
		   			}
		   		});
	   	   },
	   	   close : function(){
	   		 // remove all inner elements, remove dialog wrap, create it again when prompt
	   		 $(this).dialog("destroy").remove();  
	   	   },
	   	   beforeclose : function(){
	   		   $(this).find("div:first").videosearch("destroy");
	   	   },
	   	   width : 640,
	   	   height:630,
	   	   buttons: {
	   		   "Done": function() { 
	   			   $(this).dialog("close"); 
	   		   }
	   	   }
	   });
   },
   _events : function() {
	   var prefix = this.options.prefix,that=this;
	   $(this.options.dialogHandler).click(function(){
		   try {
			   that.dialog();
			   return false;
		   } catch(e) {
			   return false;
		   }
	   });
	   
	   this.element.find("ul").click(function(e){
		   var elem = $(e.target);
		   if(elem.is("a")) {
			   var data = getDataFromStr(elem.attr("href")); 
			   switch(data.action) {
			   		case "delete":
			   			$(elem).parents("li").fadeOut("fast",function(){	
			   				$(this).remove();
			   			})
			   		break;
			   		case "play":
			   			
			   		break;
			   }
		   };
		   return false;
	   });
   },
   _markup : function(){
	 var prefix = this.options.prefix;
	 var wrapClass = prefix+"-wrap";
	 this.element.html(["<div class='"+wrapClass+"'>",
	                    	"<ul class='wordtour-media-box "+prefix+"-box ui-helper-reset ui-helper-clearfix'></ul>",
	                   "</div>",
	                  ].join(""));
	 if(this.options.height) this.element.find("."+wrapClass).css("height",this.options.height);
   },
   _create: function(options) {
	   $.extend(this.options,options);
	   this._markup();
	   this._events();
	   if($.isArray(this.options.value)) {
		   if(this.options.value.length>0) this.value(this.options.value);
	   }

   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments);
   }
 });

$.widget("ui.thumbnailmanager", {
   options: {
		prefix : "wordtour-thumbnail",
		height: false,
		value : [],
		exclude : {},
		remove : $.noop
   },
   addItem : function(id,src){
	   var prefix = this.options.prefix;
	   if(this.selectedThumbs["t"+id]) return ;
	   this.selectedThumbs["t-"+id] = true;
	   var target = this.element.find("ul");
	   var markup = ["<li class='ui-widget-content ui-corner-tr "+prefix+"-"+id+"'>",
	                 	"<h5 class='ui-widget-header'>Image</h5>",
	                 	"<div>",
	                 		"<img src='"+src+"'>",
	                 	"</div>",
	                 	"<a class='ui-icon ui-icon-trash' title='Delete this image' href='#'>Delete Image</a>",
	                  "</li></ul>"].join("");
	   target.append(markup);
   },
   removeItem : function(elem){
	   var id = this.itemId(elem);
	   if(id) {
		   elem.remove();
		   delete this.selectedThumbs["t-"+id];
		   this._trigger("remove",null,id);
	   }
   },
   empty: function(){
	   this.element.find("ul").empty();
   },
   value : function(value) {
	   var that = this;
	   // set value
	    if($.isArray(value)) {
		   this.empty();
		   $.each(value,function(){
			  that.addItem(this.id,this.url); 
		   });
	   }
	   // get value
	   if(!value) {
		   var value = [];
		   this.element.find("ul li").each(function(){
			   value.push(that.itemId($(this))); 
		   });
		   return value;
	   }
   },
   selected: function(){
	   return this.selectedThumbs;
   },
   itemId : function(elem){
	   var idRegExp = new RegExp(".("+this.options.prefix+"-)(\\d*)");
	   var match = elem.attr("class").match(idRegExp);
	   return $.isArray(match) ? match[2] : false;
   },
   _events : function() {
	   var prefix = this.options.prefix,that=this;
	   $(this.options.dialogHandler).click(function(){
		   try {
			   that.dialog();
			   return false;
		   } catch(e) {
			   return false;
		   }
	   });
	   
	   this.element.find("ul").click(function(e){
		   var elem = $(e.target);
		   if(elem.hasClass("ui-icon-trash")) {
			   $(elem).parents("li").fadeOut("fast",function(){
				   that.removeItem($(this));
			   })
		   }		   
		   return false;
	   });
   },
   _markup : function(){
	 var prefix = this.options.prefix;
	 var wrapClass = prefix+"-wrap";
	 this.element.html(["<div class='"+wrapClass+"'>",
	                    	"<ul class='wordtour-media-box "+prefix+"-box ui-helper-reset ui-helper-clearfix'></ul>",
	                   "</div>",
	                  ].join(""));
	 if(this.options.height) this.element.find("."+wrapClass).css("height",this.options.height);
   },
   _create: function(options) {
	   this.selectedThumbs = {};
	   $.extend(this.options,options);
	   this._markup();
	   this._events();
	   if($.isArray(this.options.value)) {
		   if(this.options.value.length>0) this.value(this.options.value);
	   }

   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments);
   }
 });

$.extend($.ui.thumbnailmanager,{
	eventPrefix: "thumbnailmanager"	
});

$.widget("ui.medialibrary", {
   options: {
		mediaUrl : $CONSTANT["MEDIA_LIBRARY"],
		height   : "520",
		select   : $.noop,
		// object of id's {1:true,2:true,3:true}
		exclude  : {}
   },
   _markup : function(){
	   var elem = this.element,o = this.options,that = this; 
		elem.html("<iframe style='width:100%;height:"+o.height+"px;' frameborder='0' src='"+this.options.mediaUrl+"' hspace='0' scrolling='no'></iframe>");
		elem.find("iframe").load(function(){
			var content = $(this).contents();
			content.find('html').css({margin:0,padding:0,backgroundColor:"#FFFFFF"}); 
			content.find('#media-upload-header,.ml-submit,.subsubsub').hide();
			that._manipulateContent(content);
		});
   },
   _create: function(options) {
	   $.extend(this.options,options);
	   this._markup();
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   },
   refresh : function(){
	   this._manipulateContent(this.element.find("iframe").contents());
   },
   _manipulateContent : function(content){
	    var that = this;
	    if(content) {
		   	content.find('.describe-toggle-on').each(function(){
		   		var button = $(this).next(".thumbnail-select");
		   		
		   		if(!button.is(".thumbnail-select")) {
		   			$(this).html("Select").hide().after("<a href='#' style='float:right;margin-right:20px;line-height:36px;display:block;' class='thumbnail-select'>Select</a>");
		   			button = $(this).next();
		   		}
				var name = $(this).parent().find("input[name^=attachments]").attr("name");
				var attachId = name.slice(name.indexOf("[")+1,name.indexOf("]")) ;
				if(that.options.exclude["t-"+attachId]) {
					button.hide();
					$(this).parent().css("opacity",0.5);
				} else {
					button.show();
					$(this).parent().css("opacity",1);
				};
				var imgSrc = $(this).parent().find("img").attr("src"); 
				$(this).next().unbind("click").click(function(){
					that.options.exclude[attachId] = attachId;
					$(this).hide();
					$(this).parent().animate({"opacity":0.5},"slow");
					that._trigger("select",null,{id:attachId,src:imgSrc});
				});
			});
	    }
   }
 });
		
$.extend($.ui.medialibrary,{
	eventPrefix: "medialibrary"	
});

$.widget("ui.videosearch", {
   options: {
	 prefix : "wordtour-video-search",
	 searchDefault : "",
	 exclude : {},
	 select  : $.noop
   },
   _create: function(options) {
	   var that = this;
	   var prefix = this.options.prefix;
	   // hidden field
	   $.extend(this.options,options);
	   
	   this.playerWrapMarkup = ["<div id='"+this.options.prefix+"-swf-wrap' style='width:300;height:225px;'>",
	                            	"<img src='http://img.youtube.com/vi/"+new Date()+"/0.jpg' width='300' height='225'>",
	                            "</div>"].join(""); 
	   this.element.html([
         "<div class='"+prefix+"-tab-wrap'>",
         	"<ul>",
         		"<li><a href='#"+prefix+$.ui.videosearch.TAB_YOUTUBE+"'>YouTube</a><li>",
         	"</ul>",
     		"<div id='"+prefix+$.ui.videosearch.TAB_YOUTUBE+"' class='"+prefix+"-youtube'>",
     			"<div class='"+prefix+"-filter'>",
     				"<input type='text' class='search' value='"+this.options.searchDefault+"'></input>",
     				"<select class='order-by'><option value='relevance'>Relevence</option><option value='published'>Upload Date</option><option value='viewCount'>View Count</option><option value='rating'>Rating</option></select>",
     				"<select class='time'><option value='all_time'>All Time</option><option value='today'>Today</option><option value='this_week'>This Week</option><option value='this_month'>This Month</option></select>",
     				"<a href='#' class='search button'>Search</a>",
     			"</div>",
     			"<div class='ui-corner-all results'></div>",
     			"<div class='ui-helper-clearfix navigation'>",
     				"<div class='total'>Total Results:<span></span></div>",
     				"<div class='buttons'><a href='#' class='button prev'>Prev</a> <a href='#' class='button next'>Next</a></div>",
     				"<div class='clear'></div>",
     		"</div>",
     	 "</div>"
       ].join(""));
	   // tabs
	   this.element.find("."+this.options.prefix+"-tab-wrap").tabs();
	   // search
	   this._initYouTubeSearch();
	   // focus search field
	   this.element.find("input.search").focus();
   },
   _initYouTubeSearch: function(){
	   var that = this,prefix = this.options.prefix;
	   var w = this.filterWrapper = this.element.find("#"+prefix+"-tab-youtube ."+prefix+"-filter");
	   var n = this.navigation = this.element.find("#"+prefix+"-tab-youtube .navigation").hide();
	   
	   n.find("a.next,a.prev").click(function(){
		   that.searchYouTube($(this).data("nav"));
		   return false;
	   });
	   
	   w.find("a.search").click(function(){
		   n.find("a.prev,a.next").each(function(){
			   $(this).data("nav",null);
		   });
		   that.searchYouTube({
			   "start-index":1,
			   format: 5,
			   time : w.find("select.time").val(),
			   q: $(this).parents("div").find("input.search").val(),
			   orderby : w.find("select.order-by").val()
		   });
		   return false;
	   });
	   
	   this.element.find("#"+prefix+"-tab-youtube .results a.select span").live("click",function(e){
	   	   var elem = $(e.target);
	   	   elem.parents("div.row").fadeOut('slow',function(){
				  $(this).remove();
		   });
	   	   that._trigger("select",null,getDataFromStr(elem.attr("class")));
		   return false;
		});
   },
   searchYouTube : function(params){
	   var that = this,prefix = this.options.prefix;
	   var l = $("#"+prefix+"-tab-youtube div.results").html("<div style='padding:10px;'>Searching...</div>");
	   var w = this.filterWrapper;
	   var n = this.navigation;
	   var params = $.extend({
	    		v: 2,
	    		alt : "jsonc",
	    		format: 5,
	    		category: "Music",
	    		"max-result" : 25
	    },params);
	    $.ajax({
			url: "http://gdata.youtube.com/feeds/api/videos?"+$.param(params),
			dataType: "jsonp",
			success: function(r) {
				l.html("");
				var html = [];
				if(r.data.totalItems == 0) {
					l.html("<div style='padding:10px;'>No Results</div>");
					n.hide();
					return false;
				}
				(function Navigation(){
					var totalItems = r.data.totalItems,startIndex = r.data.startIndex,itemsPerPage = r.data.itemsPerPage; 
					var isNav = totalItems>r.data.itemsPerPage;
					if(isNav) {
						var nextIndex = startIndex+(itemsPerPage);
						var prevIndex = startIndex >= itemsPerPage ? startIndex - itemsPerPage: 1; 
						n.show();
						n.find(".prev")[startIndex >= itemsPerPage ? "show":"hide"]().data("nav",$.extend({},params,{"start-index":prevIndex}));
						n.find(".next")[totalItems > nextIndex ? "show":"hide"]().data("nav",$.extend({},params,{"start-index":nextIndex}));
						n.find(".total span").html(totalItems);
					} else {
						n.hide();
					}
				})();
				
				$.each(r.data.items,function(){
					var exclude = that.options.exclude["YouTube"];
					if(exclude) if(exclude[this.id]) return;
					html.push(["<div class='row'>",
		           		"<div class='image'><img src='"+this.thumbnail.sqDefault+"'></div>",
		           		"<div class='content'>",
		           			"<b>"+this.title+"</b><br/>",
		           			this.description+"<br/>",
		           			"<div class='info'>"+this.viewCount+" views | rating "+this.rating+"</div>",
		           		"</div>",
		           		"<div class='selectbutton'>",
		           			"<a href='#' class='button select'><span class='attachment_type_id-"+this.id+":attachment_info-YouTube'>Select</span></a>",
		           		"</div>",
		           		"<div style='clear:both;'></div>",
					"</div>"].join(""));
				})
				l.html(html.join(""));
			}
		})
   },
   destroy: function() {
	   var that = this;
	   if(that.player) swfobject.removeSWF(that.player.id);
       window.onYouTubePlayerReady = null;
       this.element.find("."+this.options.prefix+"-tab-wrap").tabs("destroy").remove();
       this.element.empty();
	   $.Widget.prototype.destroy.apply(this, arguments);
   }
 });

$.extend($.ui.videosearch, {
	eventPrefix: "videosearch"	
});

$.ui.videosearch.TAB_YOUTUBE = "-tab-youtube";
$.ui.videosearch.TAB_VIMEO = "-tab-vimeo";
$.ui.videosearch.TAB_MEDIA = "-tab-media";




$.widget("ui.formpanel", {
   options: {
		collapse: true,
		buttons : false,
		page    : "event",
		expand  : $.noop,
		collapse  : $.noop,
		dragstop  : $.noop
   },
   collapse: function(){
	    var that= this,page = this.options.page;
	    this.element.find(".wordtour-panel-button button.collapse span:first").removeClass("ui-icon-triangle-1-n").removeClass("ui-icon-triangle-1-s").addClass("ui-icon-triangle-1-s");	
		this.element.find(".wordtour-panel-content:visible").slideUp("fast",function(){
			$(this).hide();
			that._trigger("collpase",null);
			setPanelsOrder(page);
		});
   },
   expand: function(){
	    var that = this,page = this.options.page;
	    this.element.find(".wordtour-panel-button button.collapse span:first").removeClass("ui-icon-triangle-1-n").removeClass("ui-icon-triangle-1-s").addClass("ui-icon-triangle-1-n");
		this.element.find(".wordtour-panel-content:hidden").slideDown("fast",function(){
			$(this).show();
			that._trigger("expand",null);
			setPanelsOrder(page);
		});
   },
   _create: function(options) {
	   var that = this;
	   $.extend(this.options,options);
	   if(this.options.collapse) {
			this.element.find(".wordtour-panel-button").prepend("<button class='collapse'>Collapse</button>").find("button.collapse").button({
				icons: {
					primary: !that.isCollapse() ? 'ui-icon-triangle-1-n' : 'ui-icon-triangle-1-s'
				},
				text   : false
			}).bind("click",function(){
				var collapse = $(this).find(".ui-icon-triangle-1-n").length > 0 ? true : false;
				if(collapse) {
					that.collapse();		
				} else {
					that.expand();
				}
				return false;
			});
	   }

	   if(this.options.buttons) {
		   $.each(this.options.buttons,function(){
			   var options = this ;
			   that.element.find(".wordtour-panel-button").prepend("<button>"+this.title+"</button>").find("button:first").button({
				   icons: {
						primary: this.icon
				   },
				   text   : this.text
				}).click(function(e,ui){
					try{ 
						if(options.click) options.click.call(this,e,ui);
						return false;
					} catch(e) {
						return false;
					}
				});		
		   	});	
	   }
   },
   isCollapse: function(){
	   return this.element.find(".wordtour-panel-content:visible").length == 0;	
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

$.extend($.ui.formpanel, {
	eventPrefix: "formpanel"	
});

$.widget("ui.wordtourlist", {
   options: {
	   	edit      : $.noop,
	   	quickedit : $.noop,
	   	remove    : $.noop,
	   	alert     : ".wordtour-alert",
	   	deleteAll : "#wordtour-button-delete"
   },
   _initEvents: function(){
	   var that = this;
		this.element.click(function(e){
			var elem = $(e.target);
			if(elem.is("a")){
				
				if(elem.parent().hasClass("edit")) {
					that._trigger("edit",null,e.target);
					return false;					
				}
				if(elem.parent().hasClass("delete")) {
					that.removeRow(getDataFromStr(elem.attr("class")),elem);
					that._trigger("remove",null,e);
					return false;
				}
				if(elem.parent().hasClass("quickedit")) {
					that._trigger("quickedit",null,e.target);	
					return false;
				}

				var triggerName = elem.parent().attr("class");
				if(that.options[triggerName]) {
					that._trigger(triggerName,null,e.target);	
					return false;
				}
			}
			
			if(elem.is("input:checkbox")) {
				that._checkDeleteAll();
			}
		});
		this._checkDeleteAll();
   },
   _checkDeleteAll: function(){
	   var delButton = $(this.options.deleteAll);
	   if(delButton.length>0) {
			var checked = this.element.find("input:checked").length;
			delButton.toolbarbutton(checked>0 ? "enable":"disable")
			delButton.find(".update:first").css("visibility",checked>0 ? "visible":"hidden");
			delButton.find(".count").text(checked);
	   }
   },
   deleteAll: function(data,text){
	   var that = this;
	   var data = $.extend(data,{
		   id: JSON.stringify($.map(this.element.find("input:checked"),function(c){
			   return c.value;
		   }))
	   }); 
	   
	   $("body").append(["<div id='events-confirm-delete'>",
	                  		"<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>",
	                  		"<span style='font-size:10px;'>"+text+" will be permanently deleted and cannot be recovered. Are you sure?</span>",
	                  		"</p>",
	                  "</div>"].join(""));
		$("#events-confirm-delete").dialog({
			resizable: false,
			height:160,
			modal: true,
			buttons: {
				"Delete": function() {
					$(this).dialog('close');
					that.ajax({data:data},function(r){
					   $.each(r,function(id,data){
							if(data.type == "success") {
								that.element.find("input:checkbox[value="+id+"]").parents("tr:first").remove();
							} 
							if(data.type == "error") {
								var e = [];
								$.each(r,function(i,k){
									e.push("<li>"+i+" "+this.msg+"</li>");
								});
								that.alert("show","error","Error as occurred<ul>"+e.join("")+"</ul>");
							}
						});
					    that._checkDeleteAll();
				   });
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function(){
				$(this).remove();
			}
		});
	   
	   
   },
   replaceRowHtml: function(rowToUpdate,html){
	   var tbody = $(rowToUpdate).parent();
		var rowIndex = $(rowToUpdate).attr("rowIndex")-1  ;
		$(rowToUpdate).replaceWith(html);
		tbody.find("tr:eq("+rowIndex+")").effect("highlight",null,600);
   },
   addRowHtml: function(html){
	   if(this.element.find("tr.empty").length>0) this.element.find("tr.empty").remove();
	   this.element.find("tbody").prepend(html).find("tr:first").hide().effect("highlight",null,600);
   },
   showOverlay : function() {
		$.blockUI.defaults.fadeOut = 0; 
		$.blockUI.defaults.fadeIn = 0; 
		$.blockUI({ message: 'Processing...',css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .5, 
            color: '#fff'
        }
		});
   },
   alert: function(state,mode,msg) {
		var elem = $(this.options.alert);
		elem[state]();
		if(state=="show") {
			if(msg) elem.html(msg);
			elem.attr("class","wordtour-alert");
			elem.addClass("wordtour-alert-"+mode);
		}
		
	},
   ajax: function(options,success,error){
	   var that = this;
	   var o = $.extend({
	   				url: $CONSTANT["PLUGIN_AJAX"],
				  	type    : "post",
				  	cache      : false,
				  	dataType: "json"},options);
	   
		$.extend(o,{
			beforeSend : function(){
				that.alert("hide");
				that.showOverlay();
			},
			success: function(r){
				var type = r.type;
				if(type) {
					if(type=="success") {
						if(success) success.call(that,r);
						return;
					}; 
					if(type=="error") {
						that.alert("show","error",r.msg);
						if(error) error.call(that,r);
						return;
					}
				} else {
					if(success) success.call(that,r);
				}
										
  	 		},
  	 		complete: function(){
  	 			$.unblockUI();
  	 		},
  	 		error: function(){
  	 			that.alert("show","error","Error as Occured, Please Try Again");
  	 		}
		});
	   $.ajax(o);
   },
   removeRow   : function(data,elem,success){
	  this.ajax({
		  data: data || getDataFromStr($(elem).attr("class"))
	  },function(r){
		  	$(elem).parents("tr:first").effect("highlight",{color:"#FFEBE8",mode:"hide"},400,function(){
				$(this).remove();
			});
			if(success) success.call(this,r);
	  });			
   },
   updateRow   : function(data,elem,success){
	  this.ajax({
		  data: data || getDataFromStr($(elem).attr("class"))
	  },function(r){
		 	this.element.find("tbody tr.empty").remove();
			this.replaceRowHtml($(elem).parents("tr:first"),r.html);
			if(success) success.call(this,r);
	  });						
   },
   _create: function(options) {
	   var that = this;
	   $.extend(this.options,options);
	   this._initEvents();
	  
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

$.extend($.ui.wordtourlist, {
	eventPrefix: "wordtourlist"	
});


$.widget("ui.googlemap", {
   options: {
		height: 200
   },
   map   : null,
   geocoder : null,
   addressMarker : null,
   value : function(address,countryCode) {
	   var elem = this.element, that = this ;
	   if(address) {
		   var that = this ;
		   this.element.data("value",[address,countryCode])
			var geocoder = this.geocoder;
			var map = this.map;
			var addressMarker = this.addressMarker || null;
			if(geocoder) {
		        geocoder.setBaseCountryCode(countryCode);
		        geocoder.getLatLng(address,
		          function(point) { 
			        if (!point) {
		            	//errorHandler(address);
		            } else {
			          if(that.addressMarker) map.removeOverlay(that.addressMarker);
			          that.addressMarker = addressMarker = new GMarker(point);
		              map.checkResize();
		              map.setCenter(point,14);
		              map.addOverlay(addressMarker);	
		              addressMarker.openInfoWindowHtml(address,{maxWidth:50});
		            }
		          }
		        );
			}
	   } 
   },
   resize : function(){
	   var data = this.element.data("value");
	   this.value(data[0],data[1]);
   },
   _create: function(options) {
	   $.extend(this.options,options);
	    try{
		    if (GBrowserIsCompatible()) {
		    	this.element.css("height",this.options.height);
		    	var map = this.map = new GMap2(this.element[0]);
				map.addControl(new GLargeMapControl3D());
				this.geocoder = new GClientGeocoder();
				//map.setUIToDefault(); 
				//map.removeMapType(G_NORMAL_MAP);
		        //map.addControl(new GMapTypeControl());
		        //map.setMapType(G_NORMAL_MAP);
		    } else {
		    	this.element.html("Google Map Key is not exist. Need to register to google map service.");
		    }
	    } catch(e) {
	    	this.element.html("Google Map Key is not exist. Need to register to google map service.");
	    }
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

$.extend($.ui.googlemap, {
	eventPrefix: "googlemap"	
});
});


	




 