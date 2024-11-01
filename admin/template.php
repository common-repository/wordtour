<?php
// PANELS
function wt_static_panel_start($attr = array(),$panel_order=""){
	extract($attr);
	$panel_id = $id ? "id='$id'" : ""; 
	
	echo "<div $panel_id class='wordtour-panel wordtour-static-panel'>
			<input type='hidden' class='panel' value='$panel_order'>
			<div class='wordtour-panel-content'>";	
	
}

function wt_static_panel_end(){
	echo "</div></div>";		
}

function wt_dynamic_panel_start($attr,$panel_order="",$collapsed = 0){
	extract($attr);
	$panel_id = $id ? "id='$id'" : "";
	$panel_title = $title ? $title : "" ;
	$collapsed = $collapsed ? "" : "style='display:none'";
	echo "<div $panel_id class='wordtour-panel wordtour-dynamic-panel'>
			<input type='hidden' class='panel' value='$panel_order'>
			<div class='wordtour-panel-header wordtour-panel-hndl-header ui-helper-clearfix'>
				<div class='wordtour-panel-button'></div>
				<h3 class='wordtour-panel-title'>$panel_title</h3>
			</div>
			<div class='wordtour-panel-content' $collapsed>";
}

function wt_dynamic_panel_end(){
	echo "</div></div>";
}




function render_page_msg($type,$msg) {
	if($type && $msg) echo "<div class='wordtour-alert wordtour-alert-$type' style='display:block;margin-left:0px;margin-right:0px;'>$msg</div>";	
}

function render_form_slider($name='',$id='',$label='',$value='',$required=0){
	$req = $required && !empty($name) ? "required='true'" : "" ;
	
	$mark = $required ? "*" : "" ;
	
	echo "<div class='dialog-field'>
			<label style='float:left;' for='$name'>$label$mark</label>
			<div style='float:left;width:300px;padding:7px;' class='input-wrap'>
				<input type='hidden' name='$name' value='$value'/>
				<div id='$id'></div>
				<div style='font-size:10px;color:#000000;padding-top:5px;'>Rating: <span class='rating-text'>$value</span></div>
			</div>
			<div style='clear:both;'></div>
		</div>";
}

function render_form_thumbnail($name,$id,$thumbnail_id = 0,$thumbnail_src = ""){
	echo "<input id='$id' type='hidden' value='$thumbnail_src'></input>";
	render_form_hidden($name,"",$thumbnail_id);	
}


function render_form_input($name='',$id='',$label='',$value='',$required=0){
	$req = $required && !empty($name) ? "required='true'" : "" ;
	
	$mark = $required ? "*" : "" ;
	$html =  "<div class='dialog-field'>
			<label for='$name'>$label$mark</label>
			<span class='input-wrap'>
				<input $req id='$id' name='$name' type='text' size='40' value='".esc_attr($value)."'/>
			</span>
		</div><div style='clear:both;'></div>";
	//echo "<textarea>$html</textarea>";
	echo $html;
}

function render_form_password($name='',$id='',$label='',$value='',$required=0){
	$req = $required && !empty($name) ? "required='true'" : "" ;
	
	$mark = $required ? "*" : "" ;
	$html =  "<div class='dialog-field'>
			<label for='$name'>$label$mark</label>
			<span class='input-wrap'>
				<input $req id='$id' name='$name' type='password' size='40' value='".esc_attr($value)."'/>
			</span>
		</div><div style='clear:both;'></div>";
	//echo "<textarea>$html</textarea>";
	echo $html;
}

function render_form_text($name='',$id='',$label='',$value=''){
	$mark = $required ? "*" : "" ;
	$html =  "<div class='dialog-field'>
			<label for='$name'>$label$mark</label>
			<span class='input-wrap'>
				<input id='$id' name='$name' type='text' size='40' value='".esc_attr($value)."' style='border:0px;'/>
			</span>
		</div><div style='clear:both;'></div>";
	echo $html;
}

function render_form_empty($name='',$id='',$label='',$value=''){
	$mark = $required ? "*" : "" ;
	$html =  "<div class='dialog-field'>
			<label for='$name'>$label$mark</label>
			<span class='input-wrap'>
				$value
			</span>
		</div><div style='clear:both;'></div>";
	echo $html;
}

