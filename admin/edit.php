<?php
global$_wt_options;
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));
if ( !defined('ABSPATH') )
       define('ABSPATH', dirname(__FILE__) . '/');
    require_once(ABSPATH . 'wp-settings.php');
# need to check if i can add permission called 'edit_events'
$page     = getQueryString("page");
$action   = getQueryString("action");
$event_id = getQueryString("event_id");
$meta_id  = getQueryString("meta_id");

if($action==="new"){
	include('new_event.php');
} else if($action==="edit" && !empty($event_id)) {
	include('event.php');
} else {
	render_edit_rows_page($event_id,$page);
}	

function get_query_mode_sql() {
	$sql_array = array(
		"all"       => "WHERE e.event_published != 3",
		"published" => "WHERE e.event_published = 1",
		"upcoming"  => "WHERE e.event_published = 1 AND e.event_start_date >= CURDATE()",
		"archive"   => "WHERE e.event_published = 1 AND e.event_start_date < CURDATE()",
		"unpublished"     => "WHERE e.event_published = 0"
	);
	$sql = '' ;
	if(!empty($_GET["event_date"])) {
		$sql.= $sql_array[$_GET["event_date"]] ; 		
	} else {
		$sql.=$sql_array["published"];	
	}
	
	if(!empty($_GET["date"])) {
		$sql.= " AND YEAR(e.event_start_date)>=".substr($_GET["date"],0,4)." AND MONTH(e.event_start_date)=".substr($_GET["date"],-2) ; 		
	}
	
	if(!empty($_GET["venue"])) {
		$sql.= " AND e.event_venue_id = ".$_GET["venue"] ; 		
	}
	
	if(!empty($_GET["status"])) {
		$sql.= " AND e.event_status = '".$_GET["status"]."'" ; 		
	}
	
	if(!empty($_GET["artist"])) {
		$sql.= " AND e.event_artist_id = '".$_GET["artist"]."'" ; 		
	}
	
	if(!empty($_GET["tour"])) {
		$sql.= " AND e.event_tour_id = '".$_GET["tour"]."'" ; 		
	}
	
	return $sql ;
}
	
function link_query_html($id,$title,$link,$qs='',$sql,$is_default=0,$show_sep=1) {
	global $wpdb ;
	$class = ($_GET[$qs] == $id || $is_default && empty($_GET[$qs]) )  ? "current" : "" ;
	$count = $wpdb->get_var($sql);
	$sep = ($show_sep) ? "|" : "";
	$count_markup = ($id !="upcoming" && $id!="archive") ? "<span class='count'>(<span class='countNum'>$count</span>)</span>" : "";
	
	$html = "<li class='$id'>
				<a class='$class' href='$link&event_date=$id'>$title $count_markup</a>$sep
			</li>";
	echo $html ;		
}



