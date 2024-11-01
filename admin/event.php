<?php
	$action_new = WT_ADMIN_URL."page=wt_new_event";
	$action_edit = $event_id ? WT_ADMIN_URL."page=wordtour/navigation.php&action=edit&event_id=$event_id" : "#";
	$action_trash = $event_id ? WT_ADMIN_URL."page=wordtour/navigation.php&action=trash&event_date=trash&event_id=$event_id&_nonce=".wp_create_nonce(WT_Event::NONCE_TRASH) : "#" ;
	$back_to_url = WT_ADMIN_URL."page=wordtour/navigation.php";
	$action_header = $action === "edit" ? "Edit" : "New";
?>


<style>
	/* Comments Panel*/
	.check-column {
		display: none;
	}
	.column-response {
		display: none;
	}	
	
	#wordtour-panel-comments .wordtour-panel-content {
		padding: none;
	}
	
	.wordtour-panel-content .widefat {
		border-width: 0px;
	}
</style>

<div class="wrap">
	<h2><?php echo $action_header ?> Event
		<a class="button button-h2" title="Back to Events" href="<?php echo $back_to_url;?>">< Back to Events</a>
		<?php if($action=="edit") { ?>
		<a class="button button-h2" title="Add New Event" href="<?php echo $action_new;?>">New Event</a>
		<?php }?>
	</h2>
</div>

<div class="wordtour-column-wrap wordtour-column-event">

<div class="wordtour-alert"></div>
<div class="wordtour-toolbar" style="overflow:hidden;">
	<div class="ui-helper-clearfix" style="width:2000px;">
		<div title="Save Event" id="wordtour-button-save"></div>
		<div title="Unpublish Event" id="wordtour-button-trash"></div>
		<div title="Publish Event" id="wordtour-button-publish"></div>	
	</div>
</div>

<?php 

$default_panels_order = array("left"=>array(
			array("details",1),
			array("status",1),
			array("title",1),
			array("notes",1),
			array("moreartists",1),
			array("tickets",0),
			array("comments",0)
		),
		"right"=>array(
			array("social",1),
			array("poster",1),
			array("status2",1),
			array("genre",1),
			array("gallery",1),
			array("category",1),
			array("video",1),
			array("rsvp",1)
		)
);

$panels_order = wordtour_get_panel_state("event");
if(!$panels_order) $panels_order = $default_panels_order;

?>
<form id="event-form">
	<input type="hidden" name="event_id" value="<?php echo $_GET["event_id"]?>"></input>
	<input type="hidden" name="event_meta_id"></input>
	<input type="hidden" name="_event_nonce" value=""></input>
	<div class="ui-helper-clearfix">
		<div class="wordtour-column wordtour-column-left">
			<?php 
				foreach($panels_order["left"] as $panel) {
					call_user_func("wordtour_event_".$panel[0]."_panel",$panel[1]);
				}
			?>
		</div>
		<div class="wordtour-column wordtour-column-right" style="">
			<?php 
				foreach($panels_order["right"] as $panel) {
						call_user_func("wordtour_event_".$panel[0]."_panel",$panel[1]);
				}
			?>	
		</div>
	</div>
</form>
</div>

<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/event");
?>
