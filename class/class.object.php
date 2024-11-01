<?php
class WT_Object extends WT_Component {
	public function gallery($value = null) {
		$attachments = array();
		if(is_array($value)) {
			foreach($value as $gallery) {
				$gallery = new WT_Gallery($gallery["attachment_type_id"]);
				$gallery->retrieve();
				$attachments = array_merge($attachments,$gallery->get_attachments(array("thumbnail","large")));
			}
		}
		return $attachments;
	}
	
	public function video($videos= null) {
		if(is_array($videos)) {
			foreach($videos as $key=>$video) {
				$video_id = $video["attachment_type_id"];
				$videos[$key] = array("id"=>$video_id,"thumbnail"=>urldecode("http://img.youtube.com/vi/$video_id/2.jpg"));
			}
			return $videos;
		}
		return array(); 	
	}
	
	public function flickr($type = null) {
		if($type) {
			$photos = wt_flickr_by_machinetag(wt_get_machinetag($type,$this->id));
			return $photos;
		}
		
		return array();
	}
	
	public function posts($posts) {	
		global $post;	
		if(is_array($posts)) {
			$categories = array();
			foreach($posts as $category) {
				$categories[] = $category["attachment_type_id"];	
			}
			if(count($categories)) {
				$posts = get_posts('numberposts=5&category='.implode(",",$categories));
				foreach($posts as $k=>$v) {
					$post = $v;
					//print_r($post);
					//$v->post_content =wp_trim_excerpt($v->post_content,200);
					$v->post_content = $this->dbprepere->html_teaser_out($v->post_content);
					$posts[$k] = (array) $v;	
				}
				return $posts;
			}
		} else {
			$posts = array();
		}
	}
	// flag that control the update type wihile adding a new object
	public $quick_mode = 0;
	// Videos
	public function get_attachments($id=0,$target_type="",$type="") {
		$id = $id ? $id : $this->id;
		if($id && !empty($target_type)) {
			$attachments = new WT_Attachment();
			$result = $attachments->query("attachment_target=$target_type&attachment_target_id=$id&attachment_type=$type"); 
			if($result) return $result;
		}
		
		return array();
	}
	
	public function delete_attachments($id=0,$target_type="",$type="") {
		$id = $id ? $id : $this->id;
		if($id && !empty($target_type)) {
			$attachments = new WT_Attachment();
			$query = "attachment_target=$target_type&attachment_target_id=$id";
			if(!empty($type)) $query.= "&attachment_type=$type"; 
			$result = $attachments->delete($query); 
			if($result) return $result;
		}
		
		return array();
	}
	
	
	public function update_attachments($attachments = array(),$id=0,$target_type="",$type="") {
		$id = $id ? $id : $this->id;
		if($id && !empty($target_type)) {
			foreach($attachments as $attachment) {
				$attachment->attachment_target = $target_type;
				$attachment->attachment_type   = $type;
				$attachment->attachment_target_id = $id;
			}
			$attachment = new WT_Attachment();
			$attachment->delete("attachment_target_type=$target_type&attachment_target_id=$target_id&attachment_type=$type");
			return $attachment->insert($attachments);
		}
		return array();
	}
	// Galleries
	public function get_gallery($target_type="") {
		$id = $this->id;
		if($id && !empty($target_type)) return $this->get_attachments($id,$target_type,"gallery");
		return array();
	}
	
	public function update_gallery($galleries = array(),$target_type="") {
		$id = $this->id;
		if($id && !empty($target_type)) {
			$this->delete_attachments($id,$target_type,"gallery");
			return $this->update_attachments($galleries,$id,$target_type,"gallery");
		}
		return array();
	}
	// Videos
	public function get_videos($target_type="") {
		$id = $this->id;
		if($id && !empty($target_type)) return $this->get_attachments($id,$target_type,"video");
		return array();
	}
	
	public function update_videos($videos = array(),$target_type="") {
		$id = $this->id;
		if($id && !empty($target_type)) {
			$this->delete_attachments($id,$target_type,"video");
			return $this->update_attachments($videos,$id,$target_type,"video");
		}
		return array();
	}
	// Categories
	public function get_category($target_type="") {
		$id = $this->id;
		if($id && !empty($target_type)) return $this->get_attachments($id,$target_type,"category");
		return array();
	}
	
	public function update_category($categories = array(),$target_type="") {
		$id = $this->id;
		if($id && !empty($target_type)) {
			$this->delete_attachments($id,$target_type,"category");
			return $this->update_attachments($categories,$id,$target_type,"category");
		}
		return array();
	}
	
	
	// Thumbnail
	public function update_thumbnail($thumbnail = "",$target_type="") {
		$id = $this->id;
		if($id && !empty($target_type)) {
			$data = new stdClass;;
			$this->delete_attachments($id,$target_type,"thumbnail");
			
			if($thumbnail!="empty") {
				if(is_numeric($thumbnail)) {
					$data->attachment_type_id = $thumbnail;	
				} else {
					$data->attachment_type_info = $thumbnail;
				}
				return $this->update_attachments(array($data),$id,$target_type,"thumbnail");
			}
		}
		return array();
	}
	
	
	public function get_thumbnail($target_type="") {
		$id = $this->id;
		$thumbnails = array();
		if($id && !empty($target_type)) {
			$attachments = $this->get_attachments($id,$target_type,"thumbnail");
			foreach($attachments as $attachment) {
				if(!empty($attachment["attachment_type_id"])) {
					$thumbnails = $attachment["attachment_type_id"]>0 ? $attachment["attachment_type_id"] : 0;	
				} else {
					$thumbnails = array("url"=>$attachment["attachment_info"]);
				}
			}
		}
		return $thumbnails;
	}
	
	// Genre
	public function get_genre($target_type="") {
		$id = $this->id;
		$genres = array();
		if($id && !empty($target_type)) {
			$attachments = $this->get_attachments($id,$target_type,"genre");
			foreach($attachments as $attachment) {
				$genres[] = array($attachment["attachment_info"]);
			}
		}
		return $genres;
	}
	
	public function get_genre_tpl($target_type="") {
		$genres  = $this->get_genre($target_type);
		$genre = array();
		foreach($genres as $g) {
			$genre[] = ucfirst($g[0]);
		}
		return $genre;
	}
	
	public function update_genre($genres = array(),$target_type="") {
		$id = $this->id;
		$attachments = array();
		if($id && !empty($target_type)) {
			foreach($genres as $genre_name) {
				$attachments[] = (object) array("attachment_info"=>$genre_name);
				wordtour_add_genre($genre_name);			
			}
			$this->delete_attachments($id,$target_type,"genre");
			return $this->update_attachments($attachments,$id,$target_type,"genre");
		} 
		return array();	
	}
	
}
