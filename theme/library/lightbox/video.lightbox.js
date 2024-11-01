;if(window.jQuery) {
	(function($) {
		$.fn.videoBox = function(options) {
			var settings = jQuery.extend({
				id      : "videos-slider-player",
				overlay : "videos-slider-player-overlay",
				imageBtnClose : "./images/lightbox-btn-close.gif",
				player  : null,				
			}, options);
			var elem = $(this);
			
			
			elem.append("<div id='"+settings.overlay+"' class='wt-video-overlay'></div>");
			
			$("#"+settings.overlay).prepend([
		         "<div id='"+settings.id+"-wrap'></div>",
		         "<div class='footer'>",
					"<div class='title'></div>",
		         	"<div class='close' style='background-image:url("+settings.imageBtnClose+");'></div>",
		         	"<div class='clear:both;'></div>",
		         "</div>"
		    ].join(""));
			
			elem.find("img").overlay({
				onBeforeLoad : function(e){
					var href = e.currentTarget.getTrigger();
					var overlay = e.currentTarget.getOverlay();
					var videoId = href.parent().attr("href");
					overlay.css({width:500});
					window.onYouTubePlayerReady = function(playerId) {
						settings.player = document.getElementById(settings.id);
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
				onBeforeClose : function() {
					swfobject.removeSWF(settings.id);
					$("#"+settings.overlay).prepend("<div id='"+settings.id+"-wrap'></div>");
					window.onYouTubePlayerReady = null;
				},
				onClose : function(){
					console.log("sdsdsdsd")
				},
				target : "#"+settings.overlay,
				mask : {
					color:"#000000"
				},
				effect : "default"
			});			
		};
	})(window.jQuery);
};