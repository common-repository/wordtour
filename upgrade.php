<?php
function wordtour_upgrade_1_2_1(){
	global $wpdb;
	// Add Event Type Column
   	$wpdb->query("ALTER TABLE ".WORDTOUR_EVENTS." ADD event_type VARCHAR(32) AFTER event_title");
   	$wpdb->query("ALTER TABLE ".WORDTOUR_EVENTS." MODIFY event_type VARCHAR(32) DEFAULT 'event'");
   	$wpdb->query("UPDATE ".WORDTOUR_EVENTS." SET event_type='event' WHERE event_type is NULL"); 

   	return 1;
}

function wordtour_upgrade_1_2_2(){
	return 1;
}

function wordtour_upgrade_1_2_3(){
	global $_wt_options,$wpdb;
	// Add type column - social action can be implemented not only on events
	$wpdb->query("ALTER TABLE ".WORDTOUR_SOCIAL." ADD social_parent_type VARCHAR(32) AFTER social_event_id");
	$wpdb->query("ALTER TABLE ".WORDTOUR_SOCIAL." MODIFY social_parent_type VARCHAR(32) DEFAULT 'event'");
   	$wpdb->query("ALTER TABLE ".WORDTOUR_SOCIAL." CHANGE social_event_id social_parent_id mediumint(7)");
   	$wpdb->query("UPDATE ".WORDTOUR_SOCIAL." SET social_parent_type = 'event' WHERE social_id>0");  
   	
   	
   	$_wt_options->update(array("theme_path"=>str_replace(ABSPATH,"",WT_THEME_PATH)));
	$_wt_options->update(array("eventbrite_auto_update"=>1));
	delete_option("wordtour_panel_state");
	// Implement @anywhere, user name password not supported
	delete_option("twitter_password");
	delete_option("twitter_user");
	return 1;
}

function wordtour_upgrade_1_2_3_1(){
	return 1;
}

function wordtour_upgrade_1_2_4_0(){
	global $_wt_options,$wpdb;
	delete_option("wordtour_panel_state");	
	// Multiple artist support
	// rename events table
	$wpdb->query("RENAME TABLE ".WORDTOUR_EVENTS." TO ".WORDTOUR_EVENTS."_old");
	// create new table
	$schema = wt_schema_table();
	$wpdb->query($schema[0]);
	$columns = "event_id,event_publish_date,event_published,event_meta_id,event_venue_id,
				event_artist_id,event_tour_id,event_thumbnail_id,event_title,event_type,event_start_date,event_start_time,
				event_end_date,event_end_time,event_opening_act,event_on_sale,event_status,event_notes,comment_count,rsvp_count,
				gallery_status,comment_status,rsvp_status,flickr_status,video_status,post_status";
	
	$wpdb->query("INSERT INTO ".WORDTOUR_EVENTS." ($columns) SELECT $columns FROM ".WORDTOUR_EVENTS."_old");
	// add new author column
	$wpdb->query("ALTER TABLE ".WORDTOUR_ARTISTS." ADD artist_author BIGINT(20) AFTER artist_id");
	$wpdb->query("ALTER TABLE ".WORDTOUR_ARTISTS." MODIFY artist_author BIGINT(20) DEFAULT 0");
	$wpdb->query("UPDATE ".WORDTOUR_ARTISTS." SET artist_author = 0");
	$wpdb->query("ALTER TABLE ".WORDTOUR_TOUR." ADD tour_author BIGINT(20) AFTER tour_id");
	$wpdb->query("ALTER TABLE ".WORDTOUR_TOUR." MODIFY tour_author BIGINT(20) DEFAULT 0");
	$wpdb->query("UPDATE ".WORDTOUR_TOUR." SET tour_author = 0");
	$wpdb->query("ALTER TABLE ".WORDTOUR_VENUES." ADD venue_author BIGINT(20) AFTER venue_id");
	$wpdb->query("ALTER TABLE ".WORDTOUR_VENUES." MODIFY venue_author BIGINT(20) DEFAULT 0");
	$wpdb->query("UPDATE ".WORDTOUR_VENUES." SET venue_author = 0");
	
	return 1;
};

