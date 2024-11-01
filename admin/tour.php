<?php
	$action_new = WT_ADMIN_URL."page=wt_tour&action=new";
	$back_to_url = WT_ADMIN_URL."page=wt_tour";
	$action_header = $action === "edit" ? "Edit" : "New";
?>

<div class="wrap">	
	<h2><?php echo $action_header ?> Tour
		<a class="button button-h2" title="Back to Events" href="<?php echo $back_to_url;?>">< Back to Tours</a>
		<?php if($action=="edit") { ?>
		<a class="button button-h2" title="Add New Event" href="<?php echo $action_new;?>">New Tour</a>
		<?php }?>
	</h2>
</div>


<div class="wordtour-column-wrap wordtour-column-tour">

	<div class="wordtour-alert"></div>
	<div class="wordtour-toolbar ui-helper-clearfix">
		<div title="Save Tour" id="wordtour-button-save">
			<span class="update"><br/>Last Updated <span id="tour_publish_date"></span></span>
			<span class="update"><br/>&nbsp;</span>		
		</div>
		<div title="Set As Default" id="wordtour-button-default">
			 <span style="font-size:9px;" class="update"><br/></span>
			 <span class="update"><br/>&nbsp;</span>	
		</div>
		<div title="Remove Default" id="wordtour-button-undo-default" style='display:none;'>
			 <span class="update" style="visibility:visible;"><br/>Tour set as default</span>
			 <span class="update"><br/>&nbsp;</span>	
		</div>
	</div>
<?php 

$default_panels_order = array("left"=>array(
			array("details",1),
			array("status",1),
			array("info",1)
		),
		"right"=>array(
			array("poster",1),
			array("order",1),
			array("genre",1),
			array("gallery",0),
			array("video",0),
			array("category",0),
		)
);

$panels_order = wordtour_get_panel_state("tour");
if(!$panels_order) $panels_order = $default_panels_order; 

?>
<form id="tour-form">
	<input type="hidden" name="tour_id" value="<?php echo $_GET["tour_id"]?>"></input>
	<input type="hidden" name="_tour_nonce" value=""></input>
	<div class="wordtour-column wordtour-column-left">
		<?php 
			foreach($panels_order["left"] as $panel) {
				call_user_func("wordtour_tour_".$panel[0]."_panel",$panel[1]);
			}
		?>
	</div>
	<div class="wordtour-column wordtour-column-right" style="">
		<?php 
			foreach($panels_order["right"] as $panel) {
					call_user_func("wordtour_tour_".$panel[0]."_panel",$panel[1]);
			}
		?>	
	</div>
	<div style="clear:both;"></div>
</form>
</div>

<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/tour");
?>
