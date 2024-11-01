<?php 
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));

global $_wt_options;
$page     = getQueryString("page");
$action   = getQueryString("action");
$venue_id   = getQueryString("venue_id");


if($action==="new"){
	include('venue.php');
	exit();
} else if($action==="edit" && !empty($venue_id)) {
	
	include('venue.php');
	exit();
}	

# GENERATE LIST
$list = new WT_List();
$list->set_columns('venues',array(
	'cb'                     => '',
	'venue_id-col'           => 'ID',
	'venue_order-col'        => 'Order',
	'venue_name-col'         => 'Name',
	'venue_event_number-col' => 'Number of events',
	'venue_address-col'      => 'Address',
	'venue_city-col'         => 'City',
	'venue_state-col'        => 'State',
	'venue_country-col'      => 'Country'
));

$list->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM ".WORDTOUR_VENUES." ORDER BY venue_name",$_GET['paged']);

?>

<div class="wrap">
	<h2>Venues</h2>
</div>

<div style="margin-right:15px;margin-bottom:15px;margin-top:15px;">
	<div class="wordtour-alert wordtour-alert-error" style="margin-left:0px;margin-right:0px;"></div>
		<div class="wordtour-toolbar ui-corner-all" style="margin-left:0px;margin-right:0px;overflow:hidden;">
			<div class="ui-helper-clearfix">
				<div title="Add New Venue" id="wordtour-button-add"></div>
				<div title="Delete Venue" id="wordtour-button-delete"></div>
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
		<?php $list->render("venues-list","venue_rows");?>
	</div>
</div>
<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/venues");
?>