function render_form_textarea($name='',$id='',$label='',$value='',$required=0){
	$req = $required && !empty($name) ? "required='true'" : "" ;
	
	$mark = $required ? "*" : "" ;
	
	echo "<div class='dialog-field'>
			<label for='$name'>$label$mark</label>
			<span class='input-wrap'>
				<textarea $req id='$id' rows='8' cols='38' name='$name'>$value</textarea>
			</span>
		</div><div style='clear:both;'></div>";
}

function render_form_checkbox($name='',$id='',$label='',$value='',$checked = 0){
	$checked = $checked ? "checked=true" : "";
	echo "<div class='dialog-field'>
			<label for='$name'>$label$mark</label>
			<span class='input-wrap'>
				<input $checked id='$id' name='$name' type='checkbox' style='width:auto;' value='$value'/>
			</span>
		</div><div style='clear:both;'></div>";
}

function render_form_radio($name='',$label='',$radio=array(),$value = 0){
	$radio_markup = "";
	foreach($radio as $r){
		$checked = ($r["value"] == $value) ? "checked=true" : "";
		$radio_markup.="<input style='width:auto;' type='radio' name='$name' value='".$r["value"]."' $checked> ".$r["text"];	
	}
	echo "<div class='dialog-field'>
			<label for='$name'>$label$mark</label>
			<span class='input-wrap'>
				$radio_markup
			</span>
		</div><div style='clear:both;'></div>";
}

function render_form_hidden($name='',$id='',$value='',$required=0){
	$req = $required ? "required='true'" : "" ;
	
	echo "<input $req id='$id' name='$name' type='hidden' value='$value'/>";
}


function render_form_select($name='',$id='',$label='',$values=array(),$selected='',$required=0){
	$req = $required ? "required='true'" : "" ;
	$mark = $required ? "*" : "" ;
	
	$html = '';
	$html.=	"<div class='dialog-field'>" ;
	$html.= 	"<label for='$name'>$label$mark</label>";
	$html.=		"<select $req id='$id' name='$name'>";
				foreach($values as $value=>$text) {
					$is_selected = $selected == $value ? "selected='true'" : "";
					$html.="<option $is_selected value='$value'>$text</option>";
				}
	$html.=		"</select>";
	$html.=	"</div>";
	
	echo $html ;
}


function get_event_date_string($start_date,$start_time,$end_date,$end_time) {
	global $_wt_options;
	$date_format =get_option('date_format');//$_wt_options->options("admin_date_format");
	if($start_time !== "00:00:01" && !empty($start_time)) {
		$start_time = date_i18n(get_option('time_format') ,strtotime($start_time));
	} else {
		unset($start_time);
	}
		
	if($end_time !== "00:00:01" && !empty($end_time)) {
		$end_time = date_i18n(get_option('time_format') ,strtotime($end_time));
	} else {
		unset($end_time);
	}
		
	if($start_date !== "0000-00-00" && !empty($start_date)) {
		$start_date = date_i18n($date_format,strtotime($start_date));
	} else {
		unset($start_date);
	}
		
	if($end_date !== "0000-00-00" && !empty($end_date)) {
		$end_date = date_i18n($date_format,strtotime($end_date));
	} else {
		unset($end_date);
	}
	
	$start_display = array();
	if(isset($start_date)) array_push($start_display,$start_date);
	if(isset($start_time)) array_push($start_display,$start_time); 
	
	
	if($end_date && $end_date!==$start_date || ($end_date===$start_date && isset($end_time))) {
		$end_display = array();
		if(isset($end_date)) array_push($end_display,$end_date);
		if(isset($end_time)) array_push($end_display,$end_time);
	} else {
		$end_display = false ;
	}
	 
	return array("start_date" =>$start_display,"end_date"   =>$end_display);
}

