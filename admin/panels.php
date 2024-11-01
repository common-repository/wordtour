<?php
/* **************** */
/*   EVENT PANEL   */
/* **************** */
function wordtour_event_details_panel($collapsed = 0,$show_helpers = 1) {
	wt_static_panel_start(array(),"details");
?>
	<div class="ui-helper-clearfix">
		<div class="wordtour-field wordtour-field-left" style="width:30%;margin-right:0px;">
			<div class="label">Start Date*</div>
			<div class="field field-large">
				<input type="text" id="event_start_date" name="event_start_date"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-left" style="width:17%;margin-left:2px;">
			<div class="label">&nbsp;</div>
			<div class="field-large">
				<input type="text" id="event_start_time" name="event_start_time"></input>
			</div>	
		</div>
		
		<div class="wordtour-field wordtour-field-right" style="width:17%;margin-left:2px;">
			<div class="label">&nbsp;</div>
			<div class="field-large">
				<input type="text" id="event_end_time" name="event_end_time"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-right" style="width:30%;margin-right:0px;">
			<div class="label">End Date*</div>
			<div class="field-large">
				<input type="text" id="event_end_date" name="event_end_date"></input>
			</div>
		</div>
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Where*</div>
		<div class="field-large">
			<input type="text" id="event_venue_name"></input>
			<?php if($show_helpers){?>
			<a id="add-venue" class="add" href="#" style="text-decoration : none;display:inline;">Create New Venue</a> | 
			<a class="show-venues" id="show_all_venues" href="#" style="text-decoration : none;display:inline;">Show All Venues</a>
			<?php }?>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Artist</div>
		<div class="field-large">
			<input type="text" id="event_artist_name"></input>
			<?php if($show_helpers){?>
			<a class="add" id="add-artist" href="#" style="text-decoration : none;">Create New Artist</a> | 
			<a class="show-artists" id="show_all_artists" href="#" style="text-decoration : none;display:inline;">Show All Artists</a>
			<?php }?>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Tour</div>
		<div class="field-large">
			<input type="text" id="event_tour_name"></input>
			<?php if($show_helpers){?>
			<a id="add-tour" class="add" href="#" style="text-decoration : none;">Create New Tour</a> | 
			<a class="show-tour" id="show_all_tour" href="#" style="text-decoration : none;display:inline;">Show All Tour</a>
			<?php }?>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block" id='event_permalink' style='display:none;'>
		<div class="label">Permalink</div>
		<div class="field-large">
			<a href="#" target="_blank"></a> 
		</div>	
	</div>
<?php 
	wt_static_panel_end();
}

function wordtour_event_status_panel($collapsed = 0){
	wt_static_panel_start(array(),"status"); 
?>
	<div id="wordtour-panels-status">
		<input type="checkbox" id="comment_status" value="1"/><label for="comment_status">Show Comments</label>
		<input type="checkbox" id="rsvp_status" value="1"/><label for="rsvp_status">Show RSVP</label>
		<input type="checkbox" id="gallery_status" value="1"/><label for="gallery_status">Show Gallery</label>
		<input type="checkbox" id="flickr_status" value="1"/><label for="flickr_status">Show Flickr</label>
		<input type="checkbox" id="video_status" value="1"/><label for="video_status">Show Videos</label>
		<input type="checkbox" id="post_status" value="1"/><label for="post_status">Show Posts</label>
	</div>
<?php wt_static_panel_end();
}
// Title Panel
function wordtour_event_title_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Title","id"=>"wordtour-panel-title"),"title",$collapsed);
?>
	
	<input type="text" name="event_title"></input>
	
<?php wt_dynamic_panel_end(); 	
}
// Notes Panel
function wordtour_event_notes_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Notes","id"=>"wordtour-panel-notes"),"notes",$collapsed); 
?>
	<textarea  name="event_notes"></textarea>
	<p class="help">Use &lt;!--more--&gt; parameter for formatting a short content</p>
	
<?php 
	wt_dynamic_panel_end();
}
// Opening Act Panel
function wordtour_event_moreartists_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"More Artists","id"=>"wordtour-panel-moreartists"),"moreartists",$collapsed); 
?>
	<div class='more-artists-wrap'></div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Free Text</div>
		<div>
			<input type="text" name="event_opening_act"></input>
		</div>	
	</div>
	
<?php 
	wt_dynamic_panel_end();
}
// Ticket Information Panel
function wordtour_event_tickets_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Ticket Information","id"=>"wordtour-panel-tickets"),"tickets",$collapsed); 
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Tickets URL</div>
		<div>
			<input id="event_tkts_url" type="text" name="tkts_url"></input>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Phone</div>
		<div>
			<input type="text" name="tkts_phone"></input>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Admission</div>
		<div>
			<input type="text" name="tkts_price"></input>
		</div>	
	</div>
<?php 
	wt_dynamic_panel_end(); 
}
// Comments Panel
function wordtour_event_comments_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Comments","id"=>"wordtour-panel-comments"),"comments",$collapsed);
	?>
	<a href="#" id="load-comments">Click here to load comments</a>
	<?php 
	wt_dynamic_panel_end();
}
// Poster Panel
function wordtour_event_poster_panel($collapsed = 0) {
	wt_static_panel_start(array("id"=>"wordtour-poster-panel"),"poster",$collapsed);
	wt_static_panel_end();
}

// Status2 Panel
function wordtour_event_status2_panel($collapsed = 0) {
	wt_static_panel_start(array("id"=>"wordtour-panel-status2"),"status2",$collapsed);
?>
	
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Status</div>
			<div>
				<select name="event_status">
					<?php 
						$status = get_all_status();
						foreach($status as $key=>$value) {
							echo "<option value='$key'>$value</option>";	
						}
					?>
				</select>
			</div>	
		</div>
		<div id="event_on_sale_block" class="wordtour-field wordtour-field-block" style="display:none;">
			<div class="label">Sale starts on</div>
			<div>
				<input type="text" id="event_on_sale" name="event_on_sale"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Type</div>
			<div>
				<input type="text" id="event_type" name="event_type""></input>
				<a id="show_all_event_type" href="#" style="text-decoration : none;display:inline;">Show All Suggested</a>
			</div>	
		</div>
	
<?php 
	wt_static_panel_end();
}