function wordtour_upgrade_1_2_5_0(){
	global $_wt_options,$wpdb;
	// delete column genre from artist table
	$wpdb->query("ALTER TABLE ".WORDTOUR_ARTISTS." DROP artist_genre");
	// add schema of new tables
	$schema = wt_schema_table();
	// create track table
	$wpdb->query($schema[10]);
	// create album table
	$wpdb->query($schema[11]);
	// Move thumbnail id to attachments table and delete column
	$events = $wpdb->get_results("SELECT event_id,event_thumbnail_id FROM ".WORDTOUR_EVENTS." WHERE event_thumbnail_id>0");
	foreach($events as $event) {
		$wpdb->insert(WORDTOUR_ATTACHMENT, array('attachment_target'=>'event','attachment_target_id'=>$event->event_id,"attachment_type"=>"thumbnail","attachment_type_id"=>$event->event_thumbnail_id,"attachment_info"=>""));
	}
	
	$artists = $wpdb->get_results("SELECT artist_id,artist_thumbnail_id FROM ".WORDTOUR_ARTISTS." WHERE artist_thumbnail_id>0");
	foreach($artists as $artist) {
		$wpdb->insert(WORDTOUR_ATTACHMENT, array('attachment_target'=>'artist','attachment_target_id'=>$artist->artist_id,"attachment_type"=>"thumbnail","attachment_type_id"=>$artist->artist_thumbnail_id,"attachment_info"=>""));
	}
	
	$tours = $wpdb->get_results("SELECT tour_id,tour_thumbnail_id FROM ".WORDTOUR_TOUR." WHERE tour_thumbnail_id>0");
	foreach($tours as $tour) {
		$wpdb->insert(WORDTOUR_ATTACHMENT, array('attachment_target'=>'tour','attachment_target_id'=>$tour->tour_id,"attachment_type"=>"thumbnail","attachment_type_id"=>$tour->tour_thumbnail_id,"attachment_info"=>""));
	}
	
	$venues = $wpdb->get_results("SELECT venue_id,venue_thumbnail_id FROM ".WORDTOUR_VENUES." WHERE venue_thumbnail_id>0");
	foreach($venues as $venue) {
		$wpdb->insert(WORDTOUR_ATTACHMENT, array('attachment_target'=>'venue','attachment_target_id'=>$venue->venue_id,"attachment_type"=>"thumbnail","attachment_type_id"=>$venue->venue_thumbnail_id,"attachment_info"=>""));
	}
	
	$wpdb->query("ALTER TABLE ".WORDTOUR_EVENTS." DROP event_thumbnail_id");
	$wpdb->query("ALTER TABLE ".WORDTOUR_ARTISTS." DROP artist_thumbnail_id");
	$wpdb->query("ALTER TABLE ".WORDTOUR_TOUR." DROP tour_thumbnail_id");
	$wpdb->query("ALTER TABLE ".WORDTOUR_VENUES." DROP venue_thumbnail_id");
	// delete panel state
	delete_option("wordtour_panel_state");
	// update album permalink
	if($_wt_options->options("permalinks")==1) {
		$_wt_options->update(array("permalinks_album"=>get_bloginfo("url")."/album/%name%/%id%/"));	
	}
	return 1;
}


function wordtour_upgrade() {
	global $_wt_options;
	$versions = array("1.2.1"=>"1_2_1","1.2.2"=>"1_2_2","1.2.3"=>"1_2_3","1.2.3.1"=>"1_2_3_1","1.2.4.0"=>"1_2_4_0","1.2.5.0"=>"1_2_5_0");
	if(!$_wt_options->options("version")) {
		// upgrade
		wordtour_install();
		$_wt_options->update(array("version"=>"1.2.1"));
		$_wt_options->update(array("default_theme"=>"jqueryui"));
		delete_option('twitter');
		delete_option('wt_facebook');
	}
	
	
	$current_version = $_wt_options->options("version");
	$update_success = 1;
	if($current_version < WORDTOUR_VERSION) {
		foreach($versions as $version=>$func) {
			if($current_version < $version) {
				$result = call_user_func("wordtour_upgrade_$func");
				if($update_success) $update_success = $result; 
				//$_wt_options->update(array("version"=>WORDTOUR_VERSION));
			}	
		}
		
		if($update_success) $_wt_options->update(array("version"=>WORDTOUR_VERSION));
	}
}

wordtour_upgrade();

function plugin_theme_backup_rmdirr($dirname){
	// Sanity check
	if (!file_exists($dirname)) {
		return false;
	}

	// Simple delete for a file
	if (is_file($dirname)) {
		return unlink($dirname);
	}

	// Loop through the folder
	$dir = dir($dirname);
	while (false !== $entry = $dir->read()) {
		// Skip pointers
		if ($entry == '.' || $entry == '..') {
		continue;
		}
		// Recurse
		plugin_theme_backup_rmdirr("$dirname/$entry");
	}
	// Clean up
	$dir->close();
	return rmdir($dirname);
}

function plugin_theme_backup_copyr($source, $dest){
    // Check for symlinks
    if(is_link($source)) {
        return symlink(readlink($source), $dest);
    }
    // Simple copy for a file
    if(is_file($source)) {
        return copy($source, $dest);
    }
    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest);
    }
    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }
    	// Deep copy directories
       	plugin_theme_backup_copyr("$source/$entry", "$dest/$entry");
    }
    // Clean up
    $dir->close();
    return true;
}
function plugin_theme_backup() {
	try {
		$to = dirname(__FILE__)."/../wordtour_theme_backup/";
		$from = dirname(__FILE__)."/theme/";
		if(wt_is_writable($to) && wt_is_writable($from)){
			if(!is_dir($to)) {
				mkdir($to);
		    }
		    
		    $dir = dir($from);
			while (false !== $entry = $dir->read()) {
				if ($entry == '.' || $entry == '..' || is_file("$from$entry") || strtolower($entry) == "default" || strtolower($entry) == "library") {
		            continue;
		        }
		        plugin_theme_backup_copyr("$from$entry", "$to$entry/");
			}
			$dir->close();
		}
	} catch(Exception $e) {
		//echo "Error backup theme folders, probably a permission isn't assigned to wp-content folder";
		//return 0;
	}
}

function plugin_theme_backup_recover(){
	try {
		$from = dirname(__FILE__)."/../wordtour_theme_backup/";
		$to = dirname(__FILE__)."/theme/";
		if(wt_is_writable($to) && wt_is_writable($from)){
			plugin_theme_backup_copyr($from, $to);
			if (is_dir($from)) {
				plugin_theme_backup_rmdirr($from);#http://putraworks.wordpress.com/2006/02/27/php-delete-a-file-or-a-folder-and-its-contents/
			}
		}
	} catch(Exception $e) {
		//echo "Error backup theme folders, probably a permission isn't assigned to wordtour/theme folder";
		//return 0;
	}
}
