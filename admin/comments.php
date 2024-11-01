<?php 
global$_wt_options;
if(!current_user_can($_wt_options->options("user_role")))
	wp_die(__('Cheatin&#8217; uh?'));
if ( !defined('ABSPATH') )
       define('ABSPATH', dirname(__FILE__) . '/');
       
       
?>

<?php 
	$query = array();	

	if(isset($_GET["e"])) {
		$event = new wt_event($_GET["e"]);
		$event->retrieve();
		$data = $event->db_out();  
		$title = $data["event_start_date"]." at the ".$data["venue_name"]; 
		$query[] = "comment_event_id=".$event->id;  	 	
	}
	
	$comment_approved = "all";
	if(isset($_GET["status"])) {
		$comment_approved = $_GET["status"];
		if($comment_approved) {
			switch($comment_approved) {
				case "approved":
					$comment_approved = "1" ;
				break;
				case "pending":
					$comment_approved = "0" ;
				break;
			}
			
			$query[] = "comment_approved=".$comment_approved;  	
		}	 	
	}
	
	$total_pending = $wpdb->get_row("SELECT COUNT(*) as total FROM " . WORDTOUR_COMMENTS . " WHERE comment_approved=0","ARRAY_A");
	
	if($total_pending) $total_pending = $total_pending["total"];

	if(count($query)) {
		$query = "WHERE ".implode(" AND ",$query);
	}
	
	# GENERATE LIST
	$list = new WT_List();
	$list->set_columns('comments',array(
			'cb'              => '<input type="checkbox" />',
			'comment_author'  => 'Author',
			'comment_content' => 'Content',
			'event_id'        => 'In Response To'
	));
	
	
	$list->get_results(
		"SELECT SQL_CALC_FOUND_ROWS * 
		FROM ".WORDTOUR_COMMENTS."           
		$query ORDER BY comment_event_id"
	,$_GET['paged']);
	?>


<div class="wrap">
	<h2>Edit Comments
		 <?php if(isset($title)) {?>
		 <span>
		  <?php echo "on \"$title\"";?>
		</span>
		<a  class="button add-new-h2" title="Show All Comments" href="<?php echo admin_url("admin.php?page=".$_GET["page"]);?>">Back to Comments</a>
		<?php } ;?>
	</h2>
</div>

	
<ul class="subsubsub">
	<li class="all"><a <?php if($comment_approved=="all") echo "class=\"current\"";?> href="<?php echo admin_url("admin.php?page=".$_GET["page"]);?>">All</a> |</li>
	<li class="moderated"><a <?php if($comment_approved=="0") echo "class=\"current\""; ?> href="<?php echo admin_url("admin.php?page=".$_GET["page"]."&status=pending");?>">Pending <span class="count">(<span id="pending-count" class="pending-count"><?php echo $total_pending;?></span>)</span></a> |</li>
	<li class="approved"><a <?php if($comment_approved=="1") echo "class=\"current\""; ?> href="<?php echo admin_url("admin.php?page=".$_GET["page"]."&status=approved");?>">Approved</a></li>
</ul>
<div style="clear:both;"></div>


<div style="margin-right:15px;margin-bottom:15px;margin-top:15px;">
	<div class="wordtour-alert wordtour-alert-error" style="margin-left:0px;margin-right:0px;"></div>
		<div class="wordtour-toolbar ui-corner-all" style="margin-left:0px;margin-right:0px;overflow:hidden;">
			<div class="ui-helper-clearfix">
				<div title="Delete Comment" id="wordtour-button-delete"></div>
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
		<?php $list->render("the-comment-list","comments_rows");?>
	</div>
</div>

<?php 
	wt_script_js(WT_PLUGIN_PATH."/js/admin/pages/comments");
?>


