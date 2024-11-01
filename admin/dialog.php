<?php 
require_once("../../../../wp-load.php");
if(!current_user_can($_wt_options->options("user_role"))) wp_die(__('Cheatin&#8217; uh?'));
require_once("handlers.php");
require_once("template.php");

global $_wt_options;
$options = $_wt_options->options(); 
?>

<?php 
if($_GET["page"] == "import_album-info") {
	
	global $_wt_options;
	$api_key = $_wt_options->options("lastfm_key");
	$artist = $_GET['artist'];
	$msg="";
	$album = $_GET['album']; 

	
	$lastfm_base_url = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=$api_key";
	$lastfm_query = "&artist=".urlencode($artist)."&album=".urlencode($album)."&format=json&autocorrect=1";
	$lastfm_query_url = $lastfm_base_url.$lastfm_query;
	$response = wt_file_get_contents($lastfm_query_url);
	$response = json_decode($response);
	//print_r($response);
	if($response->album) {
		$album = $response->album;
		$release_date_format = "";
		$release_date_format = date('Y-m-d',strtotime(trim($album->releasedate)));	
		if($release_date_format){
			$release_date =  WT_DBPrepere::admin_date_out($release_date_format);	
		}
						
		$title = $album->name;
		$artist = $album->artist;
		$about = $album->wiki ? $album->wiki->content : "";
		$tracks = $album->tracks ? $album->tracks->track : array();
	} else if($response->error) {
		$msg =  $response->message;
	}

?>
<style>
#import-album-dialog .checkbox {
	width:30px;
	margin-top:5px;
	float:left;
	background-color: #ECECEC;
}

#import-album-dialog .checkbox input {
	width:auto;
}

#import-album-dialog .textarea {
	float:left;
	width:90%;
}

#import-album-dialog textarea {
	width:100%;
	height:25px;
}
</style>
<form>
	<div id="dialog-alert" class='wordtour-alert wordtour-alert-error' style='display:<?php echo empty($msg) ? "none" : "block"; ?>'><?php echo $msg ?></div>
	<?php
		 if(!isset($response->error)) {
			 wt_static_panel_start(array()); ?>
			<?php if(!empty($title)) { ?>
			<div class="wordtour-field wordtour-field-block">
				<div class="label">Title</div>
				<div class="ui-helper-clearfix">
					<div class="checkbox">
						<input checked="true" type="checkbox" name="title"></input>
					</div>
					<div class="textarea">
						<textarea readonly="true"><?php echo $title;?></textarea>
					</div>
				</div> 
			</div>
			<?php } ?>
			<?php if(!empty($artist)) { ?>
			<div class="wordtour-field wordtour-field-block">
				<div class="label">Artist</div>
				<div class="ui-helper-clearfix">
					<div class="checkbox">
						<input checked="true" type="checkbox" name="artist"></input>
					</div>
					<div class="textarea">
						<textarea readonly="true"><?php echo $artist;?></textarea>
					</div>
				</div> 	
			</div>
			<?php } ?>
			<?php if(!empty($release_date)) { ?>
			<div class="wordtour-field wordtour-field-block">
				<div class="label">Release Date</div>
				<div class="ui-helper-clearfix">
					<div class="checkbox">
						<input checked="true" type="checkbox" name="date"></input>
					</div>
					<div class="textarea">
						<textarea readonly="true"><?php echo $release_date;?></textarea>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php 
			if(count($tracks)>0) {
			?>
			<div class="wordtour-field wordtour-field-block">
				<div class="label">Tracks</div>
				<?php 
					$track_count = 1;
					foreach($tracks as $track) {
				?>
				<div class="ui-helper-clearfix">
					<div class="checkbox">
						<input checked="true" type="checkbox" name="track"></input>
					</div>
					<div class="textarea">
						<?php echo "<textarea>".$track->name."</textarea>";
							$track_count++
						?>
						
					</div>
				</div>
				<?php 		
					} 
				?>	
			</div>
			<?php } ?>
			<?php if(!empty($about)) { ?>
			<div class="wordtour-field wordtour-field-block">
				<div class="label" style="width:20px;">About</div>
				<div class="ui-helper-clearfix">
					<div class="checkbox">
						<input checked="true" type="checkbox" name="about"></input>
					</div>
					<div class="textarea">
						<textarea style="height:110px;"><?php echo $about?></textarea>
					</div>
				</div>
			</div>
		<?php } 
		}
		?>
		
	<?php wt_static_panel_end(); ?>	
</form>
<?php 
}
?>



