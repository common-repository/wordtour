jQuery(function($){
	requireCSS($CONSTANT["THEME"]+"library/star-rating/jquery.rating.css",function(){});
	requireCSS($CONSTANT["THEME"]+"library/lightbox/jquery.lightbox-0.5.css",function(){});
	requireJS({
		"jquerytools"   : $CONSTANT.THEME_PATH+"library/jquery-tools/jquery.tools.min.js",
		"lightbox"      : $CONSTANT.THEME_PATH+"library/lightbox/jquery.lightbox-0.5.min.js",
		"videobox" : $CONSTANT.THEME_PATH+"library/lightbox/video.lightbox.js"
	},function(){
		// MAP
		(function GoogleMap(targetSelector){
			var targetSelector = "#directions-wrap";
			var googleMap = function(){
				var map,geocoder,addressMarker,is_render = false;
				return {
					isRender : function(){
						return is_render ;
					},
					render : function() {
						try {
							if(GBrowserIsCompatible()) {
								map = new GMap2($(targetSelector)[0]);
								map.addControl(new GLargeMapControl3D());
							    geocoder = new GClientGeocoder();
							};
							return this;
						} catch(e){
							$(targetSelector).hide();
							return this;
						}
					},
					show : function(address,countryCode,errorHandler) {
						if(!is_render) this.render();
						var addressMarker = addressMarker || null;
						if(geocoder) {
							geocoder.setBaseCountryCode($("#google-map-country").val());
							geocoder.getLatLng($("#google-map-address").val(),function(point){
						        if(!point) {
					           		errorHandler(address);
					            } else {
						          if(addressMarker) map.removeOverlay(addressMarker);
						          addressMarker = new GMarker(point);
					              map.checkResize();
					              map.setCenter(point,14);
					              map.addOverlay(addressMarker);	
					              //addressMarker.openInfoWindowHtml(address,{maxWidth:50});
					            }
					        });
					     };
					}			
				}
			}();
			
			googleMap.render().show();
		})();
		// GALLERY
		(function Gallery() {
			$("#thumbnails a").lightBox({
				imageLoading: $CONSTANT.THEME+"library/lightbox/lightbox-ico-loading.gif",
				imageBtnPrev: $CONSTANT.THEME+"library/lightbox/lightbox-btn-prev.gif",
				imageBtnNext: $CONSTANT.THEME+"library/lightbox/lightbox-btn-next.gif",
				imageBtnClose:$CONSTANT.THEME+"library/lightbox/lightbox-btn-close.gif",
				imageBlank   :$CONSTANT.THEME+"library/lightbox/lightbox-blank.gif"
			});
		})();
		(function Video() {
			$("#ytvideos").videoBox({
				imageBtnClose:$CONSTANT.THEME+"library/lightbox/lightbox-btn-close.gif"
			});
		})();
		//COMMENTS
		(function Comments(){
			$("#submit-comment").click(function(){
				var f = $(this).parents("form:first");
				$.ajax({
					type: 'POST',
					url     : $CONSTANT.PLUGIN_AJAX,
					data    : "action=insert_comment"+"&"+f.serialize(),
					success : function(r){
						if(r!="") {
							$("#the-comment-list").append(r);
							var totalComments = parseInt($("#wt-total-comments").html())+1 ;  
							$("#wt-total-comments").html(totalComments);
							$("#comment-form")[0].reset();
						} else {
							alert("Comment fields are missing or invalid");
						}
					},
					failure : "",
					dataType: "html"
				});
				return false;
			});
		})();
		// RSVP
		(function RSVP(){
			var widget = $(".wt-rsvp-widget");
			var attach_event = function() {
				$(".not-attending",widget).click(function(){
					attend("not-attend");
					return false;
				});
			
				$(".attending",widget).click(function(){
					attend("attend");
					return false;
				});
				return arguments.callee;
			}();
				
			function attend(action) {
				if(widget.length > 0) {
					$.ajax({
						type: 'POST',
						url     : $CONSTANT.PLUGIN_AJAX,
						data    : "action="+action+"&"+$("#event-form").serialize(),
						success : function(r){
							widget.html(r);
							var totalRSVP = parseInt($("#wt-total-rsvp").html())+(action=="attend"? (1) : (-1)) ;  
							$("#wt-total-rsvp").html(totalRSVP);
							attach_event();	
						},
						failure : "",
						dataType: "html"
					});	
				}
			};	
		})();
		
		// load addthi widget, when loaded through script it cause a bug
		jQuery.ajax({
			url  : "http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4cd07dc85452c5ad",
			dataType: 'jsonp'
		});
		
	});
	
	
	
});
