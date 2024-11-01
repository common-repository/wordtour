<script>
jQuery(function($){
	$(".wordtour-column,").sortable({
		connectWith: '.wordtour-column',
		handle     : '.wordtour-panel-hndl-header',
		stop     : function(){
			setPanelsOrder("settings");	
		}	
	});
	
	$(".wordtour-panel").formpanel({page:"settings"});
	$("#wordtour-button-settings").toolbarbutton({
		click: function(){
			$("form").submit();
		},
		cls : "save"
	});

	$("#eventbrite-import-button").button({
		
	}).click(function(){
		openImportEventbriteDialog();
		return false;
	});

	$("#settings-upgrade-button").click(function(){
		$.ajax({
			type     : "post",
			dataType :"html",
			url      : $CONSTANT["PLUGIN_AJAX"],
			data     : {
				action: "upgrade"
			}
		});
		return false;
		
	});

	function saveThemePath(){
		var themePath = $("#theme_path");
		var button = $("#theme_path_button");
		var error = $("#theme_path_error");
		button.val("Loading...");
		$.ajax({
			type     : "post",
			dataType :"json",
			url      : $CONSTANT["PLUGIN_AJAX"],
			data     : {
				action: "theme_path",
				path  : themePath.val()
			},	
			complete: function(){
				$("#wordtour-button-settings").toolbarbutton("enable");
				button.val("Edit");
				themePath.attr("readonly","true");
			},					
			success: function(r){
				if(r.success) {
					themePath.val(r.path);
					$("#theme_default_wrap").html(r.themes);	
				}

				if(r.error) {
					error.show().html(r.msg);
					themePath.val(themePath.data("origValue"));
				}
			},
			error: function(){
				error.show().html("Error updating Theme Path");
			}
		})
	}
	
	$("#theme_path").blur(function(){
		if(!$(this).attr("readonly")) saveThemePath();
	});
	
	$("#theme_path_button").click(function(e){
		var button = $(this);
		$("#theme_path_error").hide();
		if(button.val()=="Edit") {
			$("#wordtour-button-settings").toolbarbutton("disable");
			$(this).val("Done");
			$("#theme_path").removeAttr("readonly").focus().data("origValue",$("#theme_path").val());
		} else {
			//saveThemePath();
		}
	});	
		
});
</script>