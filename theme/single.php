<?php	
	if($event) {
		if($event->data) {
			if(!$event->is_published()) return;
			echo "<form id='event-form'><input type='hidden' name='event_id' value='".$event->id."'></input></form>";
			$data = $event->template(null,1);
			$attachment = new WT_Attachment();
			if($event->is_gallery()) {
				$thumbnails = $event->gallery($event->data["event_gallery"]);
				$data["gallery_markup"] = $tpl->gallery(array("thumbnails"=>$thumbnails,"total"=>count($thumbnails)),false);
				
			};
			
			if($event->is_video()) {
				$videos = $event->video($event->data["event_videos"]);
				$data["video_markup"] = $tpl->videos(array("videos"=>$videos,"total"=>count($videos)),false);
			};
			
			if($event->is_flickr()) {
				$data["flickr_markup"] = $tpl->flickr($event->flickr("event"),false);
			};
			
			if($event->is_post()) {
				$posts = $event->posts($event->data["event_category"]);
				$data["news_markup"] = $tpl->posts(array("posts"=>$posts,"total"=>count($posts)),false);
			}
			
			if($event->is_rsvp()) {
				$rsvp = new WT_RSVP();
				$rsvp_users = $rsvp->get_users_by_event($event->id);		
				$data["attending_markup"] = $tpl->rsvp($rsvp->template($rsvp_users),false); 	
			};
			
			if($event->is_comments()) {
				$data["comments_markup"] = $tpl->comments($event->comments(),false);
			};

			$tpl->event($data);
			echo wt_load_script("all");
			echo wt_load_script("event");
		};
	}
	
	if($artist) {
		if($artist->is_artist()) {
			$data = $artist->template();
			$attachment = new WT_Attachment();
			
			if($artist->is_gallery()) {
				$thumbnails = $artist->gallery($artist->data["artist_gallery"]);
				$data["gallery_markup"] = $tpl->gallery(array("thumbnails"=>$thumbnails,"total"=>count($thumbnails)),false);
			};
			
			if($artist->is_video()) {
				$videos =$artist->video($artist->data["artist_videos"]);
				$data["video_markup"] = $tpl->videos(array("videos"=>$videos,"total"=>count($videos)),false);
			};
			
			if($artist->is_flickr()) {
				$data["flickr_markup"] = $tpl->flickr($artist->flickr("artist"),false);
			};
			
			if($artist->is_post()) {
				$posts = $artist->posts($artist->data["artist_category"]);
				$data["news_markup"] = $tpl->posts(array("posts"=>$posts,"total"=>count($posts)),false);
			}
			
			if($artist->is_tour()) {
				$data["tour_markup"] =  $artist->events(apply_filters("artist_events_shortcode_params",array()));
			}
			
			
			$tpl->artist($data);
			echo wt_load_script("all");
			echo wt_load_script("artist");
		}	
	}
	
	if($tour) {
		if($tour->is_tour()) {
			$data = $tour->template($tour->data);
			
			if($tour->is_gallery()) {
				$thumbnails = $tour->gallery($tour->data["tour_gallery"]);
				$data["gallery_markup"] = $tpl->gallery(array("thumbnails"=>$thumbnails,"total"=>count($thumbnails)),false);
			};
			
			if($tour->is_video()) {
				$videos =$tour->video($tour->data["tour_videos"]);
				$data["video_markup"] = $tpl->videos(array("videos"=>$videos,"total"=>count($videos)),false);
			};
			
			if($tour->is_flickr()) {
				$data["flickr_markup"] = $tpl->flickr($tour->flickr("tour"),false);
			};
			
			if($tour->is_post()) {
				$posts = $tour->posts($tour->data["tour_category"]);
				$data["news_markup"] = $tpl->posts(array("posts"=>$posts,"total"=>count($posts)),false);
			}
			// Change Tour Dates Display by defining parametrs as for shortcode
			if($tour->is_tour()) {
				$data["tour_markup"] =  $tour->events(apply_filters("tour_events_shortcode_params",array()));
			}
			$tpl->tour($data);
			echo wt_load_script("all");
			echo wt_load_script("tour");
		}
	}
	
	if($venue) {
		if($venue->data) {
			$data = $venue->template($venue->data);
			
			if($venue->is_gallery()) {
				$thumbnails = $venue->gallery($venue->data["venue_gallery"]);
				$data["gallery_markup"] = $tpl->gallery(array("thumbnails"=>$thumbnails,"total"=>count($thumbnails)),false);
			};
			
			if($venue->is_video()) {
				$videos =$venue->video($venue->data["venue_videos"]);
				$data["video_markup"] = $tpl->videos(array("videos"=>$videos,"total"=>count($videos)),false);
			};
			
			if($venue->is_flickr()) {
				$data["flickr_markup"] = $tpl->flickr($venue->flickr("venue"),false);
			};
			
			if($venue->is_post()) {
				$posts = $venue->posts($venue->data["venue_category"]);
				$data["news_markup"] = $tpl->posts(array("posts"=>$posts,"total"=>count($posts)),false);
			}
			
			if($venue->is_tour()) {
				$data["tour_markup"] =  $venue->events(apply_filters("venue_events_shortcode_params",array()));
			}
			$tpl->venue($data);
			echo wt_load_script("all");
			echo wt_load_script("venue");
		}
	}
	
	if($album) {
		if($album->is_album()) {
			$data = $album->template();
			
			if($album->is_tracks()) {
				$tracks = $album->tracks();
				$data["tracks_markup"] = $tpl->tracks(array("tracks"=>$tracks,"total"=>count($tracks)),false);
			};
			
			if($album->is_similar()) {
				$similar_albums = $album->similar();
				$data["similar_markup"] = $tpl->similar_albums(array("albums"=>$similar_albums,"total"=>count($similar_albums)),false);
			};
			
			$tpl->album($data);
			
			echo wt_load_script("all");
			echo wt_load_script("album");
		}	
	}
	
	
?>
