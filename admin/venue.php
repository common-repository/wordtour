<?php
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));

	
$action_new = WT_ADMIN_URL."page=wt_venues&action=new";
$back_to_url = WT_ADMIN_URL."page=wt_venues";
$action_header = $action === "edit" ? "Edit" : "New";

?>

<div class="wrap">	
	<h2><?php echo $action_header ?> Venues
		<a class="button button-h2" title="Back to Venues" href="<?php echo $back_to_url;?>">< Back to Venues</a>
		<?php if($action=="edit") { ?>
		<a class="button button-h2" title="Add New Venue" href="<?php echo $action_new;?>">New Venue</a>
		<?php }?>
	</h2>
</div>


<div class="wordtour-column-wrap wordtour-column-venue">

	<div class="wordtour-alert"></div>
	<div class="wordtour-toolbar ui-helper-clearfix">
		<div title="Save Venue" id="wordtour-button-save"></div>
		<div title="Set As Default" id="wordtour-button-default"></div>
		<div title="Remove Default" id="wordtour-button-undo-default" style='display:none;'></div>
	</div>
<?php 

$default_panels_order = array("left"=>array(
			array("details",1),
			array("status",1),
			array("map",1),
			array("info",1),
			array("more",1)
		),
		"right"=>array(
			array("poster",1),
			array("order",1),
			array("gallery",1),
			array("video",1),
			array("category",1),
		)
);

$panels_order = wordtour_get_panel_state("venue");
if(!$panels_order) $panels_order = $default_panels_order;
?>
<form id="venue-form">
	<input type="hidden" name="venue_id" value="<?php echo $_GET["venue_id"]?>"></input>
	<input type="hidden" name="_venue_nonce" value=""></input>
	<div class="ui-helper-clearfix">
		<div class="wordtour-column wordtour-column-left">
			<?php 
				foreach($panels_order["left"] as $panel) {
					call_user_func("wordtour_venue_".$panel[0]."_panel",$panel[1]);
				}
			?>
		</div>
		<div class="wordtour-column wordtour-column-right">
			<?php 
				foreach($panels_order["right"] as $panel) {
						call_user_func("wordtour_venue_".$panel[0]."_panel",$panel[1]);
				}
			?>	
		</div>
	</div>
</form>
</div>
<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/venue");
?>
