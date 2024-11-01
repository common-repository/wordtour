<?php
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));
	
	$action_new = WT_ADMIN_URL."page=wt_artists&action=new";
	$back_to_url = WT_ADMIN_URL."page=wt_artists";
	$action_header = $action === "edit" ? "Edit" : "New";
?>





<div class="wrap">
	<h2><?php echo $action_header ?> Artist
		<a class="button button-h2" title="Back to Artists" href="<?php echo $back_to_url;?>">< Back to Artists</a>
		<?php if($action=="edit") { ?>
		<a class="button button-h2" title="Add New Artist" href="<?php echo $action_new;?>">New Artist</a>
		<?php }?>
	</h2>
</div>


<div class="wordtour-column-wrap wordtour-column-artist">

<div class="wordtour-alert"></div>
<div class="wordtour-toolbar ui-helper-clearfix">
	<div title="Save Artist" id="wordtour-button-save">
		<span class="update"><br/>Last Updated <span id="artist_publish_date"></span></span>
		<span class="update"><br/>&nbsp;</span>		
	</div>
	<div title="Set As Default" id="wordtour-button-default">
		 <span style="font-size:9px;" class="update"><br/></span>
		 <span class="update"><br/>&nbsp;</span>	
	</div>
	<div title="Remove Default" id="wordtour-button-undo-default" style='display:none;'>
		 <span class="update" style="visibility:visible;"><br/>Artist set as default</span>
		 <span class="update"><br/>&nbsp;</span>	
	</div>
</div>
<?php 

$default_panels_order = array("left"=>array(
			array("details",1),
			array("status",1),
			array("bio",1),
			array("social",1),
			array("info",1)
		),
		"right"=>array(
			array("poster",1),
			array("order",1),
			array("genre",1),
			array("gallery",1),
			array("video",1),
			array("category",1)
		)
);

$panels_order = wordtour_get_panel_state("artist");
if(!$panels_order) $panels_order = $default_panels_order;
 

?>
<form id="artist-form">
	<input type="hidden" name="artist_id" value="<?php echo $_GET["artist_id"]?>"></input>
	<input type="hidden" name="_artist_nonce" value=""></input>
	<div class="wordtour-column wordtour-column-left">
		<?php 
			foreach($panels_order["left"] as $panel) {
				call_user_func("wordtour_artist_".$panel[0]."_panel",$panel[1]);
			}
		?>
	</div>
	<div class="wordtour-column wordtour-column-right" style="">
		<?php 
			foreach($panels_order["right"] as $panel) {
					call_user_func("wordtour_artist_".$panel[0]."_panel",$panel[1]);
			}
		?>	
	</div>
	<div style="clear:both;"></div>
</form>
</div>

<?php wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/artist");?>

