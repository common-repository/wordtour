<?php
class WT_Theme {
	private $tpl; 
	private $theme ;
	private $file = 0;
	
	public function __construct(){
		global $_wt_options;
		$this->tpl = new Dwoo();
		// get theme defualt folder nane
		$this->theme = $_wt_options->options("default_theme");
		// if not exist, use "default" folder	
		if(empty($this->theme)) $this->theme = "jqueryui";
		// get theme root
		$this->path =  realpath(wt_get_theme_path().$this->theme);
		if(!is_dir($this->path)) $this->path = realpath(WT_THEME_PATH."jqueryui");
		require_once($this->path."/functions.php");
	}
	
	public function path_url(){
		return WT_THEME_URL.$this->theme; 
	}
	
	public function theme($file_path,$data = 0,$output = true){
		try {
			if(wt_is_writable(WT_PLUGIN_PATH."dwoo/compiled/")) {
				global $_wt_options;
				$theme_path = wt_get_theme_path();
				$this->tpl = new Dwoo();
				if($data) {
					if($this->file) {
						$path = realpath($this->file);
						$this->remove_file_path();	
					} else {
						$path = realpath($this->path.'/'.$file_path);	
					}
					
					if(file_exists($path)){
						if($output) {
							$this->tpl->output($path,$data);	
						} else {
							return $this->tpl->get($path,$data);
						}
					} else {
						echo "File Path <i>$file_path</i> doesnt exist, please add it to your theme library";
					}	
				}
			} else {
				throw new Exception("In order to display results, WordTour is using DWOO framework that requires write permission on folder <i>'".WT_PLUGIN_PATH."dwoo/compiled'</i>
					Please set your permission level to read/write. contact your server administrator if WordPress is being hosted on a Windows server For Unix and Linux <a href='http://codex.wordpress.org/Changing_File_Permissions'>click here</a>");
			}
		} catch(Exception $e) {
			if(!wt_is_writable(WT_PLUGIN_PATH."dwoo/compiled/")) {
				echo "<div class='error' style='background-color:#FFCCBF;padding:5px;border:1px solid #FF9980;'>An Error has Occurred<br/><i>".$e->getMessage()."</i></div>";
			}
		}
	}
	
	public function single_path(){
		return $this->path."/single.tpl.php";
	}
	
	public function set_file_path($file = 0) {
		$this->file = wt_get_default_theme_path()."/".$file;
	}
	
	public function remove_file_path() {
		$this->file = 0;
	}
	
	public function event($data = 0,$output = true){
		$data = apply_filters("event_single_template",$data);
		if($data) return $this->theme("event.tpl.php",$data,$output);
	}
	
	public function flickr($data = 0,$output = true){
		if($data) return $this->theme("flickr.tpl.php",$data,$output);
	}
	
	public function events($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "events.tpl.php";
		$data = apply_filters("events_template",$data);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function post_event($data = 0,$output = true,$file_name=0){
		$file = $file_name ? $file_name : "post.event.tpl.php";
		$data = apply_filters("event_post_template",$data);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function navigation($data = 0,$output = true){
		if($data) return $this->theme("navigation.tpl.php",$data,$output);
	}
	
	public function events_widget($data = 0,$output = true){
		$data = apply_filters("events_widget",$data);
		if($data) return $this->theme("events.widget.tpl.php",$data,$output);
	}
	
	public function events_by_date($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "events.by.date.tpl.php";
		$data = apply_filters("events_by_date_template",$data);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function artist($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "artist.tpl.php";
		$data = apply_filters("artist_single_template",$data);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function artists($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "artists.tpl.php";
		$data["data"] = apply_filters("artists_template",$data["data"]);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function albums($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "albums.tpl.php";
		$data["data"] = apply_filters("albums_template",$data["data"]);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function similar_albums($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "similar.albums.tpl.php";
		$data["data"] = apply_filters("similar_albums_template",$data["data"]);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function album($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "album.tpl.php";
		$data = apply_filters("album_single_template",$data);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function tours($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "tours.tpl.php";
		$data["data"] = apply_filters("tours_template",$data["data"]);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function tour($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "tour.tpl.php";
		$data = apply_filters("tour_single_template",$data);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function venue($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "venue.tpl.php";
		$data = apply_filters("venue_single_template",$data);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function venues($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "venues.tpl.php";
		$data["data"] = apply_filters("venues_template",$data["data"]);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function bio($data = 0,$output = true,$file_name = 0){
		$file = $file_name ? $file_name : "bio.tpl.php";
		$data = apply_filters("bio_single_template",$data);
		if($data) return $this->theme($file,$data,$output);
	}
	
	public function posts($data = 0,$output = true){
		$data = apply_filters("posts_template_params",$data);
		if($data) return $this->theme("posts.tpl.php",$data,$output);
	}
	
	public function gallery($data = 0,$output = true){
		$data = apply_filters("gallery_template_params",$data);
		if($data) return $this->theme("gallery.tpl.php",$data,$output);
	}
	
	public function tracks($data = 0,$output = true){
		$data = apply_filters("tracks_template_params",$data);
		if($data) return $this->theme("tracks.tpl.php",$data,$output);
	}
	
	public function videos($data = 0,$output = true){
		$data = apply_filters("video_template_params",$data);
		if($data) return $this->theme("videos.tpl.php",$data,$output);
	}
	
	public function videos_shortcode($data = 0,$output = true){
		$data = apply_filters("video_single_template_params",$data);
		if($data) return $this->theme("videos.single.tpl.php",$data,$output);
	}
	
	public function rsvp($data = 0,$output = true){
		if($data) return $this->theme("rsvp.tpl.php",$data,$output);
	}
	
	public function comments($data = 0,$output = true){
		if($data) return $this->theme("comments.tpl.php",$data,$output);
	}
	
	public function comment($data = 0,$output = true){
		if($data) return $this->theme("comment.tpl.php",$data,$output);
	}
}