<?php
if($_GET["page"] == "comment") {
	$nonce   = wp_create_nonce(WT_Comment::NONCE_UPDATE);
?>

<div id="dialog-alert" class='wordtour-alert'></div>
<form>
	<input type="hidden" name="_comment_nonce"></input>
	<input type="hidden" name="comment_id"></input>
	<input type="hidden" name="comment_user_id"></input>
	<input type="hidden" name="comment_event_id"></input>
	<input type="hidden" name="comment_approved"></input>
	<?php wordtour_comment_details_panel(); ?>	
</form>
<?php 
}
?>

<?php

if($_GET["page"] == "facebook") {
	$nonce   = wp_create_nonce(WT_SOCIAL::NONCE_INSERT);
	$event_id = $_GET["event_id"];
	$event = new WT_Event($event_id);
	$data = $event->retrieve(); 
	$start_time = prepare_facbook_time(strtotime($data["event_start_date"]. ' '.($data["event_start_time"] == "00:00:01" ? "" : $data["event_start_time"])));
	$end_time = prepare_facbook_time(strtotime($data["event_end_date"]. ' '.($data["event_end_time"] == "00:00:01" ? "" : $data["event_end_time"]))); 
	$location = $data["venue_name"];
	$address = $data["venue_address"];
	$city = $data["venue_city"];
	$state = get_state_by_code($data["venue_state"]);
	$country = get_country_by_code($data["venue_country"]);
	
	if($options["facebook_status_template"]) {
		$tpl = array();
		foreach($event->template($data) as $key=>$value) {
			$tpl["%".$key."%"] = $value;
		}	
		$status = strtr($options["facebook_status_template"],$tpl);
	};
?>
	<div id="dialog-alert" class='wordtour-alert'></div>
	<form>
		<input type="hidden" name ="_nonce" value="<?php echo $nonce;?>"></input>
		<input type="hidden" name ="event_id" value="<?php echo $event_id;?>"></input>
		<input type="hidden" name ="start_time" value="<?php echo $start_time;?>"></input>
		<input type="hidden" name ="end_time" value="<?php echo $end_time;?>"></input>
		<input type="hidden" name ="location" value="<?php echo $location;?>"></input>
		<input type="hidden" name ="address" value="<?php echo $address;?>"></input>
		<input type="hidden" name ="city" value="<?php echo $city;?>"></input>
		<input type="hidden" name ="state" value="<?php echo $state;?>"></input>
		<input type="hidden" name ="country" value="<?php echo $country;?>"></input>
	
	<?php wt_static_panel_start(array()); ?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">What do you want to publish on Facebook</div>
			<div>
				<select id="fb-dialog-mode" style="font-size:14px;">
					<option value="status">Profile Status</option>
					<option value="event">New Event</option>
				</select>
			</div>	
		</div>
	<?php wt_static_panel_end(); ?>
	
	<?php wt_static_panel_start(array()); ?>
	<div id="fb-dialog-event" style="display:none;">
	<?php 
	$fb= get_option("wt_facebook"); 
	if($fb) {
		$fb = unserialize($fb);
		if(isset($fb[$event->id])) {
			if(isset($fb[$event->id]["facebook_event_id"])) {
				render_form_empty("","","Facebook ID","<a style='color:##21759B;' href='http://www.facebook.com/event.php?eid=".$fb[$event->id]["facebook_event_id"]."' target='_blank'>Published</a>") ;
			}	
		}
	}
	?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Title*</div>
			<div>
				<textarea name="title" style="height:70px;"></textarea>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Description</div>
			<div>
				<textarea name="description" style="height:120px;"></textarea>
			</div>	
		</div>
	</div>
	<div id="fb-dialog-status">
	<?php 
	$fb= get_option("wt_facebook"); 
	if($fb) {
		$fb = unserialize($fb);
		if(isset($fb[$event->id])) {
			if(isset($fb[$event->id]["status_last_update"]))
			$last_update = $fb[$event->id]["status_last_update"];
			$date_format = date_i18n($_wt_options->options("date_format")." ".get_option("time_format"),$last_update);
			render_form_text("","","Last Update",$date_format) ;	
		}
	}
	?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Status*</div>
			<div>
				<textarea name="status" style="height:70px;"><?php echo $status;?></textarea>
			</div>	
		</div>
	</div>
	<?php wt_static_panel_end(); ?>
	
	<?php wt_dynamic_panel_start(array("title"=>"History"),"",1); ?>
		<div>
			<?php 
				$social = new WT_Social();
				$social_status_history = $social->query("social_parent_id=$event_id&social_parent_type=event&social_type=fbstatus&order=social_publish_time&limit=5",WORDTOUR_SOCIAL);
				$social_event_history = $social->query("social_parent_id=$event_id&social_parent_type=event&social_type=fbevent&order=social_publish_time",WORDTOUR_SOCIAL);
				
				if($social_event_history) {
					foreach($social_event_history as $e) {
						$e = $social->db_out($e);
						echo "<div style='border-bottom: 1px solid #EEEEEE;padding:5px;'>Event created on $e[social_publish_time] <a style='color:#21759B;' href='http://www.facebook.com/event.php?eid=$e[social_ref_id]' target='_blank'>Link to Event</a></div>";	
					}
				}
				
				if($social_status_history) {
					foreach($social_status_history as $h) {
						$h = $social->db_out($h);
						echo "<div style='border-bottom: 1px solid #EEEEEE;padding:5px;'>Status was updated on ".$h["social_publish_time"]."</div>";	
					}
				}
				
				if(!$social_event_history && !$social_status_history) {
					echo "No History";
				}
			?>
			
		</div>
	<?php wt_dynamic_panel_end(); ?>
</form>
<?php }?>