// Social Panel
function wordtour_event_social_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("id"=>"wordtour-panel-social","title"=>"Social Networking"),"social",$collapsed);
	?>
	<div id="wordtour-button-facebook" title="Publish To Facebook"></div>
	<div id="wordtour-button-twitter" title="Update Twitter Status"></div>
	<div id="wordtour-button-eventbrite" title="Publish To Eventbrite"></div>
	<div id="wordtour-button-post" title="Add New Post"></div>
	<?php 
	wt_dynamic_panel_end();
}

// Gallery Panel
function wordtour_event_gallery_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("id"=>"wordtour-panel-gallery","title"=>"Gallery"),"gallery",$collapsed);
		wordtour_gallery_checklist();	
	wt_dynamic_panel_end();
}

// genre panel
function wordtour_event_genre_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("id"=>"wordtour-panel-genre","title"=>"Genre"),"genre",$collapsed);	
	wt_dynamic_panel_end();
}
// Category Panel
function wordtour_event_category_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("id"=>"wordtour-panel-category","title"=>"Attach Posts by Category"),"category",$collapsed);
		wordtour_category_checklist();
	wt_dynamic_panel_end();
}
// Video Panel
function wordtour_event_video_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Videos","id"=>"wordtour-panel-video"),"video",$collapsed);
	wt_dynamic_panel_end();
}
// RSVP Panel
function wordtour_event_rsvp_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"RSVP","id"=>"wordtour-panel-rsvp"),"rsvp",$collapsed);
?>
	<a href="#" id="load-rsvp">Click here to load RSVP</a>
<?php 
	wt_dynamic_panel_end();
}

/* **************** */
/*   ALBUM PANELS   */
/* **************** */
function wordtour_album_details_panel($collapsed = 0,$show_helpers=1) {
	wt_static_panel_start(array(),"details");
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Title*</div>
		<div class="field-large">
			<input type="text" name="album_title" id="album_title"></input>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Artist*</div>
		<div class="field-large">
			<input type="text" name="album_artist_name" id="album_artist_name"></input>
			<?php if($show_helpers){?>
			<a class="add" id="add-artist" href="#" style="text-decoration : none;">Create New Artist</a> | 
			<a class="show-artists" id="show_all_artists" href="#" style="text-decoration : none;display:inline;">Show All Artists</a>
			<?php }?>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block" id='album_permalink' style='display:none;'>
		<div class="label">Permalink</div>
		<div class="field-large">
			<a href="#" target="_blank"></a> 
		</div>
	</div>
<?php 
	wt_static_panel_end();
}

// Order Panel
function wordtour_album_order_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Order","id"=>"wordtour-panel-order"),"order",$collapsed); 
?>
	<input type="text" name="album_order"></input>
<?php 
	wt_dynamic_panel_end();
}

function wordtour_album_poster_panel($collapsed = 0) {
	wt_static_panel_start(array("id"=>"wordtour-poster-panel"),"poster",$collapsed);
	wt_static_panel_end();
}

// Track Panel
function wordtour_album_tracks_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("id"=>"wordtour-panel-tracks","title"=>"Tracks"),"tracks",$collapsed);	
	wt_dynamic_panel_end();
}

function wordtour_album_genre_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("id"=>"wordtour-panel-genre","title"=>"Genre"),"genre",$collapsed);	
	wt_dynamic_panel_end();
}

function wordtour_album_status_panel($collapsed = 0){
	wt_static_panel_start(array(),"status"); 
?>
	<div id="wordtour-panels-status">
		<input type="checkbox" id="album_tracks_status" value="1"/><label for="album_tracks_status">Show Tracks</label>
		<input type="checkbox" id="album_similar_status" value="1"/><label for="album_similar_status">Show Similar Albums</label>
	</div>
<?php wt_static_panel_end();
}

function wordtour_album_more_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"More Information","id"=>"wordtour-panel-more"),"more",$collapsed); 
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Type</div>
		<div class="field">
			<select type="text" name="album_type" id="album_type">
				<option value="album">Album</option>
				<option value="single">Single</option>
				<option value="ep">DVD</option>
				<option value="ep">EP</option>
				<option value="compilation">Compilation</option>
				<option value="soundtrack">Soundtrack</option>
				<option value="live">Live</option>
				<option value="remix">Remix</option>
				<option value="interview">Interview</option>
				<option value="audiobook">Audiobook</option>
				<option value="reissue">Reissue</option>
				<option value="vinyl">Vinyl</option>
				<option value="other">Other</option>
			</select>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Label</div>
		<div class="field">
			<input type="text" name="album_label" id="album_label"></input>
		</div>
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Release Date</div>
		<div class="field">
			<input type="text" name="album_release_date" id="album_release_date"></input>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Credits</div>
		<div class="field">
			<input type="text" name="album_credits" id="album_credits"></input>
		</div>
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">About this album</div>
		<div class="field">
			<textarea name="album_about" id="album_about"style="height:100px;"></textarea>
			<p class="help">Use &lt;!--more--&gt; parameter for formatting a short bio content</p>
		</div>	
	</div>
<?php 
	wt_dynamic_panel_end(); 
}

function wordtour_album_buy_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Buy Links","id"=>"wordtour-panel-buy"),"buy",$collapsed); 
?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Amazon Link</div>
			<div>
				<input type="text" name="album_buy_amazon"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Amazon MP3 Link</div>
			<div>
				<input type="text" name="album_buy_amazon_mp3"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">iTunes Link</div>
			<div>
				<input type="text" name="album_buy_itunes"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Buy Link 1</div>
			<div>
				<input type="text" name="album_buy_link_1"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Buy Link 2</div>
			<div>
				<input type="text" name="album_buy_link_2"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Buy Link 3</div>
			<div>
				<input type="text" name="album_buy_link_3"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">PayPal Button HTML</div>
			<div>
				<textarea name="album_buy_pay_pal" style="height:150px;"></textarea>
			</div>	
		</div>
<?php 
	wt_dynamic_panel_end(); 
}


