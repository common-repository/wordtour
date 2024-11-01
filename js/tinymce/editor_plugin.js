
(function() {
	tinymce.create('tinymce.plugins.wordtour', {
		init : function(ed, url) {
			ed.addCommand('mceWordTour', function() {
				ed.windowManager.open({
					file : url + '/shortcode_dialog.php',
					width : 700,
					height : 440,
					inline : 1
				}, {
					plugin_url : url
				});
			});
			ed.addButton('wordtour', {
				title : 'WordTour Shortcode Generator',
				cmd   : 'mceWordTour',
				image : url + '/wordtour.png'
			});
		},
		getInfo : function() {
			return {
				longname  : 'WordTour Shortcode Generator',
				author    : 'WordTour',
				authorurl : 'http://www.wordtour.com',
				infourl   : '',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add('wordtour', tinymce.plugins.wordtour);
})();