function get_event_row_html($row) {
	if($row) {
		$start_date = $row["event_start_date"];//get_event_date_string($row["event_start_date"],$row["event_start_time"],$row["event_end_date"],$row["event_end_time"]);
		if(!empty($row["event_start_time"])) $start_date.= "@$row[event_start_time]"; 
		$end_date = $row["event_end_date"];
		if($row["event_start_date"] == $row["event_end_date"] && empty($row["event_end_time"])) {
			$end_date = "";
		}
		if(!empty($row["event_end_time"])) $end_date.= "@$row[event_end_time]";
		//$end_date = 
		$event_published = $row["event_published"] == "0" ? false : true ;
		$event_id = $row["event_id"];
		$tr_class = (!$event_published) ? "style='background-color:#ffd37d;'" : "";		
		$status = ($event_published) ? ucwords($row["event_status"]) : ucwords("not published");
		$facebook_class  = "page-facebook:event_id-$event_id";
		$twitter_class  = "page-twitter:event_id-$event_id";
		$unpublish_class  = "action-unpublish_event:event_id-$event_id:_nonce-".wp_create_nonce(WT_Event::NONCE_UNPUBLISH);
		$publish_class  = "action-publish_event:event_id-$event_id:_nonce-".wp_create_nonce(WT_Event::NONCE_PUBLISH);
		$delete_class  = "action-delete_event:event_id-$event_id:_nonce-".wp_create_nonce(WT_Event::NONCE_DELETE);
		$edit_class    = "event_id-$event_id";
		$edit_link = admin_url("admin.php?page=wordtour/navigation.php&action=edit&event_id=".$row["event_id"]);
		
		$artists = array();
		if(is_array($row["event_more_artists"])) {
			foreach($row["event_more_artists"] as $a) {
				$artists[] = $a["name"];
			}
		}
		$artists = implode(", ",$artists);
	
	$html = "<tr $tr_class>
				<th class='check-column'>
					<input type='checkbox' value='$row[event_id]'/>
				</th>
				<td>$event_id</td>
				<td>		
					<div style='width:400px;'>
					<a href='$edit_link' style='font-weight:bold;'>";
					if(!empty($row["event_title"])) {
			$html.= "<span style='font-size:12px;'>$row[event_title]</span><br/>";
					}
				$html.= "$start_date";
			 	if(!empty($end_date)) {
					$html.= "<b> - $end_date</b>";
				}
		
	$html.=	 		"</a></div>
						<div class='row-actions'>
							<span>
								<a title='View Event' href='".wt_get_permalink("event",$event_id,array("%date%"=>$row["event_start_sql"],"%name%"=>$row["venue_name"]))."'>View</a> |
							</span>
							<span class='quickedit'>
								<a title='Quick Edit this event' class='$edit_class' href='#'>Quick Edit</a> |
							</span>
							<span>
								<a title='Edit this event' href='$edit_link'>Edit</a> |
							</span>";
					if(!$event_published) {
						$html.= "<span class='publish'>
									<a title='Publish Event' class='$publish_class' href='$publish_class'>Publish</a> |
								 </span>";
					} else {
						$html.=	"<span class='unpublish'>
									<a title='Unpublish Event' class='$unpublish_class' href='#'>Unpublish</a> | 
								</span>";
					}
							
						$html.=	"<span class='delete'>
								<a title='Delete this event permanently' class='$delete_class' href='#'>Delete</a>
							</span>";
	
			
		$html.= "</div>
			</td>
			<td>"; 
			$venue_display = array();
			if(!empty($row["venue_city"])) array_push($venue_display,$row["venue_city"]);
			if(!empty($row["venue_state"])) array_push($venue_display,get_state_by_code($row["venue_state"]));
			if(!empty($row["venue_country"])) array_push($venue_display,get_country_by_code($row["venue_country"]));
		$html.= $row["venue_name"]."<br><span style='color:#999999' title='".implode(", ",$venue_display)."'>".get_country_by_code($row["venue_country"])."</span>";
					
		$html.= "</td>
				<td>$status</td>
				<td>$row[artist_name]</td>
				<td>$artists</td>
				<td>$row[tour_name]</td>
				<td>
					<a href='".admin_url("admin.php?page=wt_comments&e=".$row["event_id"])."'>";
						$comment_total = get_comment_total_by_event($row["event_id"]); 
						$html .= "$comment_total[total] Comments
					</a>
					| $row[rsvp_count] Attending</td>
			</tr>";

		return $html;
	}
}


function event_rows($rows) {
	global $wpdb ;
	if(!$rows) { 
		?>
		<tr class="empty">
			<td colspan="5"><p>No Events Found</p></td>
		</tr> 
	<?php
		return ;	
	} else {
	?>
	<?php
		$event = new WT_Event();
		wt_set_event_more_artists($rows);
		foreach($rows as $row) {
			if(empty($row["artist_name"])) {
				$event->id = $row["event_id"];
				$event->retrieve();
				$row = $event->date; 	
			}
			echo get_event_row_html($event->db_out($row));
		}
	}
}


