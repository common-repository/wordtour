<?php
	//require_once "wp-load.php";
	get_header(); 
?>
<div id="container">
	<div id="content" role="main">
		<div id="wordtour-content">
		<!--  WORDTOUR CODE -->
			<div class="wt-single">
			<?php
				// Content
				include WT_THEME_PATH."single.php";
			?>
			</div>
		</div>
		<!--  WORDTOUR CODE -->
	</div><!-- #content -->
</div><!-- #container -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>

