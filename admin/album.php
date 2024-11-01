<?php
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));
	
	$action_new = WT_ADMIN_URL."page=wt_albums&action=new";
	$back_to_url = WT_ADMIN_URL."page=wt_albums";
	$action_header = $action === "edit" ? "Edit" : "New";
?>




<div class="wrap">
	<h2><?php echo $action_header ?> Album
		<a class="button button-h2" title="Back to Albums" href="<?php echo $back_to_url;?>">< Back to Albums</a>
		<?php if($action=="edit") { ?>
		<a class="button button-h2" title="Add New Album" href="<?php echo $action_new;?>">New Album</a>
		<?php }?>
	</h2>
</div>


<div class="wordtour-column-wrap wordtour-column-album">

	<div class="wordtour-alert"></div>
	<div class="wordtour-toolbar ui-helper-clearfix">
		<div title="Save Album" id="wordtour-button-save"></div>
		<div title="Import Album Info" id="wordtour-button-import">
			<span class="update"><br/>&nbsp;</span>	
			<span class="update"><br/>&nbsp;</span>		
		</div>
	</div>
<?php 

$default_panels_order = array("left"=>array(
			array("details",1),
			array("status",1),
			array("more",1),
			array("buy",1)
		),
		"right"=>array(
			array("poster",1),
			array("order",1),
			array("genre",1),	
			array("tracks",1)			
		)
);

$panels_order = wordtour_get_panel_state("album");
if(!$panels_order) $panels_order = $default_panels_order;
 

?>
	<form id="album-form">
		<input type="hidden" name="album_id" value="<?php echo $_GET["album_id"]?>"></input>
		<input type="hidden" name="_album_nonce" value=""></input>
		<div class="ui-helper-clearfix">
			<div class="wordtour-column wordtour-column-left">
				<?php 
					foreach($panels_order["left"] as $panel) {
						call_user_func("wordtour_album_".$panel[0]."_panel",$panel[1]);
					}
				?>
			</div>
			<div class="wordtour-column wordtour-column-right" style="">
				<?php 
					foreach($panels_order["right"] as $panel) {
							call_user_func("wordtour_album_".$panel[0]."_panel",$panel[1]);
					}
				?>	
			</div>
		</div>
	</form>
</div>

<?php wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/album");?>

