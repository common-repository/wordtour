<?php 
global$_wt_options;
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));

$page     = getQueryString("page");
$action   = getQueryString("action");
$artist_id   = getQueryString("artist_id");

if($action==="new"){
	include('artist.php');
	exit();
} else if($action==="edit" && !empty($artist_id)) {
	include('artist.php');
	exit();
} 
	
	
# GENERATE LIST
$list = new WT_List();
$list->set_columns('events',array(
	'cb'                  => '',
	'artist_id-col'       => 'ID',
	'artist_order-col'    => 'Order',
	'artist_name-col'     => 'Name',
	'artist_track-col'    => 'Tracks',
	'artist_album-col'    => 'Albums',
	'artist_event_no-col' => 'Events'
));
$list->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM ".WORDTOUR_ARTISTS." as a ORDER BY a.artist_order,a.artist_name ASC",$_GET['paged']);	
 		
?>

<div class="wrap">
	<h2>Artists</h2>
</div>

<div style="margin-right:15px;margin-bottom:15px;margin-top:15px;">
	<div class="wordtour-alert wordtour-alert-error" style="margin-left:0px;margin-right:0px;"></div>
		<div class="wordtour-toolbar ui-corner-all" style="margin-left:0px;margin-right:0px;overflow:hidden;">
			<div class="ui-helper-clearfix">
				<div title="Add New Artist" id="wordtour-button-add"></div>
				<div title="Delete Artist" id="wordtour-button-delete"></div>
				<div class="navigation tablenav">
					<?php if ($list->is_paging()) { ?>
						<div class="tablenav-pages">
							<?php 
							$list->render_paging(); 
							?>
						</div>
					<?php } ?>
				</div>	
			</div>
		</div>
		<?php $list->render("artists-list","artists_rows");?>
	</div>
</div>

<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/artists");
?>
