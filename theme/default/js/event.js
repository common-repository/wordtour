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
				imageBlank:	  $CONSTANT.THEME+"library/lightbox/lightbox-blank.gif"
			});
		})();
		(function Video() {
			$("#ytvideos").videoBox({
				imageBtnClose:$CONSTANT.THEME+"library/lightbox/lightbox-btn-close.gif"
			});
		})();
		// SHARE LINKS
		(function ShareLinks(){
			var target = $(".share:first");
			var url = escape(target.find("input[name=url]").val()),title = target.find("input[name=title]").val();
			var f = "http://www.facebook.com/sharer.php?u="+url+"&t="+title,
				t = "http://twitter.com/home?status="+url,
				d = "http://delicious.com/save?v=5&noui&jump=close&url="+url+"&title="+title,
				m = "http://www.myspace.com/Modules/PostTo/Pages/?u="+url,
				dg = "http://digg.com/submit?url="+url;
			
			target.html([
              	"<div class='wt-share-links'>",
              		"<div class='wt-share-link facebook' title='Send to Facebook'><input type='hidden' value='"+f+"'/></div>",
              		"<div class='wt-share-link digg' title='Digg'><input type='hidden' value='"+dg+"'/></div>",
              		"<div class='wt-share-link delicious' title='Send to Delicious'><input type='hidden' value='"+d+"'/></div>",
              		"<div class='wt-share-link myspace' title='Send to MySpace'><input type='hidden' value='"+m+"'/></div>",
              		"<div class='wt-share-link twitter' title='Tweet This'><input type='hidden' value='"+t+"'/></div>",
              		"<div class='wt-share-link-like '>",
              			"<iframe src='http://www.facebook.com/plugins/like.php?href="+url+"%2Fpage%2Fto%2Flike&amp;layout=button_count&amp;show_faces=false&amp;width=100&amp;action=like&amp;colorscheme=light&amp;height=35' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:100px; height:35px;' allowTransparency='true'></iframe>",
            		"</div>",
              		"<div style='clear:both;'></div>",
              	"</div>"
			].join(""));
			
			target.find(".wt-share-links").click(function(event){
				var elem = $(event.target);
				var url = elem.find("input").val();
				window.open(url,"sharer","toolbar=0,status=0,width=626,height=436");
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
			var attach_event = function() {
				$("#rsvp-block .not-attending").click(function(){
					attend("not-attend");
					return false;
				});
			
				$("#rsvp-block .attending").click(function(){
					attend("attend");
					return false;
				});
				return arguments.callee;
			}();
				
			function attend(action) {
				var panel = $("#rsvp-block");
				if(panel.length > 0) {
					$.ajax({
						type: 'POST',
						url     : $CONSTANT.PLUGIN_AJAX,
						data    : "action="+action+"&"+$("#event-form").serialize(),
						success : function(r){
							panel.html(r);
							var totalRSVP = parseInt($("#wt-total-rsvp").html())+(action=="attend"? (1) : (-1)) ;  
							$("#wt-total-rsvp").html(totalRSVP);
							attach_event();	
							try{
								if(Cufon) Cufon.replace("h2");
							} catch(e){};
						},
						failure : "",
						dataType: "html"
					});	
				}
			};	
		})();
	});
	
	
	
});
