
jQuery(function($) {
	requireCSS($CONSTANT["THEME"]+"library/lightbox/jquery.lightbox-0.5.css",function(){});
	requireJS({
		"jquerytools"   : $CONSTANT.THEME_PATH+"library/jquery-tools/jquery.tools.min.js",
		"lightbox"      : $CONSTANT.THEME_PATH+"library/lightbox/jquery.lightbox-0.5.min.js",
		"videobox"      : $CONSTANT.THEME_PATH+"library/lightbox/video.lightbox.js"
	},function(){
		// VIDEO
		(function Video() {
			$("#ytvideos").videoBox({
				imageBtnClose:$CONSTANT.THEME+"library/lightbox/lightbox-btn-close.gif"
			});
		})();
	});
});
