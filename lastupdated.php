<?php
/*
 * Plugin Name: last updated
 * Plugin URI: http://www.martin.wudenka.de/wordpress-widget-zuletzt-aktualisierte-posts-anzeigen/
 * Description: Shows posts and pages last updated.
 * Version: 1.6.1
 * Author: Martin Wudenka
 * Author URI: http://www.martin.wudenka.de
 */
 
/*
 * sources:
 * http://justintadlock.com/archives/2009/05/26/the-complete-guide-to-creating-widgets-in-wordpress-28
 */
 
/*
 * Translation
 */ 
load_plugin_textdomain( 'lastupdated', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'mw_load_lastupdated' );

/*
 * Register our widget.
 */
function mw_load_lastupdated() {
	register_widget( 'mw_lastupdated' );
}

/*
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 */
class mw_lastupdated extends WP_Widget {

	/*
	 * Widget setup.
	 */
	function mw_lastupdated() {
   	/* Widget settings. */
     	$widget_ops = array( 'classname' => 'lastupdated', 'description' => __('Shows posts and pages last updated.', 'lastupdated') );

   	/* Widget control settings. */
    	$control_ops = array( 'id_base' => 'lastupdated-widget' );

    	/* Create the widget. */
   	$this->WP_Widget( 'lastupdated-widget', __('last updated', 'lastupdated'), $widget_ops, $control_ops );
	}

	/*
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		global $wpdb;
    	extract( $args );

    	/* Our variables from the widget settings. */
     	$title = apply_filters('widget_title', $instance['title'] );
     	$amount = apply_filters('widget_amount', $instance['amount'] );
    	$date_bool = $instance['date'];
     	if (empty($amount))
     		$amount = 5;

		/* catch all possible post-type-names, look which are wanted and create a string for the sql-query */
     	$post_types = get_post_types( '', 'names' ); 
		$i=1;
		$post_type_string='( ';
		foreach ( $post_types as $post_type ) {
			if($instance['post-type-'.$post_type]) {
				if($i>1) $post_type_string.= 'OR ';
  				$post_type_string.='post_type = "' . $post_type . '" ';
  				$i++;
  			}
		}
		$post_type_string.=' )';
     	
    	$sql_create_date = "SELECT ID,post_title FROM " . $wpdb->posts . " WHERE post_status = 'publish' AND " . $post_type_string . " AND post_modified_gmt < '" . current_time('mysql', 1) . "' ORDER BY post_date_gmt DESC";
    	$sql_update_date = "SELECT ID,post_title FROM " . $wpdb->posts . " WHERE post_status = 'publish' AND " . $post_type_string . " AND post_modified_gmt < '" . current_time('mysql', 1) . "' ORDER BY post_modified_gmt DESC";
    	
    	$create_date = $wpdb->get_results($sql_create_date);
    	$update_date = $wpdb->get_results($sql_update_date);
    	
    	/* only posts that are higher located in sql_update_date than in sql_create_date */
    	$j=0;
    	$i=0;
    	$recentposts=array();
    	while ($i<count($update_date)) {
    		$k=0;
    		foreach($create_date as $create_date_single) {
				if(($update_date[$i]->ID == $create_date_single->ID) && ( $i<$k ) && ( $j<$amount )) { $recentposts[$j]=$update_date[$i]; $j++;} 
				$k++;   		
    		}
    		$i++;
    	}
    	
    	     
      if ($recentposts) {
      	/* Before widget (defined by themes). */
        	echo $before_widget;

        	/* Display the widget title if one was input (before and after defined by themes). */
       	if ( $title )
         	echo $before_title . $title . $after_title;

			echo '<ul>';
			                                               
       	if($date_bool) {                       	
         	foreach($recentposts as $recentpost) {
            	echo '<li>'.$recentpost->ord.'<a href="'.get_permalink($recentpost->ID).'">'.$recentpost->post_title.'</a> (' . get_post_modified_time( get_option('date_format'), false, $recentpost->ID, true ) . ') </li>';
         	}                          
       	}
        	else {
         	foreach($recentposts as $recentpost) {
             	echo '<li><a href="'.get_permalink($recentpost->ID).'">'.$recentpost->post_title.'</a></li>';
            }	
   		}
                           
			echo '</ul>';

       	/* After widget (defined by themes). */
       	echo $after_widget;
      	}
	}

	/*
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
    	$instance['title'] = strip_tags( $new_instance['title'] );
    	$instance['amount'] = strip_tags( $new_instance['amount'] );
    	$instance['date'] = (bool) $new_instance['date'];
    	
    	$post_types = get_post_types( '', 'names' ); 
		foreach ( $post_types as $post_type ) {
			$instance['post-type-'.$post_type] = (bool) $new_instance['post-type-'.$post_type];
		}

   	return $instance;
	}

	/*
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {
	global $wp_post_types;

	/* Set up some default widget settings. */
	$defaults = array( 'title' => __('last updated','lastupdated'), 'amount' => 5, 'date' => true );
	$instance = wp_parse_args( (array) $instance, $defaults ); ?>
	<p>
   	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('title:','lastupdated'); ?></label>
    	<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"  class="widefat" >
   </p>
	<p>
    	<label for="<?php echo $this->get_field_id( 'amount' ); ?>"><?php _e('amount (if empty, amount will be 5):','lastupdated'); ?></label>
 		<input type="text" id="<?php echo $this->get_field_id( 'amount' ); ?>" name="<?php echo $this->get_field_name( 'amount' ); ?>" value="<?php echo $instance['amount']; ?>"  class="widefat" >
	</p>
	<p>
		<label><?php _e('post-types:','lastupdated'); ?></label><br>	
		<?php $post_types = get_post_types( '', 'names' ); ?>
		<?php foreach ( $post_types as $post_type ) { ?>
			<input class="checkbox" type="checkbox" <?php checked( $instance['post-type-'.$post_type], true ); ?> id="<?php echo $this->get_field_id( 'post-type-'.$post_type ); ?>" name="<?php echo $this->get_field_name( 'post-type-'.$post_type ); ?>" /> 
   		<label for="<?php echo $this->get_field_id( 'post-type-'.$post_type ); ?>"><?php echo $wp_post_types[$post_type]->labels->name; ?></label><br>
		<?php } ?>
	</p>
	<p>
		<input class="checkbox" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" type="checkbox" <?php checked(isset($instance['date']) ? $instance['date'] : 0); ?> > 
		<label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e('Should the date be showen?','lastupdated'); ?></label>
	</p>

<?php
	}
}
?>