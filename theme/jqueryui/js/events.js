jQuery(window).load(function(){
	jQuery(function($) {
		
		requireJS({
			"jquerytools" : $CONSTANT.THEME_PATH+"library/jquery-tools/jquery.tools.min.js"
		},function(){
			// VENUE OVERLAY
			(function VenueOverlay(){
				$("body").append(["<div id='wt-venue-overlay' class='venue-overlay'>",
				                	"<div id='wt-venue-header'></div>",
				                	"<div id='wt-venue-map'></div>",
				                	"<div id='wt-venue-details'></div>",
				                  "</div>"].join(""));
				
				$("a[rel=#wt-venue-overlay]").each(function(i) {
					$(this).overlay({ 
						closeOnClick: false,
						effect: 'apple',
						mask : {
							color:"#000000"
						},
						onBeforeLoad : function(){
							var elem = this.getTrigger();
							$("#wt-venue-header").html(elem.parent().next().val());
							$("#wt-venue-details").html(elem.parent().next().next().next().next().val());
						},
						onLoad: function() {
							var elem = this.getTrigger();
							var countryCode = elem.parent().next().next().val();
							var address = elem.parent().next().next().next().val();
							googleMap.show(address,countryCode,function(){});  
						}	
					});
				});
				
				var googleMap = function(){
					var map,geocoder,addressMarker,is_render = false;
					return {
						isRender : function(){
							return is_render ;
						},
						render : function() {
							try {
							if(GBrowserIsCompatible()) {
								map = new GMap2($("#wt-venue-map")[0]);
								map.addControl(new GLargeMapControl3D());
							    geocoder = new GClientGeocoder();
							};
							} catch(e){
							}
						},
						show : function(address,countryCode,errorHandler) {
							if(!is_render) this.render();
							var addressMarker = addressMarker || null;
							if(geocoder) {
								geocoder.setBaseCountryCode(countryCode);
								geocoder.getLatLng(address,function(point){
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
			})();
			

			
		});
		 
	});
});
	