/* **************** */
/*   TRACK PANELS   */
/* **************** */
function wordtour_track_details_panel($collapsed = 0,$show_helpers=1) {
	wt_static_panel_start(array(),"details");
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Title*</div>
		<div class="field-large">
			<input type="text" name="track_title" id="track_title"></input>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Artist</div>
		<div class="field-large">
			<input type="text" name="track_artist_name" id="track_artist_name"></input>
			<?php if($show_helpers){?>
			<a class="add" id="add-artist" href="#" style="text-decoration : none;">Create New Artist</a> | 
			<a class="show-artists" id="show_all_artists" href="#" style="text-decoration : none;display:inline;">Show All Artists</a>
			<?php }?>
		</div>	
	</div>
<?php 
	wt_static_panel_end();
}

function wordtour_track_poster_panel($collapsed = 0) {
	wt_static_panel_start(array("id"=>"wordtour-poster-panel"),"poster",$collapsed);
	wt_static_panel_end();
}

// genre panel
function wordtour_track_genre_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("id"=>"wordtour-panel-genre","title"=>"Genre"),"genre",$collapsed);	
	wt_dynamic_panel_end();
}

function wordtour_track_more_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"More Information","id"=>"wordtour-panel-more"),"more",$collapsed); 
?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Label</div>
			<div class="field">
				<input type="text" name="track_label" id="track_label"></input>
			</div>
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Release Date</div>
			<div class="field">
				<input type="text" name="track_release_date" id="track_release_date"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Credits</div>
			<div class="field">
				<input type="text" name="track_credits" id="track_credits"></input>
			</div>
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">About This Track</div>
			<div class="field">
				<textarea name="track_about" style="height:100px;"></textarea>
				<p class="help">Use &lt;!--more--&gt; parameter for formatting a short bio content</p>
			</div>	
		</div>
	
<?php 
	wt_dynamic_panel_end(); 
}

// Lyrics Panel
function wordtour_track_lyrics_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Lyrics","id"=>"wordtour-panel-lyrics"),"lyrics",$collapsed); 
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Lyrics</div>
		<div class="field">
			<textarea name="track_lyrics"></textarea>
		</div>
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Author</div>
		<div class="field">
			<input type="text" name="track_lyrics_author" id="track_lyrics_author"></input>
		</div>
	</div>
<?php 
	wt_dynamic_panel_end();
}

/* **************** */
/*   ARTIST PANEL   */
/* **************** */
function wordtour_artist_details_panel($collapsed = 0) {
	wt_static_panel_start(array(),"details");
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Name*</div>
		<div class="field-large">
			<input type="text" name="artist_name" id="artist_name"></input>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block" id='artist_permalink' style='display:none;'>
		<div class="label">Permalink</div>
		<div class="field-large">
			<a href="#" target="_blank"></a> 
		</div>
	</div>	
<?php 
	wt_static_panel_end();
}
	
function wordtour_artist_status_panel($collapsed = 0){
	wt_static_panel_start(array(),"status"); 
?>
	<div id="wordtour-panels-status">
		<input type="checkbox" id="artist_gallery_status" value="1"/><label for="artist_gallery_status">Show Gallery</label>
		<input type="checkbox" id="artist_flickr_status" value="1"/><label for="artist_flickr_status">Show Flickr</label>
		<input type="checkbox" id="artist_video_status" value="1"/><label for="artist_video_status">Show Videos</label>
		<input type="checkbox" id="artist_post_status" value="1"/><label for="artist_post_status">Show Posts</label>
		<input type="checkbox" id="artist_tour_status" value="1"/><label for="artist_tour_status">Show Upcoming Events</label>
	</div>
<?php wt_static_panel_end();
}
// Bio Panel
function wordtour_artist_bio_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Bio","id"=>"wordtour-panel-bio"),"bio",$collapsed); 
?>
	<textarea name="artist_bio"></textarea>
	<p class="help">Use &lt;!--more--&gt; parameter for formatting a short bio content</p>
<?php 
	wt_dynamic_panel_end();
}
// Order Panel
function wordtour_artist_order_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Order","id"=>"wordtour-panel-order"),"order",$collapsed); 
?>
	<input type="text" name="artist_order"></input>
<?php 
	wt_dynamic_panel_end();
}
// Ticket Information Panel
function wordtour_artist_info_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"More Information","id"=>"wordtour-panel-info"),"info",$collapsed); 
?>
		<div class="wordtour-field wordtour-field-block">	
			<div class="label">Record Label</div>
			<div>
				<input type="text" name="artist_record_company" id="artist_record_company"></input>
			</div>
		</div>
		<div class="wordtour-field wordtour-field-block">	
			<div class="label">Website URL</div>
			<div>
				<input type="text" name="artist_website_url"></input>
			</div>
		</div>	
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Contact Info (Email, Web Page etc.)</div>
			<div>
				<input type="text" name="artist_email"></input>
			</div>	
		</div>
<?php 
	wt_dynamic_panel_end(); 
}
	
function wordtour_artist_social_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Social Networks Links","id"=>"wordtour-panel-social"),"social",$collapsed); 
?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Facebook Link</div>
			<div>
				<input type="text" name="artist_facebook"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Twitter Link</div>
			<div>
				<input type="text" name="artist_twitter"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">MySpace Link</div>
			<div>
				<input type="text" name="artist_myspace"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Last.FM Link</div>
			<div>
				<input type="text" name="artist_lastfm"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">YouTube Link</div>
			<div>
				<input type="text" name="artist_youtube"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Vimeo Link</div>
			<div>
				<input type="text" name="artist_vimeo"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Flickr Link</div>
			<div>
				<input type="text" name="artist_flickr"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Bandcamp Link</div>
			<div>
				<input type="text" name="artist_bandcamp"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Tumblr Link</div>
			<div>
				<input type="text" name="artist_tumblr"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Reverbnation Link</div>
			<div>
				<input type="text" name="artist_reverbnation"></input>
			</div>	
		</div>
<?php 
	wt_dynamic_panel_end(); 
}

