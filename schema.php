<?php
function wt_schema_table() {
	global $wpdb;
	$charset_collate = '';
  	 if ( ! empty($wpdb->charset) )
  		 $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
   	 if ( ! empty($wpdb->collate) )
   		$charset_collate .= " COLLATE $wpdb->collate";
   	
	
	$schema[] = "CREATE TABLE " . WORDTOUR_EVENTS . " (
	  id bigint(20) AUTO_INCREMENT,	
	  event_id bigint(20) NOT NULL,
	  event_publish_date datetime DEFAULT '0000-00-00 00:00:00',
	  event_published tinyint(1) DEFAULT 1,
	  event_author bigint(20) DEFAULT 0,
	  event_meta_id mediumint(7) DEFAULT 0,
	  event_venue_id mediumint(7) DEFAULT 0,
	  event_artist_id mediumint(7) DEFAULT 0,
	  event_is_headline tinyint(1) DEFAULT 1, 
	  event_tour_id mediumint(7) DEFAULT 0,
	  event_title longtext,
	  event_type varchar(32) DEFAULT 'event',
	  event_start_date date NOT NULL,
	  event_start_time time,
	  event_end_date date,
	  event_end_time time,
	  event_opening_act text,
	  event_on_sale date,
	  event_status varchar(32) DEFAULT 'active',
	  event_notes longtext,
	  comment_count smallint(5) DEFAULT 0,
	  rsvp_count smallint(5) DEFAULT 0,
	  gallery_status tinyint(1) DEFAULT 0,
	  comment_status tinyint(1) DEFAULT 0,
	  rsvp_status tinyint(1) DEFAULT 0,
	  flickr_status tinyint(1) DEFAULT 0,
	  video_status tinyint(1) DEFAULT 0,
	  post_status tinyint(1) DEFAULT 0,
	  PRIMARY KEY (id)
	) $charset_collate";
	   		 
	$schema[] = "CREATE TABLE " . WORDTOUR_EVENTS_META . " (
	  meta_id mediumint(7) AUTO_INCREMENT,
	  tkts_url varchar(255),
	  tkts_phone varchar(100),
	  tkts_price varchar(32),
	  PRIMARY KEY (meta_id)
	  ) $charset_collate";
			 
	$schema[] = "CREATE TABLE " . WORDTOUR_VENUES . " (
	  venue_id mediumint(7) AUTO_INCREMENT,
	  venue_author bigint(20) DEFAULT 0,
	  venue_name varchar(255) NOT NULL,
	  venue_publish_date datetime DEFAULT '0000-00-00 00:00:00',
	  venue_address varchar(255),
	  venue_city varchar(255),
	  venue_state varchar(5),
	  venue_country varchar(5) NOT NULL,
	  venue_zip varchar(50),
	  venue_url varchar(255),
	  venue_phone varchar(50),
	  venue_order smallint(5) DEFAULT 0,
	  venue_gallery_status tinyint(1) DEFAULT 1,
	  venue_tour_status tinyint(1) DEFAULT 1,
	  venue_video_status tinyint(1) DEFAULT 1,
	  venue_post_status tinyint(1) DEFAULT 1,
	  venue_flickr_status tinyint(1) DEFAULT 1,
	  venue_info text,
	  PRIMARY KEY (venue_id)
	) $charset_collate";
	   		
	$schema[] = "CREATE TABLE " . WORDTOUR_TOUR . " (
	  tour_id mediumint(7) AUTO_INCREMENT,
	  tour_author bigint(20) DEFAULT 0,
	  tour_name varchar(40) NOT NULL,
	  tour_publish_date datetime DEFAULT '0000-00-00 00:00:00',
	  tour_description longtext,
	  tour_order smallint(5) DEFAULT 0,
	  tour_gallery_status tinyint(1) DEFAULT 1,
	  tour_tour_status tinyint(1) DEFAULT 1,
	  tour_video_status tinyint(1) DEFAULT 1,
	  tour_post_status tinyint(1) DEFAULT 1,
	  tour_flickr_status tinyint(1) DEFAULT 1,
	  PRIMARY KEY (tour_id)
	) $charset_collate";
	   		
	$schema[] = "CREATE TABLE " . WORDTOUR_ARTISTS . " (
	  artist_id mediumint(7) AUTO_INCREMENT,
	  artist_author bigint(20) DEFAULT 0,
	  artist_name varchar(255) NOT NULL,
	  artist_publish_date datetime DEFAULT '0000-00-00 00:00:00',
	  artist_bio longtext,
	  artist_record_company varchar(50),
	  artist_order smallint(5) DEFAULT 0,
	  artist_social_links text,
	  artist_email varchar(32),
	  artist_website_url varchar(255), 
	  artist_gallery_status tinyint(1) DEFAULT 1,
	  artist_video_status tinyint(1) DEFAULT 1,
	  artist_tour_status tinyint(1) DEFAULT 1,
	  artist_flickr_status tinyint(1) DEFAULT 1,
	  artist_post_status tinyint(1) DEFAULT 1,	
	  PRIMARY KEY (artist_id)
	) $charset_collate";
	   		
	$schema[] = "CREATE TABLE " . WORDTOUR_COMMENTS . " (
	  comment_id mediumint(7) AUTO_INCREMENT,
	  comment_event_id mediumint(7) DEFAULT 0,
	  comment_author tinytext NOT NULL,
	  comment_author_email varchar(100),
	  comment_content text NOT NULL,
	  comment_date datetime DEFAULT '0000-00-00 00:00:00',
	  comment_user_id bigint(20) NOT NULL,
	  comment_approved tinyint(1) DEFAULT 1,
	  PRIMARY KEY (comment_id)
	) $charset_collate";
	   		
	$schema[] = "CREATE TABLE ". WORDTOUR_ATTENDING . " (
	rsvp_id mediumint(7) AUTO_INCREMENT,
	rsvp_event_id mediumint(7) DEFAULT 0,
	rsvp_user bigint(20) DEFAULT 0,
	rsvp_date datetime DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (rsvp_id)
	) $charset_collate";
	
	$schema[] = "CREATE TABLE ". WORDTOUR_GALLERY . " (
	gallery_id mediumint(7) AUTO_INCREMENT,
	gallery_name text NOT NULL,
	gallery_publish_time datetime DEFAULT '0000-00-00 00:00:00',
	gallery_attachment longtext NOT NULL,
	PRIMARY KEY (gallery_id)
	) $charset_collate";
	
	$schema[] = "CREATE TABLE ". WORDTOUR_SOCIAL . " (
	social_id mediumint(7) AUTO_INCREMENT,
	social_parent_id mediumint(7) NOT NULL,
	social_parent_type varchar(32) DEFAULT 'event',
	social_type varchar(32) DEFAULT 'fbstatus',
	social_publish_time datetime DEFAULT '0000-00-00 00:00:00',
	social_ref_id bigint(20) DEFAULT 0,
	PRIMARY KEY (social_id)
	) $charset_collate";

	$schema[] =  "CREATE TABLE ". WORDTOUR_ATTACHMENT . " (
	attachment_id mediumint(7) AUTO_INCREMENT,
	attachment_target varchar(32) DEFAULT 'event',
	attachment_target_id mediumint(7) DEFAULT 0,
	attachment_type varchar(32),
	attachment_type_id varchar(32),
	attachment_info text,
	PRIMARY KEY (attachment_id)
	) $charset_collate";
	
	$schema[] =  "CREATE TABLE ". WORDTOUR_TRACKS . " (
		track_id bigint(20) AUTO_INCREMENT,
		track_author bigint(20) DEFAULT 0,
		track_publish_date datetime DEFAULT '0000-00-00 00:00:00',
		track_title text NOT NULL,
		track_release_date date DEFAULT '0000-00-00',
		track_artist_id bigint(20) DEFAULT 0,
		track_about longtext,
		track_label text,
		track_buy_links longtext,
		track_credits text,
		track_play_count bigint(20) DEFAULT 0,
		track_lyrics longtext,
		track_lyrics_author text,  
		PRIMARY KEY (track_id)
	) $charset_collate";
	
	$schema[] =  "CREATE TABLE ". WORDTOUR_ALBUMS . " (
		album_id bigint(20) AUTO_INCREMENT,
		album_author bigint(20) DEFAULT 0,
		album_publish_date datetime DEFAULT '0000-00-00 00:00:00',
		album_title text NOT NULL,
		album_order smallint(5) DEFAULT 0,
		album_type varchar(32) DEFAULT 'album',
		album_release_date date DEFAULT '0000-00-00',
		album_artist_id bigint(20) DEFAULT 0,
		album_about longtext,
		album_buy_links longtext,
		album_label text,
		album_credits text,
		album_tracks_status tinyint(1) DEFAULT 1,
		album_similar_status tinyint(1) DEFAULT 0,
		PRIMARY KEY (album_id)
	) $charset_collate";
	
	
	return $schema;
}

function wordtour_install() {
   global $wpdb;
   if($wpdb->get_var("SHOW TABLES LIKE '" . WORDTOUR_EVENTS . "'") != WORDTOUR_EVENTS) {
   		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      	dbDelta(wt_schema_table());
   }	
}

function wordtour_uninstall() {
  global $wpdb;
	$wpdb->query('DROP TABLE IF EXISTS '
	. WORDTOUR_EVENTS . ', '. WORDTOUR_EVENTS_META . ', '. WORDTOUR_ATTENDING . ', '.WORDTOUR_ATTACHMENT . ', '. WORDTOUR_GALLERY . ', '
	. WORDTOUR_ARTISTS . ', '. WORDTOUR_TOUR . ', '. WORDTOUR_SOCIAL . ', '. WORDTOUR_ALBUMS . ', '.WORDTOUR_TRACKS . ', '.WORDTOUR_COMMENTS);
	
	delete_option('wordtour_settings');
	delete_option("wordtour_panel_state");
	delete_option("wordtour_event_type");
	delete_option("wordtour_genre");
}



?>