<?php

if($_GET["page"] == "post") {
	$event = new WT_Event($_GET["event_id"]);
	$renderer = new WT_Renderer();
	$event_id = $_GET["event_id"];
	$nonce   = wp_create_nonce(WT_Post::NONCE_INSERT);
	$title = "";	
?>
<form>
	<div id="dialog-alert" class='wordtour-alert'></div>
	<input type="hidden" name="_post_nonce" value="<?php echo $nonce;?>"></input>
	<input type="hidden" name="post_event_id" value="<?php echo $event_id;?>"></input>
	<?php wt_static_panel_start(array()); ?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Title</div>
			<div>
				<input type="text" name="post_title" value="<?php echo $title;?>"></input>	
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Content</div>
			<div>
				<input type="text" disabled="true" value="[wordtour_event id='<?php echo $event_id;?>']"></input>	
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Result</div>
			<div>
				<textarea disabled="true" style="height:120px;"><?php echo $renderer->event((array("id"=>$event_id)));?></textarea>
				<p class="howto" style='font-size:10px;'>To change event display layout, edit file name "post.event.tpl.php"</i><br/>
				
				</p>	
			</div>	
		</div>
	<?php wt_static_panel_end(); ?>
	
	<?php wt_dynamic_panel_start(array("title"=>"History"),"",1); ?>
		<div>
			<?php 
				$social = new WT_Social();
				$social_status_history = $social->query("social_parent_id=$event_id&social_parent_type=event&social_type=post&order=social_publish_time&limit=5",WORDTOUR_SOCIAL);
				if($social_status_history) {
					foreach($social_status_history as $h) {
						$h = $social->db_out($h);
						echo "<div style='border-bottom: 1px solid #EEEEEE;padding:5px;'>Post was added on ".$h["social_publish_time"]." <a href='".admin_url("post.php?action=edit&post=$h[social_ref_id]")."' target='_blank'>Edit Post</a></div>";	
					}
				} else {
					echo "No History";	
				}
			?>
			
		</div>
	<?php wt_dynamic_panel_end(); ?>	
</form>
<?php 
}

if($_GET["page"] == "import_eventbrite") {
?>
<form>
	<div id="dialog-alert" class='wordtour-alert'></div>
	<?php wt_static_panel_start(array()); ?>
		<input type='hidden' name="eventbrite_event_id" value='<?php echo $event_id;?>'></input>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Select artist for imported events</div>
			<div>
				<select name='artist_id'>
					<option value="">-- Select Artist --</option>
					<?php 
						$artists = WT_Artist::all(null,"artist_id,artist_name");
						foreach ($artists as $artist) {
							echo "<option value='$artist[artist_id]'>$artist[artist_name]</option>";	
						}
					?>
				</select>	
			</div>	
		</div>
		
	<?php wt_static_panel_end(); ?>	
</form>
<?php 
}

