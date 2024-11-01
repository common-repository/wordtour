<?php 
global$_wt_options;
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));

$page     = getQueryString("page");
$action   = getQueryString("action");
$track_id   = getQueryString("track_id");

if($action==="new"){
	include('track.php');
	exit();
} else if($action==="edit" && !empty($track_id)) {
	include('track.php');
	exit();
} 

function get_query_mode_sql() {
	$sql = array() ;
	
	if(!empty($_GET["artist"])) {
		$sql[] = "track_artist_id = $_GET[artist]" ; 		
	}
	
	if(count($sql)>0) return " WHERE ".implode(" AND ",$sql);
	return "";
}

$dbQuery = array(
	"artists"  => WT_Artist::all(),
);

	
# GENERATE LIST
$list = new WT_List();
$list->set_columns('events',array(
	'cb'                  => '',
	'album_id-col'        => 'ID',
	'album_title-col'     => 'Title',
	'album_artist'        => 'Artist',
	'album_label'         => 'Label'
));
	
	
# GENERATE LIST
$list = new WT_List();
$list->set_columns('events',array(
	'cb'                  => '',
	'track_id-col'        => 'ID',
	'track_title-col'     => 'Title',
	'track_albums'        => 'Albums',
	'track_artist'        => 'Artist',
	'track_label'         => 'Label'
));
$list->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM ".WORDTOUR_TRACKS." as t LEFT JOIN ".WORDTOUR_ARTISTS." as a ON a.artist_id = t.track_artist_id ".get_query_mode_sql()." ORDER BY t.track_title ASC",$_GET['paged']);	
 		
?>

<div class="wrap">
	<h2>Tracks</h2>
</div>

<form method="get" action="<?php echo admin_url("admin.php") ?>">
	<input type="hidden" name="page" value="<?php echo $page ?>"></input>
	<?php 
		echo generate_select_html('','artist',array_associate_val_to_key($dbQuery["artists"],"artist_id","artist_name"),$_GET["artist"],array("value"=>"","text"=>"Show All Artists"));
	?>
	<input class="button-secondary" type="submit" value="Filter"/>
</form>

<div style="margin-right:15px;margin-bottom:15px;margin-top:15px;">
	<div class="wordtour-alert wordtour-alert-error" style="margin-left:0px;margin-right:0px;"></div>
		<div class="wordtour-toolbar ui-corner-all" style="margin-left:0px;margin-right:0px;overflow:hidden;">
			<div class="ui-helper-clearfix">
				<div title="Add New Track" id="wordtour-button-add"></div>
				<div title="Delete Track" id="wordtour-button-delete"></div>
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
		<?php $list->render("tracks-list","tracks_rows");?>
	</div>
</div>

<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/tracks");
?>