function get_venue_row_html($row) { 
	global $wpdb, $_wt_options ;
	$row = (array) $row ;
	$venue_id = $row["venue_id"] ;
	$venue_name = $row["venue_name"];
	$num_of_events = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_EVENTS." WHERE event_venue_id=".$row["venue_id"]);
	$is_default = $_wt_options->options("default_venue") == $venue_id ? 1 : 0 ;
	$delete_class  = "action-delete_venue:venue_id-$venue_id:_nonce-".wp_create_nonce(WT_Venue::NONCE_DELETE);
	$edit_class    = "venue_id-$venue_id";
	$default_class    = "action-default_venue:venue_id-$venue_id";
	$notdefault_class    = "action-remove_default_venue:venue_id-$venue_id";
	$map_class    = $edit_class;
	$edit_link = admin_url("admin.php?page=wt_venues&action=edit&venue_id=".$row["venue_id"]);
	$delete = "" ;
	
	$html = "<tr class='".($is_default? "tr-default" : "")."'>
				<th class='check-column'>";
				if($num_of_events==0) {
					$html.="<input type='checkbox' value='$venue_id'/>";
				}
	$html.=		"</th>
				<td>$venue_id</td>
				<td>$row[venue_order]</td>
				<td>
					<strong class='edit'><a title='Edit' href='$edit_link' class='row-title'>$venue_name ".($is_default ? "<span class='is-default'>(Default)</span>" : " ")."</a></strong>
					<br>
					<span class='edit'>
						<a title='View Venue' href='".wt_get_permalink("venue",$venue_id,array("%name%"=>$venue_name))."'>View</a> |
					</span>
					
					<span class='quickedit'>
						<a class='$edit_class' title='Quick edit this venue' href='#'>Quick Edit</a> |
					</span>
					<span>
						<a title='Edit this venue' href='$edit_link'>Edit</a>
					</span> | ";
		if($num_of_events==0) {
		$html.=			"<span class='delete'>
							<a title='Delete this venue' class='$delete_class' href='#'>Delete</a>
						</span> | ";
		}
		$html .= 		"<span class='setdefault'><a title='Set as default' class='$default_class' href='#'>Set as Default</a></span>				
						<span  class='removedefault'><a title='Remove default' class='$notdefault_class' href='#'>Remove Default</a></span>
				</td>
				<td><a href='".admin_url("admin.php?page=wordtour/navigation.php&venue=$venue_id")."'>$num_of_events</a></td>
				<td>$row[venue_address]</td>
				<td>$row[venue_city]</td>
				<td>$row[venue_state]</td>
				<td>$row[venue_country]</td>
			</tr>";			
	return $html ;		
}


function venue_rows($rows) {
	global $wpdb ;
	if(!$rows) { ?>
		<tr class="empty">
			<td colspan="5"><p>No Venues Found</p></td>
		</tr> 
	<?php
		return ;	
	} else {
	?>
	<?php
		$venue = new WT_Venue();
		foreach($rows as $row) {
			echo get_venue_row_html($venue->db_out($row));
		}
	}
}

