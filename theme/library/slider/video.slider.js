;if(window.jQuery) {
	(function($) {
		$.fn.videoSlider = function(options) {
			var settings = jQuery.extend({
				id      : "videos-slider-player",
				overlay : "",
				player  : null
			}, options);
			var elem = $(this);
			
			
			elem.append("<div id='wt-video-overlay' class='wt-video-overlay'></div>");
			$(settings.overlay).prepend([
		         "<div id='"+settings.id+"-wrap'></div>",
		         "<div class='footer'>",
					"<div class='title' style='float:left'></div>",
		         	"<div style='float:right'><a href='#' class='close'>Close</a> or Esc Key</div>",
		         	"<div class='clear:both;'></div>",
		         "</div>"
		    ].join(""));
			
			var images = elem.find("img") ;
			if(images.length>0) {
				elem.scrollable().find("img").overlay({
					onBeforeLoad : function(e){
						var img = e.currentTarget.getTrigger();
						var overlay = e.currentTarget.getOverlay();
						var videoId = img.attr("class");
						overlay.css({width:500});
						window.onYouTubePlayerReady = function(playerId) {
							settings.player = document.getElementById(settings.id);
							//settings.player.playVideo();
						}
						swfobject.embedSWF("http://www.youtube.com/v/"+videoId+"?enablejsapi=1&playerapiid=ytplayer", 
							settings.id+"-wrap", "500","375","8", null, null, {allowScriptAccess: "always"},{id: settings.id});
						 $.ajax({
								url: "http://gdata.youtube.com/feeds/api/videos?v=2&alt=jsonc&q="+videoId,
								dataType: "jsonp",
								success: function(r) {
							 		if(r.data) {
							 			var item = r.data.items[0];
							 			overlay.find(".title").html(item.title);
							 		}
						 		}
						 });
					},
					onClose : function(){
						swfobject.removeSWF(settings.id);
						$(settings.overlay).prepend("<div id='"+settings.id+"-wrap'></div>");
					},
					target : settings.overlay,
					mask : {
						color:"#000000",
						onBeforeClose : function(){
							
						}
					},
					effect : "default"
				});
				

			}
		};
	})(window.jQuery);
};