
jQuery(function($) {
	requireCSS($CONSTANT["THEME"]+"library/lightbox/jquery.lightbox-0.5.css",function(){});
	requireJS({
		"jquerytools"   : $CONSTANT.THEME_PATH+"library/jquery-tools/jquery.tools.min.js"
	},function(){
		// LYRICS
		(function lyrics() {
			$("body").append(["<div id='wt-lyrics-overlay' class='lyrics-overlay'>",
			                	"<h2 id='wt-lyrics-header'></h2>",
			                	"<div id='wt-lyrics-text'></div>",
			                  "</div>"].join(""));
			
			$("a[rel=#wt-lyrics-overlay]").each(function(i) {
				$(this).overlay({ 
					closeOnClick: false,
					effect: 'apple',
					mask : {
						color:"#000000"
					},
					onBeforeLoad : function(){
						var elem = this.getTrigger();
						$("#wt-lyrics-header").html(elem.next().val());
						$("#wt-lyrics-text").html(elem.next().next().val());
					},
					onLoad: function() {  
					}	
				});
			});
		})();
	});
});


//Attach open overlay event to each venue link