function render_edit_rows_page(&$event_id,&$page) {
	global $wpdb,$wt_msg;
	
	$dbQuery = array(
		"all"      => "SELECT count(*) FROM " . WORDTOUR_EVENTS . " AS e",
		"published"=> "SELECT count(*) FROM " . WORDTOUR_EVENTS . " AS e  WHERE e.event_published = 1",
		"upcoming" => "SELECT count(*) FROM " . WORDTOUR_EVENTS . " AS e WHERE e.event_published = 1 AND e.event_start_date >= CURDATE()",
		"archive"  => "SELECT count(*) FROM " . WORDTOUR_EVENTS . " AS e WHERE e.event_published = 1 AND e.event_start_date < CURDATE()",
		"unpublished" => "SELECT count(*) FROM " . WORDTOUR_EVENTS . " AS e WHERE e.event_published = 0",
		"artists"  => WT_Artist::all(),
		"tour"     => WT_Tour::all(),
		"status"   => get_all_status(),
		"venues"   => WT_Venue::all(),
		"date"     => $wpdb->get_results("SELECT 
										  DISTINCT YEAR(event_start_date) AS year,
										  MONTH(event_start_date) AS month, 
										  MONTHNAME(event_start_date) AS month_name 
										  FROM ".WORDTOUR_EVENTS." WHERE event_published = 1 ORDER BY event_start_date DESC","ARRAY_A")
	);
	
	# GENERATE LIST
	$list = new WT_List();
	$list->set_columns('events',array(
		'event_check'   =>'',
		'event_id'      => 'ID',
		'event_date'    =>'Date',
		'event_location'=>'Location',
		'event_status'  =>'Status',
		'artist'        =>'Artist',
		'artists'       =>'Additional Artists',
		'tour'          =>'Tour',
		'extra_info'    =>'&nbsp;'
	));

	$list->get_results(
		"SELECT SQL_CALC_FOUND_ROWS * 
		FROM ".WORDTOUR_EVENTS." AS e LEFT JOIN ".WORDTOUR_VENUES." AS v 
		ON e.event_venue_id = v.venue_id 
		LEFT JOIN ".WORDTOUR_EVENTS_META." AS m 
		ON e.event_meta_id = m.meta_id
		LEFT JOIN ".WORDTOUR_ARTISTS." AS a
		ON e.event_artist_id = a.artist_id
		LEFT JOIN ".WORDTOUR_TOUR." AS t
		ON e.event_tour_id = t.tour_id    
		 ".get_query_mode_sql()."     
		ORDER BY e.event_start_date DESC,e.id,e.event_is_headline"
	,$_GET['paged']);
	
?>
<?php 
global $_wt_options;
?>

<div class="wrap">
	<h2>Events</h2>
</div>

<ul class="subsubsub" style='float:none;'>
	<?php 
		link_query_html("all","All",admin_url("admin.php?page=$page"),"event_date",$dbQuery["all"],0);
		link_query_html("published","Published",admin_url("admin.php?page=$page"),"event_date",$dbQuery["published"],1);
		link_query_html("upcoming","Upcoming",admin_url("admin.php?page=$page"),"event_date",$dbQuery["upcoming"],0);
		link_query_html("archive","Archive",admin_url("admin.php?page=$page"),"event_date",$dbQuery["archive"],0);
		link_query_html("unpublished","Unpublished",admin_url("admin.php?page=$page"),"event_date",$dbQuery["unpublished"],0,0); 
	?>
</ul>


<form method="get" action="<?php echo admin_url("admin.php?page=$page") ?>">
	<input type="hidden" name="page" value="<?php echo $page ?>"></input>
	<input type="hidden" name="event_date" value="<?php echo $_GET["event_date"] ?>"></input>
	<select name="date">
		<option value="">Show All Dates</option>
		<?php 
			foreach($dbQuery["date"] as $date) { 
				echo "<option ".(($date["year"].$date["month"] == $_GET["date"]) ? "selected" : "" )." value=\"".$date["year"].$date["month"]."\">".$date["month_name"]." ".$date["year"]."</option>" ;
			}
		?>
	</select>
	<?php
		echo generate_select_html('','venue',array_associate_val_to_key($dbQuery["venues"],"venue_id","venue_name"),$_GET["venue"],array("value"=>"","text"=>"Show All Venues"));
		echo generate_select_html('','status',$dbQuery["status"],$_GET["status"],array("value"=>"","text"=>"Show All Status")); 
		echo generate_select_html('','artist',array_associate_val_to_key($dbQuery["artists"],"artist_id","artist_name"),$_GET["artist"],array("value"=>"","text"=>"Show All Artists"));
		echo generate_select_html('','tour',array_associate_val_to_key($dbQuery["tour"],"tour_id","tour_name"),$_GET["tour"],array("value"=>"","text"=>"Show All Tour"));
	?> 
	<input class="button-secondary" type="submit" value="Filter"/>
</form>		




<div style="margin-right:15px;margin-bottom:15px;margin-top:15px;">
	<div class="wordtour-alert wordtour-alert-error" style="margin-left:0px;margin-right:0px;"></div>
	
	<div class="wordtour-toolbar ui-corner-all" style="margin-left:0px;margin-right:0px;overflow:hidden;">
		<div class="ui-helper-clearfix">
			<div title="Add New Event" id="wordtour-button-add"></div>
			<div title="Delete Event" id="wordtour-button-delete"></div>
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
	<?php $list->render("events-list","event_rows");?>
<?php		
}
?>
<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/events");
?>
