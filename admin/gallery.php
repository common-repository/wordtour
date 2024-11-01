<?php 
global$_wt_options;
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));

# GENERATE LIST
$list = new WT_List();
$list->set_columns('gallery',array(
	'cb'                   => '<input type="checkbox" />',
	'gallery_id-col'       => 'ID',
	'gallery_name-col'     => 'Name',
	'gallery_total-col'    => 'Total Thumbnails',
	'gallery_publish-col'  => 'Last Update',
	'gallery_event_no-col' => 'Attached To'
));
$list->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM ".WORDTOUR_GALLERY." as g ORDER BY g.gallery_name ASC",$_GET['paged']);	

?>
<div class="wrap">
	<h2>Gallery</h2>
</div>

<div style="margin-right:15px;margin-bottom:15px;margin-top:15px;">
	<div class="wordtour-alert wordtour-alert-error" style="margin-left:0px;margin-right:0px;"></div>
		<div class="wordtour-toolbar ui-corner-all" style="margin-left:0px;margin-right:0px;overflow:hidden;">
			<div class="ui-helper-clearfix">
				<div title="Add New Gallery" id="wordtour-button-add"></div>
				<div title="Delete Gallery" id="wordtour-button-delete"></div>
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
		<?php $list->render("gallery_list","gallery_rows"); ?>
	</div>
</div>

<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/gallery");
?>