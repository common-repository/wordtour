<?php 
require(ABSPATH . 'wp-admin/options-head.php');
global $wpdb, $_wt_options,$gpo;
$options = $_wt_options->options();

?>

<style>
	table td {
		padding: 5px;
	}
	
	p.help {
		color : #666666;
		font-family : "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
		font-style  : italic;
		font-size   : 10px;
	}
	
	.ui-widget {
		font-size: 11px;
	}
	
	td.h2 {
		font-weight:bold;
		font-size:14px;
		padding-left:55px;
		height: 55px;
		background-repeat: no-repeat;
	}
	
	td.facebook {
		background-image: url(<?php echo WT_PLUGIN_URL."/images/facebook.png"?>);
	}
	
	td.twitter {
		background-image: url(<?php echo WT_PLUGIN_URL."/images/twitter.png"?>);
	}
	
	td.lastfm {
		background-image: url(<?php echo WT_PLUGIN_URL."/images/lastfm.png"?>);
	}
	
	td.google {
		background-image: url(<?php echo WT_PLUGIN_URL."/images/google.png"?>);
	}
	
	td.flickr {
		background-image: url(<?php echo WT_PLUGIN_URL."/images/flickr.png"?>);
	}
</style>
<?php 

$default_panels_order = array("left"=>array(
			array("general",1),
			array("discussion",1),
			array("event",1),
			array("permalinks",1),
			array("system",1)
		),
		"right"=>array(
			array("eventbrite",0),
			array("google",0),
			array("facebook",0),
			array("twitter",0),
			array("lastfm",0),
			array("flickr",0)
		)
);

$panels_order = wordtour_get_panel_state("settings");
if(!$panels_order) $panels_order = $default_panels_order; 
?>
<div class="wrap">
	<h2>Settings</h2>
</div>

<div class="wordtour-column-wrap wordtour-column-event">
	<form action="options.php" method="post">
	
		<div class="wordtour-toolbar" style="overflow:hidden;">
			<div class="ui-helper-clearfix" style="width:2000px;">
				<div title="Save Settings" id="wordtour-button-settings"></div>	
			</div>
		</div>
		<?php settings_fields('wordtour_settings_prefix'); ?>	
		<input type="hidden" name="wordtour_settings[default_artist]" value="<?php echo $options["default_artist"]?>"></input>
		<input type="hidden" name="wordtour_settings[default_tour]" value="<?php echo $options["default_tour"]?>"></input>
		<input type="hidden" name="wordtour_settings[default_venue]" value="<?php echo $options["default_venue"]?>"></input>
		
		
		<div class="ui-helper-clearfix">
			<div class="wordtour-column wordtour-column-left">
				<?php 
					foreach($panels_order["left"] as $panel) {
						call_user_func("wordtour_settings_".$panel[0]."_panel",$panel[1]);
					}
				?>
			</div>
			<div class="wordtour-column wordtour-column-right">
				<?php 	
				foreach($panels_order["right"] as $panel) {
					call_user_func("wordtour_settings_".$panel[0]."_panel",$panel[1]);
				}	
				?>
			</div>
		</div>
	</form>
</div>

<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/settings");
?>