function get_comment_row_html($row) { 
	global $wpdb,$_wt_options;
	$row = (array) $row ;
	
	
	$comment_id = $row["comment_id"];
	$author = $row["comment_author"];
	$email = $row["comment_author_email"];
	$content = $row["comment_content"];
	$date = $row["comment_date"];
	$event_id = $row["comment_event_id"];
	
	$comment_total = get_comment_total_by_event($event_id);
	$total = $comment_total["total"];
	$pending = $comment_total["total_pending"];
	
	$status = $row["comment_approved"] ? "approved" : "unapproved";
	$user_id = $row["comment_user_id"] ;
	$avatar = get_avatar($user_id,32);
	///$event_title = get_event_title($event_id);
	$event = new WT_Event($event_id);
	$event->retrieve();
	$event_data = $event->db_out();
	
	$response_to = "$event_data[event_start_date] at the $event_data[venue_name]"; 
	$unapprove_class = "action-unapprove_comment:comment_id-$comment_id:event_id-$event_id:_nonce-".wp_create_nonce(WT_Comment::NONCE_UNAPPROVE); 
	$edit_class = "comment_id-$comment_id";
	$approve_class = "action-approve_comment:comment_id-$comment_id:event_id-$event_id:_nonce-".wp_create_nonce(WT_Comment::NONCE_APPROVE); 
	$delete_class  = "action-delete_comment:comment_id-$comment_id:event_id-$event_id:_nonce-".wp_create_nonce(WT_Comment::NONCE_DELETE);
	
	$html = "<tr class='$status'>
				<th class='check-column' scope='row'>
					<input type='checkbox' value='$comment_id'/>
				</th>
				<td class='author column-author'>
					<strong>$avatar $author</strong>
				</td>
				<td class='comment column-comment'>
					<div id='submitted-on'>Submitted on <a href='#'>$date</a></div>
					<p><b>$title</b><br/>$content</p>
					<div class='row-actions' id='comment-row-actions'>
						<span class='approve'>
							<a title='Approve this comment' href='#' class='$approve_class'>Approve</a> | 
						</span>
						<span class='unapprove'>
							<a title='Unapprove this comment' class='$unapprove_class' href='#'>Unapprove</a> | 
						</span>
						
						<span class='quickedit'> 
							<a class='$edit_class' title='Edit comment' href='#'>Edit</a>
						</span>
						<span class='delete'> | 
						<a class='$delete_class' href='#'>Delete Permanently</a>
						</span>
					</div>
				</td>
				<td class='response column-response'>
					<div class='response-links'>
						<span class='post-com-count-wrapper'>
							<a href='".admin_url("admin.php?page=wordtour/navigation.php&action=edit&event_id=")."$event_id'>$response_to<a/><br/>
							<a href='".admin_url("admin.php?page=wt_comments")."&e=$event_id' class='post-com-count'>
								<span class='comment-count'>$total</span>
							</a>
						</span>
					</div>
				</td>
			</tr>";			
	return $html ;		
}


function comments_rows($rows,$type="") {
	global $wpdb ;
	if(!$rows) { ?>
		<tr class="empty">
			<td colspan="5"><p>No Comments Found</p></td>
		</tr> 
	<?php
		return ;	
	} else {
	?>
	<?php
		$comment = new WT_Comment();
		foreach($rows as $row) {
			echo get_comment_row_html($comment->db_out($row),$type);
		}
	}
}

function get_artist_row_html($row) { 
	global $wpdb,$_wt_options ;
	$row = (array) $row ;
	$artist_id = $row["artist_id"] ;
	$artist_order = $row["artist_order"] ;
	$artist_name = $row["artist_name"];
	$num_of_events = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_EVENTS." WHERE event_artist_id=".$row["artist_id"]);
	$num_of_albums = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_ALBUMS." WHERE album_artist_id=".$row["artist_id"]);
	$num_of_tracks = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_TRACKS." WHERE track_artist_id=".$row["artist_id"]);
	$is_default = $_wt_options->options("default_artist") == $artist_id ? 1 : 0 ;
	$delete_class  = "action-delete_artist:artist_id-$artist_id:_nonce-".wp_create_nonce(WT_Artist::NONCE_DELETE);
	$edit_class    = "artist_id-$artist_id";
	$default_class    = "action-default_artist:artist_id-$artist_id";
	$notdefault_class    = "action-remove_default_artist:artist_id-$artist_id";
	$edit_link = admin_url("admin.php?page=wt_artists&action=edit&artist_id=".$row["artist_id"]);
	
	$html = "<tr class='".($is_default? "tr-default" : "")."'>
				<th class='check-column'>";
				if($num_of_events==0 && $num_of_albums == 0 && $num_of_tracks == 0) {
					$html.="<input type='checkbox' value='$artist_id'/>";
				}
	$html.=		"</th>
				<td>$artist_id</td>
				<td>$artist_order</td>
				<td>
					<strong><a title='Edit' href='$edit_link' class='row-title'>$artist_name ".($is_default ? "<span class='is-default'>(Default)</span>" : " ")."</a></strong>
					<br>
					<span class='edit'>
						<a title='View Artist' href='".wt_get_permalink("artist",$artist_id,array("%name%"=>$artist_name))."'>View</a> |
					</span>
					<span class='quickedit'>
						<a class='$edit_class' title='Edit this artist' href='#'>Quick Edit</a> |
					</span>
					<span>
						<a title='Edit this artist' href='".admin_url("admin.php?page=wt_artists&action=edit&artist_id=".$row["artist_id"])."'>Edit</a>
					</span> | ";
	if($num_of_events==0 && $num_of_albums == 0 && $num_of_tracks == 0) {
		$html.=			"<span class='delete'>
							<a title='Delete this artist' class='$delete_class' href='#'>Delete</a>
						</span> | ";
	}
	$html .= 		"<span class='setdefault'><a title='Set as default' class='$default_class' href='#'>Set as Default</a></span>				
					<span  class='removedefault'><a title='Remove default' class='$notdefault_class' href='#'>Remove Default</a></span>				
				</td>
				<td><a href='".admin_url("admin.php?page=wt_tracks&artist=$artist_id")."'>$num_of_tracks</a></td>
				<td><a href='".admin_url("admin.php?page=wt_albums&artist=$artist_id")."'>$num_of_albums</a></td>
				<td><a href='".admin_url("admin.php?page=wordtour/navigation.php&artist=$artist_id&event_date=all")."'>$num_of_events</a></td>
			</tr>";			
	return $html ;		
}

