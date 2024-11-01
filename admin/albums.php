<?php 
global$_wt_options;
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));

$page     = getQueryString("page");
$action   = getQueryString("action");
$album_id   = getQueryString("album_id");

if($action==="new"){
	include('album.php');
	exit();
} else if($action==="edit" && !empty($album_id)) {
	include('album.php');
	exit();
} 


function get_query_mode_sql() {
	$sql = array() ;
	
	if(!empty($_GET["artist"])) {
		$sql[] = "album_artist_id = $_GET[artist]" ; 		
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
	'album_order-col'     => 'Order',
	'album_title-col'     => 'Title',
	'album_artist'        => 'Artist',
	'album_label'         => 'Label'
));

$list->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM ".WORDTOUR_ALBUMS." as al LEFT JOIN ".WORDTOUR_ARTISTS." as a ON a.artist_id = al.album_artist_id ".get_query_mode_sql()." ORDER BY al.album_title ASC",$_GET['paged']);	
 		
?>

<div class="wrap">
	<h2>Albums</h2>
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
				<div title="Add New Album" id="wordtour-button-add"></div>
				<div title="Delete Album" id="wordtour-button-delete"></div>
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
		<?php $list->render("albums-list","albums_rows");?>
	</div>
</div>

<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/albums");
?>
