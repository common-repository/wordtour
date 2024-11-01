<?php
class WT_ArtistsWidget extends WP_Widget {
    /** constructor */
    function WT_ArtistsWidget() {
        parent::WP_Widget(false, $name = 'WordTour Artists');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract($args);
        
        $title = apply_filters('widget_title',$instance['title']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title ) echo $before_title . $title . $after_title; ?>
                  <?php 
                  $artists = WT_Artist::all();
                  echo "<ul>";
                  foreach($artists as $artist) {
                  	echo "<li><strong><a href='".wt_get_permalink("artist",$artist["artist_id"],array("%name%"=>$artist["artist_name"]))."'>".$artist["artist_name"]."</a></strong></li>";
                  }
                  echo "</ul>";
                  ?>
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        $type = $instance['type'];
        ?>
            <p>
            	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> 
            	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            	</label>
            </p>
        <?php 
    }

}

class WT_EventsWidget extends WP_Widget {
    /** constructor */
    function WT_EventsWidget() {
        parent::WP_Widget(false, $name = 'WordTour Events');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract($args);
        
        $title = apply_filters('widget_title',$instance['title']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title ) echo $before_title . $title . $after_title; ?>
                  <?php 
                  	$renderer = new WT_Renderer();
                  	$renderer->events($instance);	
                  ?>
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
    	global $_wt_options;
    	$title = esc_attr($instance['title']);
        $date = $instance['date_range'];
        $order = $instance['order'];
        $group_by = $instance['group_by'];
        $group_by_tour = $instance['group_by_tour'];
        $artists = $instance['artists'];
        $tour = $instance['tour'];
        $limit = $instance['limit'];
        ?>
        	<input type="hidden" id="<?php echo $this->get_field_id('render_type'); ?>" name="<?php echo $this->get_field_name('render_type'); ?>" type="text" value="widget" />
            <p>
            	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> 
            	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            	</label>
            </p>
            <p>
            	<label>Type
            	<select class="widefat" id="<?php echo $this->get_field_id('range'); ?>" name="<?php echo $this->get_field_name('date_range'); ?>">
            		<option <?php if($date =="UPCOMING") echo "selected='true'" ;?> value="UPCOMING">Upcoming</option>
            		<option <?php if($date =="TODAY") echo "selected='true'" ;?> value="TODAY">TODAY</option>
            		<option <?php if($date =="ARCHIVE") echo "selected='true'" ;?> value="ARCHIVE">ARCHIVE</option>
            	</select>
            	</label>
            </p>
            <p>
            	<label>Order By
            	<select class="widefat" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
            		<option <?php if($date =="DESC") echo "selected='true'" ;?> value="DESC">Descending</option>
            		<option <?php if($order =="ASC") echo "selected='true'" ;?> value="ASC">Ascending</option>
            	</select>
            	</label>
            </p>
             <p>
            	<label>Limit
            	     <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>" />
            	</label>
            </p>
            <p>
            	<label>Theme
            	<select class="widefat" id="<?php echo $this->get_field_id('theme'); ?>" name="<?php echo $this->get_field_name('theme'); ?>">
            		<?php 
    				foreach(wt_get_themes() as $theme){
						$selected_theme = $_wt_options->options("default_theme") == $theme ? "selected='true'" : "";
						echo "<option $selected_theme value='$theme'>".strtoupper($theme)."</option>";		  
					}
            		?>
            	</select>
            	</label>
            </p>
            
            
             
            <p>
            	<label>Group By Artist
            	<select class="widefat" id="<?php echo $this->get_field_id('group_by'); ?>" name="<?php echo $this->get_field_name('group_by'); ?>">
            		<option value="">NO</option>
            		<option <?php if($group_by =="ARTIST") echo "selected='true'" ;?> value="ARTIST">YES</option>
            	</select>
            	</label>
            </p>
            <p>
            	<label>Group By Tour
            	<select class="widefat" id="<?php echo $this->get_field_id('group_by_tour'); ?>" name="<?php echo $this->get_field_name('group_by_tour'); ?>">
            		<option value="0">NO</option>
            		<option <?php if($group_by_tour =="1") echo "selected='true'" ;?> value="1">YES</option>
            	</select>
            	</label>
            </p>
            <p>
            	<label>Include Artists
            	<input class="widefat" id="<?php echo $this->get_field_id('artists'); ?>" name="<?php echo $this->get_field_name('artists'); ?>" type="text" value="<?php echo $artists; ?>" />
            	<br/>
            	<small>Artists IDs, separated by commas.</small>
            	</label>
            </p>
            <p>
            	<label>Include Tour
            	<input class="widefat" id="<?php echo $this->get_field_id('tour'); ?>" name="<?php echo $this->get_field_name('tour'); ?>" type="text" value="<?php echo $tour; ?>" />
            	<br/>
            	<small>Tour IDs, separated by commas.</small>
            	</label>
            </p>            
        <?php 
    }

} // class FooWidget

add_action('widgets_init', create_function('', 'return register_widget("WT_EventsWidget");'));
add_action('widgets_init', create_function('', 'return register_widget("WT_ArtistsWidget");'));