// Poster Panel
function wordtour_artist_poster_panel($collapsed = 0) {
	wt_static_panel_start(array("id"=>"wordtour-poster-panel"),"poster",$collapsed);
	wt_static_panel_end();
}
// Gallery Panel
function wordtour_artist_gallery_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("id"=>"wordtour-panel-gallery","title"=>"Gallery"),"gallery",$collapsed);
		wordtour_gallery_checklist();	
	wt_dynamic_panel_end();
}
// Category Panel
function wordtour_artist_category_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("id"=>"wordtour-panel-category","title"=>"Attach Posts by Category"),"category",$collapsed);
		wordtour_category_checklist();
	wt_dynamic_panel_end();
}
// Video Panel
function wordtour_artist_video_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Videos","id"=>"wordtour-panel-video"),"video",$collapsed);
	wt_dynamic_panel_end();
}
// Media Player Panel
function wordtour_artist_player_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Player","id"=>"wordtour-panel-player"),"player",$collapsed);
	wt_dynamic_panel_end();
}

// genre panel
function wordtour_artist_genre_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("id"=>"wordtour-panel-genre","title"=>"Genre"),"genre",$collapsed);	
	wt_dynamic_panel_end();
}
	
/* **************** */
/*   Tour PANEL   */
/* **************** */
function wordtour_tour_details_panel($collapsed = 0) {
		wt_static_panel_start(array(),"details");
	?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Name*</div>
			<div class="field-large">
				<input type="text" name="tour_name" id="tour_name"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block" id='tour_permalink' style='display:none;'>
			<div class="label">Permalink</div>
			<div class="field-large">
				<a href="#" target="_blank"></a> 
			</div>
		</div>	
	<?php 
		wt_static_panel_end();
	}
	
	function wordtour_tour_status_panel($collapsed = 0){
		wt_static_panel_start(array(),"status"); 
	?>
		<div id="wordtour-panels-status">
			<input type="checkbox" id="tour_gallery_status" value="1"/><label for="tour_gallery_status">Show Gallery</label>
			<input type="checkbox" id="tour_flickr_status" value="1"/><label for="tour_flickr_status">Show Flickr</label>
			<input type="checkbox" id="tour_video_status" value="1"/><label for="tour_video_status">Show Videos</label>
			<input type="checkbox" id="tour_post_status" value="1"/><label for="tour_post_status">Show Posts</label>
			<input type="checkbox" id="tour_tour_status" value="1"/><label for="tour_tour_status">Show All Events</label>
		</div>
	<?php wt_static_panel_end();
	}
	// Info Panel
	function wordtour_tour_info_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("title"=>"Info","id"=>"wordtour-panel-info"),"info",$collapsed); 
	?>
		<textarea name="tour_description"></textarea>
		<p class="help">Use &lt;!--more--&gt; parameter for formatting a short description content</p>
	<?php 
		wt_dynamic_panel_end();
	}
	// Order Panel
	function wordtour_tour_order_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("title"=>"Order","id"=>"wordtour-panel-order"),"order",$collapsed); 
	?>
		<input type="text" name="tour_order"></input>
	<?php 
		wt_dynamic_panel_end();
	}
	// Poster Panel
	function wordtour_tour_poster_panel($collapsed = 0) {
		wt_static_panel_start(array("id"=>"wordtour-poster-panel"),"poster",$collapsed);
		wt_static_panel_end();
	}
	// genre panel
	function wordtour_tour_genre_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("id"=>"wordtour-panel-genre","title"=>"Genre"),"genre",$collapsed);	
		wt_dynamic_panel_end();
	}
	// Gallery Panel
	function wordtour_tour_gallery_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("id"=>"wordtour-panel-gallery","title"=>"Gallery"),"gallery",$collapsed);
			wordtour_gallery_checklist();	
		wt_dynamic_panel_end();
	}
	// Category Panel
	function wordtour_tour_category_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("id"=>"wordtour-panel-category","title"=>"Attach Posts by Category"),"category",$collapsed);
			wordtour_category_checklist();
		wt_dynamic_panel_end();
	}
	// Video Panel
	function wordtour_tour_video_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("title"=>"Videos","id"=>"wordtour-panel-video"),"video",$collapsed);
		wt_dynamic_panel_end();
	}


