<?php
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));
	
	$action_new = WT_ADMIN_URL."page=wt_tracks&action=new";
	$back_to_url = WT_ADMIN_URL."page=wt_tracks";
	$action_header = $action === "edit" ? "Edit" : "New";
?>





<div class="wrap">
	<h2><?php echo $action_header ?> Track
		<a class="button button-h2" title="Back to Tracks" href="<?php echo $back_to_url;?>">< Back to Tracks</a>
		<?php if($action=="edit") { ?>
		<a class="button button-h2" title="Add New Track" href="<?php echo $action_new;?>">New Track</a>
		<?php }?>
	</h2>
</div>


<div class="wordtour-column-wrap wordtour-column-artist">

<div class="wordtour-alert"></div>
<div class="wordtour-toolbar ui-helper-clearfix">
	<div title="Save Track" id="wordtour-button-save"></div>
</div>
<?php 

$default_panels_order = array("left"=>array(
			array("details",1),
			array("more",1),
			array("lyrics",1)
		),
		"right"=>array(
			array("poster",1),
			array("genre",1)				
		)
);

$panels_order = wordtour_get_panel_state("track");
if(!$panels_order) $panels_order = $default_panels_order;
 

?>
<form id="track-form">
	<input type="hidden" name="track_id" value="<?php echo $_GET["track_id"]?>"></input>
	<input type="hidden" name="_track_nonce" value=""></input>
	<div class="wordtour-column wordtour-column-left">
		<?php 
			foreach($panels_order["left"] as $panel) {
				call_user_func("wordtour_track_".$panel[0]."_panel",$panel[1]);
			}
		?>
	</div>
	<div class="wordtour-column wordtour-column-right" style="">
		<?php 
			foreach($panels_order["right"] as $panel) {
					call_user_func("wordtour_track_".$panel[0]."_panel",$panel[1]);
			}
		?>	
	</div>
	<div style="clear:both;"></div>
</form>
</div>

<?php wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/track");?>