if($_GET["page"] == "eventbrite") {
	$event = new WT_Event($_GET["event_id"]);
	$event->retrieve(); 
	$data = $event->db_out(null,0);
	$event_id = $_GET["event_id"];
	//$nonce   = wp_create_nonce(WT_Post::NONCE_INSERT);
	$title = "";	
?>
<form>
	<div id="dialog-alert" class='wordtour-alert'></div>
	<?php wt_static_panel_start(array()); ?>
		<input type='hidden' name="eventbrite_event_id" value='<?php echo $event_id;?>'></input>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Title</div>
			<div>
				<input type="text" name="eventbrite_title" value="<?php echo $data["event_title"];?>"></input>	
			</div>	
		</div>
		
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Description</div>
			<div>
				<textarea style='height:100px;' name="eventbrite_description"><?php echo $data["event_notes"];?></textarea>	
			</div>	
		</div>
		
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Privacy</div>
			<div>
				<select name="eventbrite_privacy">
					<option value="1" <?php echo $data["event_published"] == 1 ? "selected='true'":"" ;?>>Public</option>
					<option value="0" <?php echo $data["event_published"] == 0 ? "selected='true'":"" ;?>>Private</option>
				</select>	
			</div>	
		</div>
			<div class="wordtour-field wordtour-field-block">
			<div class="label">Status</div>
			<div>
				<select name="eventbrite_status">
					<option value="draft">Draft</option>
					<option value="live">Live</option>
					<option value="canceled" <?php echo $data["event_status"] == "cancelled" ? "selected='true'":"" ;?>>Canceled</option>
					<option value="deleted">Deleted</option>
				</select>	
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Personalize the link for your event</div>
			<div>
				http://<input type="text" name="eventbrite_personalized_url" value="" style='width:40%;'></input>.eventbrite.com		
			</div>	
		</div>
		
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Start Date</div>
			<div>
				<?php echo $data["event_start_date"]. " ".$data["event_start_time"];?>	
			</div>	
		</div>
		
		<?php if(!empty($data["event_end_date"]) || !empty($data["event_end_date"])) {?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">End Date</div>
			<div>
				<?php echo $data["event_end_date"]." ".$data["event_end_date"];?>	
			</div>	
		</div>
		<?php }?>
		
		<?php if(!empty($data["event_end_date"])) {?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">End Date</div>
			<div>
				<?php echo $data["event_end_date"];?>	
			</div>	
		</div>
		<?php }?>
		
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Venue</div>
			<div>
				<?php echo $data["venue_name"];?>	
			</div>	
		</div>
		
		
	<?php wt_static_panel_end(); ?>
	
	<?php wt_dynamic_panel_start(array("title"=>"History"),"",1); ?>
		<div>
			<?php 
				$social = new WT_Social();
				$social_status_history = $social->query("social_parent_id=$event_id&social_parent_type=event&social_type=ebevent&order=social_publish_time&limit=5",WORDTOUR_SOCIAL);
				if($social_status_history) {
					foreach($social_status_history as $h) {
						$h = $social->db_out($h);
						echo "<div style='border-bottom: 1px solid #EEEEEE;padding:5px;'>Post was added on ".$h["social_publish_time"]." <a href='".admin_url("post.php?action=edit&post=$h[social_ref_id]")."' target='_blank'>Edit Post</a></div>";	
					}
				} else {
					echo "No History";	
				}
			?>
			
		</div>
	<?php wt_dynamic_panel_end(); ?>	
</form>
<?php 
}

if($_GET["page"] == "all_countries") {
	$countries = get_countries(); 
?>
	<div class="wordtour-selectable">
		
		<?php 
		foreach($countries as $code=>$name) {
			echo "<a class='ui-selectee' href='$name'><strong>$name</strong></a>";	
		}
		?>
	</div>	
	
<?php 
};