function get_track_row_html($row) { 
	global $wpdb,$_wt_options ;
	$row = (array) $row ;
	$track_id = $row["track_id"] ;
	$track_title = $row["track_title"];
	$track_label = $row["track_label"];
	$artist = new WT_Artist();
	$artist = $artist->db_out($row); 
	$artist_name = $artist["artist_name"];
	$artist_id = $artist["artist_id"];
	
	$attachment = new WT_Attachment();
	$track_attached = $attachment->query("attachment_target=album&attachment_type=track&attachment_type_id=$track_id");
	//print_r($track_attached);
	//echo "<br/>";
	$album_attached = array();
	foreach($track_attached as $track_a) {
		if(!in_array($track_a["attachment_target_id"],$album_attached)) {
			$album_id = $track_a["attachment_target_id"];
			$album_title = $wpdb->get_var("SELECT album_title FROM ".WORDTOUR_ALBUMS." WHERE album_id=$album_id"); 
			$album_attached[] = array("id" => $album_id,"title" => $album_title);
		}	
	}
	
	//$num_of_albums = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_EVENTS." WHERE event_artist_id=".$row["artist_id"]);
	$delete_class  = "action-delete_track:track_id-$track_id:_nonce-".wp_create_nonce(WT_Track::NONCE_DELETE);
	$edit_class    = "track_id-$track_id";
	$edit_link = admin_url("admin.php?page=wt_tracks&action=edit&track_id=".$row["track_id"]);
	
	$html = "<tr>
				<th class='check-column'>";
				if($num_of_events==0) {
					$html.="<input type='checkbox' value='$track_id'/>";
				}
		
	$html.=		"</th>
				<td>$track_id</td>
				<td>
					<strong><a title='Edit' href='$edit_link' class='row-title'>$track_title</a></strong>
					<br>
					<span class='quickedit'>
						<a class='$edit_class' title='Edit this track' href='#'>Quick Edit</a> |
					</span>
					<span>
						<a title='Edit this track' href='".admin_url("admin.php?page=wt_tracks&action=edit&track_id=".$row["track_id"])."'>Edit</a>
					</span>"; 
	$html.=			"| <span class='delete'>
						<a title='Delete this track' class='$delete_class' href='#'>Delete</a>
					</span>";
	$html.=		"</td>
				<td>";
				$albums = array();
				foreach($album_attached as $album) {
					$albums[] = "<a href='".admin_url("admin.php?page=wt_albums&action=edit&album_id=".$album["id"])."'>$album[title]</a>";	
				}
				$html.= implode(", ",$albums);
	$html.=	"</td>
				<td><a href='".admin_url("admin.php?page=wt_artists&action=edit&artist_id=".$artist_id)."'>$artist_name</a></td>
				<td>$track_label</td>
			</tr>";
			
	return $html ;		
}

