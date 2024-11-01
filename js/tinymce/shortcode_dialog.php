<?php 
require_once("../../../../../wp-load.php");
require_once('../../../../../wp-admin/includes/admin.php');
if (!current_user_can('upload_files'))
	wp_die(__('You do not have permission to upload files.'));
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
echo "<title>WordTour Shortcode Generator</title>";
wt_admin_menu();
$hook_suffix = "wordtour";
wp_admin_css( 'css/global' );
wp_admin_css();
wp_admin_css( 'css/colors' );
wp_admin_css( 'css/ie' );

do_action('admin_enqueue_scripts', $hook_suffix);
do_action("admin_print_styles-$hook_suffix");
do_action('admin_print_styles');
do_action("admin_print_scripts-$hook_suffix");
do_action('admin_print_scripts');
do_action("admin_head-$hook_suffix");
do_action('admin_head');
?>
<style>
 
body, div, td,th {
	font-size: 10px;
	font-weight:normal;
	text-align:left;
	white-space : no-wrap;
	height:auto;
	/*font-family : Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;*/
}

table {
	padding:0px;
	border: 0px;
	border: 1px solid #CCCCCC;
	border-top-width: 0px ;
}

th {
	padding:3px;
	padding-top:10px;
	vertical-align:top;
	border-top: 1px solid #CCCCCC;
	white-space: no-wrap;
}

td {
	vertical-align:top;
	padding:3px;
	border-top: 1px solid #CCCCCC;
	white-space: no-wrap;
}

td div.th {
	padding-bottom: 3px;
	font-weight: bold;
	font-size: 9px;
	
}

.row-1 td,.row-1 th {
	background-color: #FFFFFF;
	
}

.row-2 td,.row-2 th {
	background-color: #DDDDDD;
}

td input, td select {
	width:100%;
}

select {
	height: 2em;
	padding:2px;
}

</style>

<script>
	<?php include 'tiny_mce_popup.js';?>
</script>
<?php include 'shortcode.php'; ?>
