<?php
/*
Plugin Name: Social Links Widgets
Plugin URI: http://patrick.forringer.com/wordpress/plugin/SLW
Description: Allows you to place social media links in your sidebar in an organized fasion.
Version: 0.2
Author: Patrick Forringer
Author URI: http://patrick.forringer.com

TODO: ordering functionality to raise and lower items being output. how? reordering array and saving that.
TODO: styling for output, and seperate front facing css sheet with icons.
*/

define( 'SLW_VER', 0.2);

/* Set constant path to the SLW plugin directory. */
define( SLW_DIR, plugin_dir_path( __FILE__ ) );

/* Set constant path to the SLW plugin URL. */
define( SLW_URL, plugin_dir_url( __FILE__ ) );

// Register Scripts and Styles
wp_register_script( 'autoresize.jquery', SLW_URL . '/js/autoresize.jquery.min.js', array('jquery'), '1.04');
wp_register_script( 'jquery.color', SLW_URL . '/js/jquery.color.js', array('jquery'), '1');

wp_register_script( 'slw_widget_js', SLW_URL . '/js/SLW_func.js', array('jquery','autoresize.jquery','jquery.color'), '0.1'); //,'autoresize.jquery','jquery.autofill'
wp_register_style( 'slw_widget_styles', SLW_URL . '/css/SLW_styles.css', null,  '0.1', 'screen' );
wp_register_style( 'slw_frontend_styles', SLW_URL . '/css/SLW_FE_styles.css', null,  '0.1', 'screen' );


/* Launch the plugin. */
add_action( 'plugins_loaded', 'slw_plugin_init' );

function slw_plugin_init(){
	// ini widget
	add_action( 'widgets_init', create_function('', 'return register_widget("Social_Links_Widgets");') );
}

class Social_Links_Widgets extends WP_Widget{
	
	// array of networks and their icon links
	var $types = array(
		'facebook'	=> array('Facebook','http://facebook.com/'),
		'youtube'		=> array('youtube', 'http://youtube.com/'),
		'twitter'		=> array('twitter', 'http://twitter.com/'),
		'rss'				=> array('RSS', ''),
	);
	
	function Social_Links_Widgets() {
			$widget_ops = array('description' => __( "Add your social links to your website's sidebar" ) );
			
			// Add admin_header CSS and JS
			global $pagenow;
			
			if( is_admin() and $pagenow == 'widgets.php'): // js and css for admin area. TODO: find out how to only output on widgets page.
				wp_enqueue_script('slw_widget_js');
				wp_enqueue_style('slw_widget_styles');
			else: // load styles and scripts for theme.
				//wp_enqueue_style('slw_frontend_styles');
			endif;
			
      parent::WP_Widget(false, 'Social Links', $widget_ops);	
  }
  
	function widget($args, $instance) {
	
	  extract( $args );
	  $title = apply_filters( 'widget_title', $instance['title'] );
	  
	  $s_links = (array) $instance['social_links_texts'];
	  
	  echo $before_widget;
		echo $before_title.$title.$after_title;
	
		echo '<ul>';
			
			foreach( $s_links as $link ):
			
				extract($link);
				echo '<li class="'.$type.'">';
				echo '<a href="'.$link.'" >'.$text.'</a>';
				//print_r($link);
				echo '</li>';
			endforeach;
			
		echo '</ul>';
		
		echo $after_widget;	
	}
	
	function update( $new_instance, $old_instance ) {
		
		$new_instance = (array) $new_instance;
		
		// TODO check for new social link and add it.
		if( $new_type = $new_instance['add_link'] ):
			$new_id = substr( md5(rand(0,5)), 0, 4); // rand identifyer for link
			$new_instance['social_links_texts'][$new_id] = array(
				'type' => $new_type,
				'link' => $this->types[$new_type][1], // assign default link for type
				'text' => ''
				);
			
		endif;
		
		// TODO: resave updated title and link data
		$links = $new_instance['links'];
		foreach( $links as $id => $val):
			// check to see if we are being deleted, in which case don't update == removed
			if(!$val['delete'])
				$new_instance['social_links_texts'][$id] = array_merge( $old_instance['social_links_texts'][$id], (array) $val );
			
		endforeach;
		
		// don't need links saved.
		unset($new_instance['links']);
		
		return $new_instance;
	}
	
	// New value arrays to handle
	function get_field_name_array( $name, $id, $kind ){
		return $this->get_field_name($name).'['.$id.']'.'['.$kind.']';
	}
	
	function form($instance) {
		
		$title = esc_attr( $instance['title'] );
		$links = (array) $instance['social_links_texts'];
				
		?>
	    <p>
	    	<label for="<?= $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
		    	<input class="widefat" id="<?= $this->get_field_id('title'); ?>" name="<?= $this->get_field_name('title'); ?>" type="text" value="<?= $title; ?>" />
	    	</label>
	    </p>
	    <? foreach($links as $id => $link):
	    //$link 
	    $type = $link['type'];
	    $link_link = $link['link'];
	    ?>
		    <p class="social_link">
		    <label for="<?= $this->get_field_id($id); ?>"><?php _e( $this->types[$type][0] ); ?> Link:
		    	<input class="widefat" id="<?= $this->get_field_id($id); ?>" name="<?= $this->get_field_name_array('links', $id, 'link'); ?>" type="text" value="<?= $link_link; ?>" />
		    </label>
		    <textarea class="widefat" name="<?= $this->get_field_name_array('links', $id, 'text' ); ?>"><?=esc_attr( $link['text'])?></textarea>
		    <span class="to_delete"><span>click to delete</span> <input type="checkbox" name="<?= $this->get_field_name_array('links', $id, 'delete' ); ?>" value="1"/></span>
		    </p>
	    <? endforeach; ?>
	    <p>
	    	<select class="widefat" name="<?= $this->get_field_name('add_link'); ?>">
	    	<option value=""><?php _e('Add New Link'); ?></option>
	    <? foreach( $this->types as $type => $val): ?>
			    <option value="<?=$type?>"><?=$val[0]?></option>
	    <? endforeach; ?>
	    	</select>
	    	
	    </p>
	    <p><span>click save to create a new link</span></p>
		<?php
		
	}

}