/* **************** */
/*   VENUE PANEL   */
/* **************** */
function wordtour_venue_details_panel($collapsed = 0,$show_helpers=1) {
		wt_static_panel_start(array("id"=>"wordtour-details-panel"),"details");
	?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Name*</div>
			<div class="field-large">
				<input type="text" name="venue_name" id="venue_name"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Address*</div>
			<div class="field-large">
				<input type="text" name="venue_address" id="venue_address"></input>
			</div>	
		</div>
		<div class="ui-helper-clearfix">
			<div class="wordtour-field wordtour-field-left" style="width:35%;margin-right:0px;">
				<div class="label">City*</div>
				<div class="field-large">
					<input type="text" name="venue_city" id="venue_city"></input>
				</div>	
			</div>
			<div class="wordtour-field wordtour-field-left" style="width:26%;margin-right:0px;">
				<div class="label">State\Region</div>
				<div class="field-large">
					<input type="text" name="venue_state" id="venue_state"></input>
					<?php if($show_helpers){?> 
					<a id="show_all_states" href="#" style="text-decoration : none;display:inline;">Show All States</a>
					<?php }?>
				</div>	
			</div>
			<div class="wordtour-field wordtour-field-left" style="width:35%;margin-right:0px;">
				<div class="label">Country*</div>
				<div class="field-large">
					<input type="text" name="venue_country" id="venue_country"></input>
					<?php if($show_helpers){?> 
					<a id="show_all_countries" href="#" style="text-decoration : none;display:inline;">Show All Countries</a>
					<?php }?>
				</div>	
			</div>
		</div>
		<div class="wordtour-field wordtour-field-block" id='venue_permalink' style='display:none;'>
			<div class="label">Permalink</div>
			<div class="field-large">
				<a href="#" target="_blank"></a> 
			</div>
		</div>	
		
	<?php 
		wt_static_panel_end();
	}
	
	function wordtour_venue_status_panel($collapsed = 0){
		wt_static_panel_start(array(),"status"); 
	?>
		<div id="wordtour-panels-status">
			<input type="checkbox" id="venue_gallery_status" value="1"/><label for="venue_gallery_status">Show Gallery</label>
			<input type="checkbox" id="venue_flickr_status" value="1"/><label for="venue_flickr_status">Show Flickr</label>
			<input type="checkbox" id="venue_video_status" value="1"/><label for="venue_video_status">Show Videos</label>
			<input type="checkbox" id="venue_post_status" value="1"/><label for="venue_post_status">Show Posts</label>
			<input type="checkbox" id="venue_tour_status" value="1"/><label for="venue_tour_status">Show All Events</label>
		</div>
	<?php wt_static_panel_end();
	}
	// More Details
	function wordtour_venue_more_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("title"=>"More Info","id"=>"wordtour-panel-more"),"more",$collapsed); 
	?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Website</div>
			<div>
				<input type="text" name="venue_url"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Phone Number</div>
			<div>
				<input type="text" name="venue_phone"></input>
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Zip</div>
			<div>
				<input type="text" name="venue_zip"></input>
			</div>	
		</div>
	<?php 
		wt_dynamic_panel_end();
	}
	// Info Panel
	function wordtour_venue_info_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("title"=>"Description","id"=>"wordtour-panel-info"),"info",$collapsed); 
	?>
		<textarea name="venue_info"></textarea>
		<p class="help">Use &lt;!--more--&gt; parameter for formatting a short description content</p>
	<?php 
		wt_dynamic_panel_end();
	}
	// Order Panel
	function wordtour_venue_order_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("title"=>"Order","id"=>"wordtour-panel-order"),"order",$collapsed); 
	?>
		<input type="text" name="venue_order"></input>
	<?php 
		wt_dynamic_panel_end();
	}
	// Poster Panel
	function wordtour_venue_poster_panel($collapsed = 0) {
		wt_static_panel_start(array("id"=>"wordtour-poster-panel"),"poster",$collapsed);
		wt_static_panel_end();
	}
	// Map Panel
	function wordtour_venue_map_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("id"=>"wordtour-panel-map","title"=>"Map"),"map",$collapsed);
	?>
	
	<?php 
		wt_dynamic_panel_end();
	}
	// Gallery Panel
	function wordtour_venue_gallery_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("id"=>"wordtour-panel-gallery","title"=>"Gallery"),"gallery",$collapsed);
			wordtour_gallery_checklist();	
		wt_dynamic_panel_end();
	}
	// Category Panel
	function wordtour_venue_category_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("id"=>"wordtour-panel-category","title"=>"Attach Posts by Category"),"category",$collapsed);
			wordtour_category_checklist();
		wt_dynamic_panel_end();
	}
	// Video Panel
	function wordtour_venue_video_panel($collapsed = 0) {
		wt_dynamic_panel_start(array("title"=>"Videos","id"=>"wordtour-panel-video"),"video",$collapsed);
		wt_dynamic_panel_end();
	}

/* **************** */
/*   COMMENT PANEL   */
/* **************** */

function wordtour_comment_details_panel($collapsed = 0) {
	wt_static_panel_start(array(),"details");
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Name*</div>
		<div class="field-large">
			<input type="text" name="comment_author" id="comment_author"></input>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Email</div>
		<div class="field-large">
			<input type="text" name="comment_author_email" id="comment_author_email"></input>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Content*</div>
		<div class="field">
			<textarea type="text" name="comment_content" id="comment_content"></textarea>
		</div>	
	</div>
<?php 
	wt_static_panel_end();
}

/* **************** */
/*   SETTINGS PANEL   */
/* **************** */

function wordtour_settings_system_panel($collapsed = 0) {	
	global $_wt_options;
	$options = $_wt_options->options();
	wt_dynamic_panel_start(array("title"=>"System","id"=>"wordtour-panel-system"),"system",$collapsed); 
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Version</div>
		<div>
			<?php echo $options["version"];?>
			<input type="hidden" value="<?php echo $options["version"];?>" name="wordtour_settings[version]"></input>
		</div>	
	</div>
	
	<div class="wordtour-field wordtour-field-block">
		<div class="label">If you encounter any problem while upgrading to version 1.2.5.0, Click the upgrade button</div>
		<div style='margin-top:7px;'>
			<a href="#" id="settings-upgrade-button" class="button">Upgrade to 1.2.5.0</a>
		</div>
	</div>
	
	
	
<!--	<div class="wordtour-field wordtour-field-block">-->
<!--		<div class="label">Minimize JS (For Developers)</div>-->
<!--		<div>-->
<!--			<select name="wordtour_settings[js_minimized]">-->
<!--				<option value="mini" <?php echo $options["js_minimized"] == "dev" ? "" : "selected='true'" ;?>>Minimized</option>-->
<!--				<option value="dev" <?php echo $options["js_minimized"] == "dev" ? "selected='true'" : "";?>>Dev</option>-->
<!--			</select>-->
<!--		</div>	-->
<!--	</div>-->
<?php
	wt_dynamic_panel_end(); 
}


function wordtour_settings_general_panel($collapsed = 0) {	
	global $_wt_options;
	$options = $_wt_options->options();
	wt_dynamic_panel_start(array("title"=>"General","id"=>"wordtour-panel-general"),"general",$collapsed); 
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Language</div>
		<div>
			<select>
				<option>English</option>	
			</select>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">User Premission</div>
		<div>
			<select name="wordtour_settings[user_role]">
				<option <?php echo ($options["user_role"] == "manage_options" ? "selected='true'" : "") ; ?> value="manage_options">Administrator</option>
				<option <?php echo ($options["user_role"] == "edit_pages" ? "selected='true'" : "") ; ?> value="edit_pages">Editor</option>
				<option <?php echo ($options["user_role"] == "edit_posts" ? "selected='true'" : "") ; ?> value="edit_posts">Author</option>
			</select>
		</div>	
	</div>
	
	
	
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Theme Path</div>
		<div id="theme_path_error" style='color:red;display:none;'></div>
		<div>
			<?php 
				$theme_path = ($options["theme_path"] ? $options["theme_path"] : WT_THEME_PATH);
			?> 
			<span  title='<?php echo ABSPATH ;?>'>your_wordpress_path<?php echo ($pos = strpos(ABSPATH,'\\')) ? "\\" : "/" ;?></span>
			<input style='width:70%' id='theme_path' type='text' name='wordtour_settings[theme_path]' readonly='true' value='<?php echo $theme_path;?>'/>
			<input style='width:7%' id='theme_path_button' class='button' type='button' value='Edit'></input>
		</div>
	</div>
	
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Theme Name</div>
		<div id="theme_default_wrap">
			<select  name='wordtour_settings[default_theme]'>
			<?php
				$theme_select_markup = "<option value=''>--Select Theme--</option>";
				foreach(wt_get_themes() as $theme){
					$theme_name = strtoupper($theme);
					if($theme_name!="LIBRARY") {
						$selected_theme = $options["default_theme"] == $theme ? "selected='true'" : "";
						 
				    	$theme_select_markup.="<option $selected_theme value='$theme'>$theme</option>";
					}	  
				} 
				echo $theme_select_markup;
			?>
			</select>
			<p class="help">It is highly recommended to create a new theme, by duplicating the "default" theme folder. because each WordTour upgrade will overwrite "default" folder</p>	
		</div>	
	</div>		
<?php 
	wt_dynamic_panel_end();
}

