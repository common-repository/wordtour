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
						} catch(e){
							$(targetSelector).hide();
						}
						return this;
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
				imageBlank:	  $CONSTANT.THEME+"library/lightbox/lightbox-blank.gif"
			});
		})();
		(function Video() {
			$("#ytvideos").videoBox({
				imageBtnClose:$CONSTANT.THEME+"library/lightbox/lightbox-btn-close.gif"
			});
		})();
	});
});
