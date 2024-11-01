<?php
global$_wt_options;
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));

$page     = getQueryString("page");
$action   = getQueryString("action");
$tour_id   = getQueryString("tour_id");

if($action==="new"){
	include('tour.php');
	exit();
} else if($action==="edit" && !empty($tour_id)) {
	include('tour.php');
	exit();
}	

# GENERATE LIST
$list = new WT_List();
$list->set_columns('tour',array(
	'cb'            => '',
	'tour_id-col'       => 'ID',
	'tour_order-col'    => 'ORDER',
	'tour_name-col'     => 'Name',
	'tour_event_no-col' => 'Number of Events'
));
$list->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM ".WORDTOUR_TOUR." ORDER BY tour_order,tour_name",$_GET['paged']);		
?>
<div class="wrap">
	<h2>Tour</h2>
</div>

<div style="margin-right:15px;margin-bottom:15px;margin-top:15px;">
	<div class="wordtour-alert wordtour-alert-error" style="margin-left:0px;margin-right:0px;"></div>
		<div class="wordtour-toolbar ui-corner-all" style="margin-left:0px;margin-right:0px;overflow:hidden;">
			<div class="ui-helper-clearfix">
				<div title="Add New Tour" id="wordtour-button-add"></div>
				<div title="Delete Tour" id="wordtour-button-delete"></div>
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
		<?php $list->render("tours-list","tour_rows");?>
	</div>
</div>

<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/tours");
?>