function wordtour_settings_permalinks_panel($collapsed = 0) {		
	wt_dynamic_panel_start(array("title"=>"Permalinks","id"=>"wordtour-panel-permalinks"),"permalinks",$collapsed); 
	global $_wt_options;
	$options = $_wt_options->options();
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Permalinks</div>
		<div>
			<?php
				global $wp_rewrite;
				$permalink_msg = "";
				if(!$wp_rewrite->using_permalinks() && $options["permalinks"]==1) {
					$permalink_msg = "<p class='help'>* Please Note: Permalinks will not work until enabling in WordPress Settings Page</p>";
				}  
				
				if($wp_rewrite->using_permalinks() && $options["permalinks"]==1) {
					$permalink_msg = "<p class='help'>Please Note: In order to make permalinks work. Go to the WordPress permalinks settings page and click 'Save Changes' button</p>";	
				}
			?>
			<select name="wordtour_settings[permalinks]">
				<option <?php echo $options["permalinks"]=="0" ? "selected" : "" ?> value="0">Disable Permalinks</option>
				<option <?php echo $options["permalinks"]=="1" ? "selected" : "" ?> value="1">Enable Permalinks</option>
			</select>
			<?php echo $permalink_msg;?>
		</div>
	</div>
	<?php 
		$permalinks_format = array(
			"event" =>array(get_bloginfo("url")."/event/%id%/",get_bloginfo("url")."/%date%/event/%id%/",get_bloginfo("url")."/%date%/%name%/event/%id%/"),
			"artist"=>array(get_bloginfo("url")."/artist/%id%/",get_bloginfo("url")."/artist/%name%/%id%/"),
			"tour"  =>array(get_bloginfo("url")."/tour/%id%/",get_bloginfo("url")."/tour/%name%/%id%/"),
			"venue"  =>array(get_bloginfo("url")."/venue/%id%/",get_bloginfo("url")."/venue/%name%/%id%/"),
			"album"  =>array(get_bloginfo("url")."/album/%id%/",get_bloginfo("url")."/album/%name%/%id%/")
		);
	?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Event Permalink</div>
		<div>
			<select name="wordtour_settings[permalinks_event]">
				<option value="<?php echo $permalinks_format["event"][0];?>" <?php echo $options["permalinks_event"]==$permalinks_format["event"][0] ? "selected=true" : "" ?>><?php echo get_bloginfo("url")."/event/id/";?></option>
				<option value="<?php echo $permalinks_format["event"][1];?>" <?php echo $options["permalinks_event"]==$permalinks_format["event"][1] ? "selected=true" : "" ?>><?php echo get_bloginfo("url")."/year/month/day/event/id/";?></option>
				<option value="<?php echo $permalinks_format["event"][2];?>" <?php echo $options["permalinks_event"]==$permalinks_format["event"][2] ? "selected=true" : "" ?>><?php echo get_bloginfo("url")."/year/month/day/venue-name/event/id/";?></option>
			</select> 
		</div>
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Artist Permalink</div>
		<div>
			<select name="wordtour_settings[permalinks_artist]">
				<option value="<?php echo $permalinks_format["artist"][1];?>" <?php echo $options["permalinks_artist"]==$permalinks_format["artist"][1] ? "selected=true" : "" ?>><?php echo get_bloginfo("url")."/artist/artist-name/id/";?></option>
				<option value="<?php echo $permalinks_format["artist"][0];?>" <?php echo $options["permalinks_artist"]==$permalinks_format["artist"][0] ? "selected=true" : "" ?>><?php echo get_bloginfo("url")."/artist/id/";?></option>
			</select> 
		</div>
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Tour Permalink</div>
		<div>
			<select name="wordtour_settings[permalinks_tour]">
				<option value="<?php echo $permalinks_format["tour"][1];?>" <?php echo $options["permalinks_tour"]==$permalinks_format["tour"][1] ? "selected=true" : "" ?>><?php echo get_bloginfo("url")."/tour/tour-name/id/";?></option>
				<option value="<?php echo $permalinks_format["tour"][0];?>" <?php echo $options["permalinks_tour"]==$permalinks_format["tour"][0] ? "selected=true" : "" ?>><?php echo get_bloginfo("url")."/tour/id/";?></option>
			</select> 
		</div>
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Venue Permalink</div>
		<div>
			<select name="wordtour_settings[permalinks_venue]">
				<option value="<?php echo $permalinks_format["venue"][1];?>" <?php echo $options["permalinks_venue"]==$permalinks_format["venue"][1] ? "selected=true" : "" ?>><?php echo get_bloginfo("url")."/venue/venue-name/id/";?></option>
				<option value="<?php echo $permalinks_format["venue"][0];?>" <?php echo $options["permalinks_venue"]==$permalinks_format["venue"][0] ? "selected=true" : "" ?>><?php echo get_bloginfo("url")."/venue/id/";?></option>
			</select> 
		</div>
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Album Permalink</div>
		<div>
			<select name="wordtour_settings[permalinks_album]">
				<option value="<?php echo $permalinks_format["album"][1];?>" <?php echo $options["permalinks_album"]==$permalinks_format["album"][1] ? "selected=true" : "" ?>><?php echo get_bloginfo("url")."/album/album-name/id/";?></option>
				<option value="<?php echo $permalinks_format["album"][0];?>" <?php echo $options["permalinks_album"]==$permalinks_format["album"][0] ? "selected=true" : "" ?>><?php echo get_bloginfo("url")."/album/id/";?></option>
			</select> 
		</div>
	</div>
<?php 
	wt_dynamic_panel_end();
}