if($_GET["page"] == "all_type") {
	$event_type = get_all_event_type(); 
?>
	<div class="wordtour-selectable">
		
		<?php 
		foreach($event_type as $type) {
			$type = ucwords($type);
			echo "<a class='ui-selectee' href='#'><strong>$type</strong></a>";	
		}
		?>
	</div>	
	
<?php 
};

if($_GET["page"] == "all_genre") {
	$genres = wordtour_get_all_genre(); 
?>
	<div class="wordtour-selectable">
		
		<?php 
		foreach($genres as $genre) {
			$genre = ucwords($genre);
			echo "<a class='ui-selectee' href='#'><strong>$genre</strong></a>";	
		}
		?>
	</div>	
	
<?php 
};

if($_GET["page"] == "twitter") {
	$event = new WT_Event($_GET["event_id"]);
	$data = $event->retrieve();
	$event_id = $_GET["event_id"];
	$nonce   = wp_create_nonce(WT_SOCIAL::NONCE_INSERT);
	if($options["twitter_template"]) {
		$tpl = array();
		foreach($event->template($data) as $key=>$value) {
			$tpl["%".$key."%"] = $value;
		}	
		$status = strtr($options["twitter_template"],$tpl);
	};	
?>
<form>
	<div id="dialog-alert" class='wordtour-alert'></div>
	<input type="hidden" name="_twitter_nonce" value="<?php echo $nonce;?>"></input>
	<input type="hidden" name="twitter_event_id" value="<?php echo $event_id;?>"></input>
	<?php wt_static_panel_start(array()); ?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Status *</div>
			<div>
				<textarea name="twitter_status" id="twitter_status"><?php echo $status;?></textarea>	
			</div>	
		</div>
	<?php wt_static_panel_end(); ?>
	
	<?php wt_dynamic_panel_start(array("title"=>"History"),"",1); ?>
		<div>
			<?php 
				$social = new WT_Social();
				$social_status_history = $social->query("social_parent_id=$event_id&social_parent_type=event&social_type=twitter&order=social_publish_time&limit=5",WORDTOUR_SOCIAL);
				if($social_status_history) {
					foreach($social_status_history as $h) {
						$h = $social->db_out($h);
						echo "<div style='border-bottom: 1px solid #EEEEEE;padding:5px;'>Status was updated on ".$h["social_publish_time"]."</div>";	
					}
				} else {
					echo "No History";	
				}
			?>
			
		</div>
	<?php wt_dynamic_panel_end(); ?>	
</form>
<?php 
}

if($_GET["page"] == "all_countries") {
	$countries = get_countries(); 
?>
	<div class="wordtour-selectable">
		
		<?php 
		foreach($countries as $code=>$name) {
			echo "<a class='ui-selectee' href='$name'><strong>$name</strong></a>";	
		}
		?>
	</div>	
	
<?php 
};

if($_GET["page"] == "all_states") {
	$states = get_states(); 
?>
	<div class="wordtour-selectable">
		
		<?php 
		foreach($states as $code=>$name) {
			echo "<a class='ui-selectee' href='$name'><strong>$name</strong></a>";	
		}
		?>
	</div>	
	
<?php 
};

if($_GET["page"] == "all_venues") {
	$venues = WT_Venue::all(); 
	$venueObj = new WT_Venue();
?>
	<div class="wordtour-selectable">
		<?php 
		foreach($venues as $value) {
			$venue = $venueObj->db_out($value);
			$address = array();
			if(!empty($venue["venue_city"])) $address[] =  $venue["venue_city"];
			if(!empty($venue["venue_state"])) $address[] =  $venue["venue_state"];
			if(!empty($venue["venue_country"])) $address[] =  $venue["venue_country"];
			
			echo "<a class='ui-selectee' href='#'><strong>$venue[venue_name]</strong><br/><small>".implode(",",$address)."</small></a>";	
		}
		?>
	</div>	
	
<?php 
};

if($_GET["page"] == "all_artists") {
	$artists = WT_Artist::all(); 
	$artistObj = new WT_Artist();
?>
	
	<div class="wordtour-selectable">
		<?php 
		foreach($artists as $value) {
			$artist = $artistObj->db_out($value);
			echo "<a class='ui-selectee' href='#'><strong>$artist[artist_name]</strong></a>";	
		}
		?>
	</div>	
	
<?php 
}