function get_album_row_html($row) { 
	global $wpdb,$_wt_options ;
	$row = (array) $row ;
	$album_id = $row["album_id"] ;
	$album_order = $row["album_order"] ;
	$album_title = $row["album_title"];
	$album_label = $row["album_label"];
	$artist = new WT_Artist();
	$artist = $artist->db_out($row); 
	$artist_name = $artist["artist_name"];
	$artist_id = $artist["artist_id"];
	
	//$num_of_albums = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_EVENTS." WHERE event_artist_id=".$row["artist_id"]);
	$delete_class  = "action-delete_album:album_id-$album_id:_nonce-".wp_create_nonce(WT_Album::NONCE_DELETE);
	$edit_class    = "album_id-$album_id";
	$edit_link = admin_url("admin.php?page=wt_albums&action=edit&album_id=".$row["album_id"]);
	
	$html = "<tr>
				<th class='check-column'>";
				$html.="<input type='checkbox' value='$album_id'/>";
	$html.=		"</th>
				<td>$album_id</td
				<td>$album_order</td>
				<td>
					<strong><a title='Edit' href='$edit_link' class='row-title'>$album_title</a></strong>
					<br>
					<span class='quickedit'>
						<a class='$edit_class' title='Edit this track' href='#'>Quick Edit</a> |
					</span>
					<span>
						<a title='Edit this album' href='".admin_url("admin.php?page=wt_albums&action=edit&album_id=".$row["album_id"])."'>Edit</a>
					</span>"; 
	$html.=			"| <span class='delete'>
						<a title='Delete this album' class='$delete_class' href='#'>Delete</a>
					</span>";
	$html.=		"</td>
				<td><a href='".admin_url("admin.php?page=wt_artists&action=edit&artist_id=".$artist_id)."'>$artist_name</a></td>
				<td>$album_label</td>
			</tr>";
			
	return $html ;		
}

function artists_rows($rows) {
	global $wpdb ;
	if(!$rows) { ?>
		<tr class="empty">
			<td colspan="5"><p>No Artists Found</p></td>
		</tr> 
	<?php	
	} else {
	?>
	<?php
		$artist = new WT_Artist();
		foreach($rows as $row) {
			echo get_artist_row_html($artist->db_out($row));
		}
	}
}

function tracks_rows($rows) {
	global $wpdb ;
	if(!$rows) { ?>
		<tr class="empty">
			<td colspan="5"><p>No Tracks Found</p></td>
		</tr> 
	<?php	
	} else {
	?>
	<?php
		$track = new WT_Track();
		foreach($rows as $row) {
			echo get_track_row_html($track->db_out($row));
		}
	}
}

function albums_rows($rows) {
	global $wpdb ;
	if(!$rows) { ?>
		<tr class="empty">
			<td colspan="5"><p>No Albums Found</p></td>
		</tr> 
	<?php	
	} else {
	?>
	<?php
		$album = new WT_Album();
		foreach($rows as $row) {
			echo get_album_row_html($album->db_out($row));
		}
	}
}

function get_tour_row_html($row) { 
	global $wpdb, $_wt_options ;
	$row = (array) $row ;
	$tour_id = $row["tour_id"] ;
	$tour_order = $row["tour_order"] ;
	$tour_name =$row["tour_name"];
	$num_of_events = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_EVENTS." WHERE event_tour_id=".$row["tour_id"]);
	
	$delete_class  = "action-delete_tour:tour_id-$tour_id:_nonce-".wp_create_nonce(WT_Tour::NONCE_DELETE);
	$edit_class    = "tour_id-$tour_id";
	$default_class    = "action-default_tour:tour_id-$tour_id";
	$notdefault_class    = "action-remove_default_tour:tour_id-$tour_id";
	$is_default = $_wt_options->options("default_tour") == $tour_id ? 1 : 0 ;
	$edit_link = admin_url("admin.php?page=wt_tour&action=edit&tour_id=".$row["tour_id"]);
	
	$html = "<tr class='".($is_default? "tr-default" : "")."'>
				<th class='check-column'>";
				if($num_of_events==0) {
	$html.=			"<input type='checkbox' value='$tour_id'/>";
				}
	$html.=		"</th>
				<td>$tour_id</td>
				<td>$tour_order</td>
				<td>
					<strong class='edit'><a title='Edit' href='$edit_link' class='row-title'>$tour_name ".($is_default ? "<span class='is-default'>(Default)</span>" : " ")."</a></strong>
					<br>
					<span class='edit'>
						<a title='View Tour' href='".wt_get_permalink("tour",$tour_id,array("%name%"=>$tour_name))."'>View</a> |
					</span>
					<span class='quickedit'>
						<a title='Quick edit this tour' class='$edit_class' href='#'>Quick Edit</a>
					</span> | 
					<span>
						<a title='Edit this tour' href='".admin_url("admin.php?page=wt_tour&action=edit&tour_id=".$row["tour_id"])."'>Edit</a>
					</span> | ";
				if($num_of_events==0) {
	$html.=		"<span class='delete'><a title='Delete this tour' class='$delete_class' href='#'>Delete</a></span> | ";
				} 
	$html.=     "<span class='setdefault'><a title='Set as default' class='$default_class' href='#'>Set as Default</a></span>				
					<span  class='removedefault'><a title='Remove default' class='$notdefault_class' href='#'>Remove Default</a></span>	
				</td>
				<td><a href='".admin_url("admin.php?page=wordtour/navigation.php&tour=$tour_id")."'>$num_of_events</a></td>
			</tr>";			
	return $html ;		
}