function wordtour_settings_discussion_panel($collapsed = 0) {		
	wt_dynamic_panel_start(array("title"=>"Discussion","id"=>"wordtour-panel-discussion"),"discussion",$collapsed); 
	global $_wt_options;
	$options = $_wt_options->options();
?>
	<div class="wordtour-field wordtour-field-block">
		<div>
			<?php
			$comment_registration_checked = $options["comment_registration"]==1 ? "checked='true'" : "";
			$comment_moderation_checked = $options["moderation_notify"] ? "checked='true'" : "";
			$comment_captcha = $options["comment_captcha"] ? "checked='true'" : "";
			$comment_show_after_checked = $options["comment_show_after_event"]==1 ? "checked='true'" : "";
			?>
			<input style="width:inherit;" type="checkbox" <?php echo $comment_registration_checked; ?> value="1" name="wordtour_settings[comment_registration]"/>
			Users must be registered and logged in to comment<br/>
			<input style="width:inherit;" type="checkbox" <?php echo $comment_moderation_checked; ?>value="1" name="wordtour_settings[moderation_notify]"/>
			A comment is held for moderation<br/>
			<input style="width:inherit;" type="checkbox" <?php echo $comment_show_after_checked; ?> value="1" name="wordtour_settings[comment_show_after_event]"/>
			Show comments after event occured<br/>
			
			<input style="width:inherit;" type="checkbox" <?php echo $comment_captcha; ?> value="1" name="wordtour_settings[comment_captcha]"/>
			Use Captcha Service for unregistered users to help protect spam, <a style='font-size:10px;' href="https://www.google.com/recaptcha/admin/create" target="_blank">Sign Up for a reCaptcha Key</a>
			<div style="margin-left:35px;">
			Public Key <input style="width:250px;" type="text" name="wordtour_settings[captcha_public_key]" value="<?php echo $options["captcha_public_key"]?>"></input>
			Private Key <input style="width:250px;" type="text" name="wordtour_settings[captcha_private_key]" value="<?php echo $options["captcha_private_key"]?>"></input>
			</div>
		</div>	
	
	</div>
<?php 
	wt_dynamic_panel_end();
}

function wordtour_settings_event_panel($collapsed = 0) {			
	wt_dynamic_panel_start(array("title"=>"Event Form","id"=>"wordtour-panel-event"),"event",$collapsed);
	global $_wt_options;
	$options = $_wt_options->options(); 
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Date Format</div>
		<div>
			<?php 
				$admin_date_fomrat = $options["admin_date_format"];
			?>
			<select name="wordtour_settings[admin_date_format]">
				<option value="m/d/y" <?php echo ($admin_date_fomrat == "m/d/y") ? "selected='true'" : "";?>>m/d/y</option>
				<option value="d/m/y" <?php echo ($admin_date_fomrat == "d/m/y") ? "selected='true'" : "";?>>d/m/y</option>	
			</select>
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Display Defaults</div>
		<div>
			<?php
				$allow_comments_check = $options["allow_comments"]==1 ? "checked='true'" : "";
				$allow_rsvp_check = $options["allow_rsvp"]==1 ? "checked='true'" : "";
				$show_gallery_check = $options["show_gallery"]==1 ? "checked='true'" : "";
				$show_flickr_check = $options["show_flickr"]==1 ? "checked='true'" : "";
				$show_videos_check = $options["show_videos"]==1 ? "checked='true'" : "";
				$show_posts_check = $options["show_posts"]==1 ? "checked='true'" : "";
			?>
			<input style="width:inherit;" type="checkbox" <?php echo $allow_comments_check;?> name="wordtour_settings[allow_comments]" value="1"/> Allow Comments
			<input style="width:inherit;" type="checkbox" <?php echo $allow_rsvp_check;?> name="wordtour_settings[allow_rsvp]" value="1"/> Allow RSVP
			<input style="width:inherit;" type="checkbox" <?php echo $show_gallery_check;?> name="wordtour_settings[show_gallery]" value="1"/> Show Gallery
			<input style="width:inherit;" type="checkbox" <?php echo $show_flickr_check;?> name="wordtour_settings[show_flickr]" value="1"/> Show Flickr
			<input style="width:inherit;" type="checkbox" <?php echo $show_videos_check;?> name="wordtour_settings[show_videos]" value="1"/> Show Videos
			<input style="width:inherit;" type="checkbox" <?php echo $show_posts_check;?> name="wordtour_settings[show_posts]" value="1"/> Show Posts	
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Poster</div>
		<div>
			<?php
				$show_tour_poster = $options["show_tour_poster"]==1 ? "checked='true'" : "";
			?>
			<input style="width:inherit;" type="checkbox" <?php echo $show_tour_poster;?> name="wordtour_settings[show_tour_poster]" value="1"/> Display tour poster if event poster doesnt exist		
		</div>	
	</div>
	
<?php 
	wt_dynamic_panel_end();
}

function wordtour_settings_google_panel($collapsed = 0) {	
	wt_dynamic_panel_start(array("title"=>"Google Map - <a style=\"font-size:12px;font-weight:normal;color:#21759B;text-decoration:none;\" href=\"http://code.google.com/apis/maps/signup.html\" target=\"_blank\">Register</a>","id"=>"wordtour-panel-google"),"google",$collapsed); 
	global $_wt_options;
	$options = $_wt_options->options();
?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Key</div>
			<div>
				<textarea cols="80" rows="2" name="wordtour_settings[google_map_key]"><?php echo $options["google_map_key"]?></textarea>		
			</div>	
		</div>
	<?php 
		wt_dynamic_panel_end();
}