if($_GET["page"] == "all_tour") {
	$tours = WT_Tour::all(); 
	$tourObj = new WT_Tour();
?>
	
	<div class="wordtour-selectable">
		<?php 
		foreach($tours as $value) {
			$tour = $tourObj->db_out($value);
			echo "<a class='ui-selectee' href='#'><strong>$tour[tour_name]</strong></a>";	
		}
		?>
	</div>	
	
<?php 
}

if($_GET["page"] == "venues") {
	$is_default = ($_wt_options->options("default_venue") == $venue_id) ? 1 : 0 ; 
?>
<div id="dialog-alert" class='wordtour-alert'></div>
<form>
	<input type="hidden" name="_venue_nonce"></input>
	<input type="hidden" name="venue_id"></input>
	<?php 
	wordtour_venue_details_panel(null,0);
	wordtour_venue_order_panel(1);
	wordtour_venue_status_panel();
	?>
</form>

<?php 
}

if($_GET["page"] == "albums") { 
?>
<div id="dialog-alert" class='wordtour-alert'></div>
<form>
	<input type="hidden" name="_album_nonce"></input>
	<input type="hidden" name="album_id"></input>
	<?php 
	wordtour_album_details_panel(null,0);
	wordtour_album_status_panel(1);
	wordtour_album_order_panel(1);
	?>
</form>

<?php 
}

if($_GET["page"] == "tracks") { 
?>
<div id="dialog-alert" class='wordtour-alert'></div>
<form>
	<input type="hidden" name="_track_nonce"></input>
	<input type="hidden" name="track_id"></input>
	<?php 
	wordtour_track_details_panel(null,0);
	wordtour_track_more_panel(1);
	?>
</form>

<?php 
}
?>

<?php
if($_GET["page"] == "events") {
?>
<div id="dialog-alert" class='wordtour-alert'></div>
<form>
	<input type="hidden" name="_event_nonce"></input>
	<input type="hidden" name="event_id"></input>
	<?php 
	wordtour_event_details_panel(null,0);
	wordtour_event_title_panel(1);
	wordtour_event_status_panel();
	?>
</form>
<?php 
}
?>

<?php
if($_GET["page"] == "artists") { 
?>
<div id="dialog-alert" class='wordtour-alert'></div>
<form>	
	<input type="hidden" name="_artist_nonce"></input>
	<input type="hidden" name="artist_id"></input>
	<?php 
	wordtour_artist_details_panel(null,0);
	wordtour_artist_order_panel(1);
	wordtour_artist_status_panel();
	?>
</form>

<?php 
}
?>

<?php
if($_GET["page"] == "tour") {
?>
<div id="dialog-alert" class='wordtour-alert'></div>
<form>	
	<input type="hidden" name="_tour_nonce"></input>
	<input type="hidden" name="tour_id"></input>
	<?php 
	wordtour_tour_details_panel(null,0);
	wordtour_tour_order_panel(1);
	wordtour_tour_status_panel();
	?>
</form>
<?php 
}
?>

<?php
if($_GET["page"] == "gallery") {
	$gallery_id = $_GET["id"];
	$nonce   = $gallery_id ? wp_create_nonce(WT_Gallery::NONCE_UPDATE) : wp_create_nonce(WT_Gallery::NONCE_INSERT);
?>

<div id="dialog-alert" class='wordtour-alert'></div>
<form id="wordtour-gallery-dialog-form">
	<input type="hidden" name="_gallery_nonce"></input>
	<input type="hidden" name="gallery_id"></input>
	<div class="dialog-tabs">
		<ul>
			<li><a href='#wordtour-gallery-dialog-details'>Details</a></li>
			<li><a href='#wordtour-gallery-dialog-search'>Search Media Library</a></li>
		</ul>
		
		<div id="wordtour-gallery-dialog-details">
			<?php wt_static_panel_start();?>
				<div class="wordtour-field">
					<div class="label">Name*</div>
					<div class="field field-large field-full-width" >
						<input type="text" name="gallery_name" id="gallery_name"></input>
					</div>	
				</div>
			<?php wt_static_panel_end();?>
			<div class="wordtour-thumbnailmanager-wrap"></div>
		</div>
		<div id="wordtour-gallery-dialog-search">
			
		</div>
	</div>
</form>

<?php 
}
?>