function tour_rows($rows) {
	global $wpdb ;
	if(!$rows) { ?>
		<tr class="empty">
			<td colspan="3"><p>No Tours Found</p></td>
		</tr> 
	<?php	
	} else {
	?>
	<?php
		$tour = new WT_Tour();
		foreach($rows as $row) {
			echo get_tour_row_html($tour->db_out($row));
		}
	}
}

function get_gallery_row_html($row) { 
	global $wpdb, $_wt_options ;
	$row = (array) $row ;
	$gallery_id = $row["gallery_id"] ;
	$gallery_name =$row["gallery_name"];
	$total = count($row["gallery_attachment"]);
	$num_of_events = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_ATTACHMENT." WHERE attachment_type_id=$row[gallery_id] AND attachment_type='gallery' AND attachment_target='event'");
	$num_of_artists = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_ATTACHMENT." WHERE attachment_type_id=$row[gallery_id] AND attachment_type='gallery' AND attachment_target='artist'");
	$num_of_tours = $wpdb->get_var("SELECT COUNT(*) FROM ".WORDTOUR_ATTACHMENT." WHERE attachment_type_id=$row[gallery_id] AND attachment_type='gallery' AND attachment_target='tour'");
	
	$date = mysql2date(get_option("date_format"),$row["gallery_publish_time"])." at ".mysql2date(get_option("time_format"),$row["gallery_publish_time"]);
	$delete_class  = "action-delete_gallery:gallery_id-$gallery_id:_nonce-".wp_create_nonce(WT_Gallery::NONCE_DELETE);
	$edit_class    = "gallery_id-$gallery_id";
	
	
	$html = "<tr>
				<th class='check-column' scope='row'>
					<input type='checkbox' value='$gallery_id' name='list_check'/>
				</th>
				<td>$gallery_id</td>
				<td>
					<strong class='edit'><a title='Edit' href='#' class='$edit_class'>$gallery_name</a></strong>
					<br>
					<span class='edit'>
						<a title='Edit this gallery' class='$edit_class' href='#'>Edit</a>
					</span>";
				if(!$num_of_events && !$num_of_artists && !$num_of_tours) {	
	$html.=			"| <span class='delete'><a title='Delete this gallery' class='$delete_class' href='#'>Delete</a></span>";
				} else {
	$html.=			"| <span class='delete'><a title='Deattach All Events, Artist & Tour' class='$delete_class' href='#'>Delete And Deattach</a></span>";				
				}
	$html .=	"</td>
				<td>$total</td>
				<td>$date</td>
				<td>$num_of_events Events, $num_of_artists Artists, $num_of_tours Tour</td>
			</tr>";			
	return $html ;		
}

function gallery_rows($rows) {
	global $wpdb ;
	if(!$rows) { ?>
		<tr class="empty">
			<td colspan="3"><p>No Galleries Found</p></td>
		</tr> 
	<?php	
	} else {
	?>
	<?php
		$gallery = new WT_Gallery();
		foreach($rows as $row) {
			$gallery->data = $row;
			echo get_gallery_row_html($gallery->db_out($gallery->data));
		}
	}
}

function add_panel_box_start($id,$title = '',$style='') {
	echo "<div class='metabox-holder'>";
	echo	"<div id='".$id."_px' class='postbox'>";
	echo		"<div class='handlediv' title='Click to toggle'><br/></div>";
	echo		"<h3 class='hndle'><span>$title</span></h3>";
	echo		"<div class='inside' style='$style'>";
}

function add_panel_box_end() {
	echo 		"</div>";
	echo   	"</div>";
	echo "</div>";		 
}

?>