function wordtour_settings_facebook_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Facebook - <a style=\"font-size:12px;font-weight:normal;color:#21759B;text-decoration:none;\" href=\"http://www.facebook.com/developers/editapp.php\" target=\"_blank\">Register</a>","id"=>"wordtour-panel-facebook"),"facebook",$collapsed); 
	global $_wt_options;
	$options = $_wt_options->options();
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">App ID</div>
		<div>
			<input type="text" size="80" name="wordtour_settings[facebook_app_id]" value="<?php echo $options["facebook_app_id"]?>"></input>	
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">App Secret</div>
		<div>
			<input type="text" size="80" name="wordtour_settings[facebook_app_secret]" value="<?php echo $options["facebook_app_secret"]?>"></input>	
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Status Template</div>
		<div>
			<textarea style='font-size:11px;height:70px;' name="wordtour_settings[facebook_status_template]"><?php echo $options["facebook_status_template"];?></textarea>	
		</div>	
	</div>
<?php 
	wt_dynamic_panel_end();
}

function wordtour_settings_twitter_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Twitter - <a style=\"font-size:12px;font-weight:normal;color:#21759B;text-decoration:none;\" target=\"_blank\" href=\"http://dev.twitter.com/anywhere/apps/new\">Register</a>","id"=>"wordtour-panel-twitter"),"twitter",$collapsed); 
	global $_wt_options;
	$options = $_wt_options->options();
?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">@Anywhere API Key</div>
			<div>
				<input type="text" autocomplete="off" name="wordtour_settings[twitter_api_key]" value="<?php echo $options["twitter_api_key"]?>"></input>	
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Callback URL</div>
			<div>
				<textarea style='height:50px;font-size:11px;' disabled="true"><?php echo WT_PLUGIN_URL;?></textarea>	
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Status Template</div>
			<div>
				<textarea style='font-size:11px;height:70px;' name="wordtour_settings[twitter_template]"><?php echo $options["twitter_template"];?></textarea>	
			</div>	
		</div>
	<?php 
	wt_dynamic_panel_end();
}

function wordtour_settings_lastfm_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Last.FM - <a style=\"font-size:12px;font-weight:normal;color:#21759B;text-decoration:none;\" href=\"http://www.last.fm/api/\" target=\"_blank\">Register</a>","id"=>"wordtour-panel-lastfm"),"lastfm",$collapsed); 
	global $_wt_options;
	$options = $_wt_options->options();
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Key</div>
		<div>
			<input type="text" name="wordtour_settings[lastfm_key]" size="80" value="<?php echo $options["lastfm_key"]?>"></input>	
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Secret</div>
		<div>
			<input type="text" name="wordtour_settings[lastfm_secret]" value="<?php echo $options["lastfm_secret"];?>"></input>	
		</div>	
	</div>
<?php 
	wt_dynamic_panel_end();
}

function wordtour_settings_eventbrite_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Eventbrite - <a style=\"font-size:12px;font-weight:normal;color:#21759B;text-decoration:none;\" href=\"http://www.eventbrite.com/r/wt/\" target=\"_blank\">Register</a>","id"=>"wordtour-panel-eventbrite"),"eventbrite",$collapsed); 
	global $_wt_options;
	$options = $_wt_options->options();
	$eventbrite = new WT_Eventbrite();
	$auto_eventbrite_checked = $eventbrite->is_update() == 1 ? "checked=1" : "";
	$import_button_state =$eventbrite->is_init() == 1 ? "" : "disabled='true'";
	$organizer_id = $eventbrite->get_organizer_id();
?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">User Key *</div>
		<div>
			<input type="text" size="80" name="wordtour_settings[eventbrite_user_key]" value="<?php echo $options["eventbrite_user_key"];?>"></input>	
		</div>	
	</div>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Application Key *</div>
		<div>
			<input type="text" size="80" name="wordtour_settings[eventbrite_app_key]" value="<?php echo $options["eventbrite_app_key"];?>"></input>	
		</div>	
	</div>
	
	<?php if($organizer_id) { ?>
	<div class="wordtour-field wordtour-field-block">
		<div class="label">Organizer ID</div>
		<div>
			<input type="text" size="80" name="wordtour_settings[eventbrite_organizer_id]" value="<?php echo $organizer_id;?>"></input>	
		</div>	
	</div>
	<?php } ?>
	<div class="wordtour-field wordtour-field-block">
		<input style="width:inherit;" type="checkbox" <?php echo $auto_eventbrite_checked;?> name="wordtour_settings[eventbrite_auto_update]" value="1"/> Auto Update Event and Venue Info	
	</div>
	
	<div class="wordtour-field wordtour-field-block">
		<button id="eventbrite-import-button" style='width:100%' <?php echo $import_button_state;?>>Import Event and Venues</button>	
	</div>
	
	
<?php 
	wt_dynamic_panel_end();
}

function wordtour_settings_flickr_panel($collapsed = 0) {
	wt_dynamic_panel_start(array("title"=>"Flickr - <a style=\"font-size:12px;font-weight:normal;color:#21759B;text-decoration:none;\" href=\"http://www.flickr.com/services/apps/create/apply/\" target=\"_blank\">Register</a>","id"=>"wordtour-panel-flickr"),"flickr",$collapsed); 
	global $_wt_options;
	$options = $_wt_options->options();
?>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Key</div>
			<div>
				<input type="text" name="wordtour_settings[flickr_key]" size="80" value="<?php echo $options["flickr_key"]?>"></input>	
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Secret</div>
			<div>
				<input type="text" name="wordtour_settings[flickr_secret]" size="80" value="<?php echo $options["flickr_secret"]?>"></input>	
			</div>	
		</div>
		<div class="wordtour-field wordtour-field-block">
			<div class="label">Machinetag Prefix</div>
			<div>
				<input type="text" name="wordtour_settings[flickr_namespace]" size="80" value="<?php echo $options["flickr_namespace"]?>"></input>
				<p class="help">
				Please Read Licensed Uses and Restrictions before using Flickr on your website <a href="http://www.flickr.com/services/api/tos/">Click Here</a>
				</p>	
			</div>	
		</div>
<?php 
	wt_dynamic_panel_end();
}





	
	