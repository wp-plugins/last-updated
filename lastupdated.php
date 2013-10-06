<?php
/**
 * Plugin Name: last updated
 * Plugin URI: http://www.martin.wudenka.de/wordpress-widget-zuletzt-aktualisierte-posts-anzeigen/
 * Description: Shows posts and pages last updated.
 * Version: 1.3
 * Author: Martin Wudenka
 * Author URI: http://www.martin.wudenka.de
 * License: CC-BY-SA 3.0
 * License URI: http://creativecommons.org/licenses/by-sa/3.0
 */
 
 /*
 * sources: 
 * http://www.galuba.net/programmierung/wordpress/tipps-tricks/wordpress-zuletzt-aktualisierte-artikel-seiten-anzeigen.html
 * http://talkpress.de/artikel/wordpress-widget-programmieren
 */
 
/**
 * Translation
 */ 
load_plugin_textdomain( 'lastupdated', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


/**
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'mw_load_lastupdated' );

/**
 * Register our widget.
 */
function mw_load_lastupdated() {
        register_widget( 'mw_lastupdated' );
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 */
class mw_lastupdated extends WP_Widget {

        /**
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

        /**
         * How to display the widget on the screen.
         */
        function widget( $args, $instance )
                  {
                  global $wpdb;
                  extract( $args );

                  /* Our variables from the widget settings. */
                  $title = apply_filters('widget_title', $instance['title'] );
                  $number = apply_filters('widget_number', $instance['number'] );
                  $date_bool = $instance['date'];
                  if (empty($number))
                           $number = 5;        
                
                  $recentposts = $wpdb->get_results("SELECT ID, post_title, post_modified FROM $wpdb->posts WHERE post_status = 'publish' AND (post_type = 'post' OR post_type = 'page') AND post_modified_gmt < '".current_time('mysql', 1)."' ORDER BY post_modified_gmt DESC LIMIT ".$number );
                  
                  if ($recentposts) 
                           {
                           /* Before widget (defined by themes). */
                           echo $before_widget;

                           /* Display the widget title if one was input (before and after defined by themes). */
                           if ( $title )
                                    echo $before_title . $title . $after_title;

									echo '<ul>';                           
                                    
                           if($date_bool) {                       	
                           	foreach($recentposts as $recentpost) {
                              	echo '<li><a href="'.get_permalink($recentpost->ID).'">'.$recentpost->post_title.'</a> ('.date(get_option('date_format'),strtotime($recentpost->post_modified)).') </li>';
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

        /**
         * Update the widget settings.
         */
        function update( $new_instance, $old_instance ) {
                $instance = $old_instance;

                /* Strip tags for title and name to remove HTML (important for text inputs). */
                $instance['title'] = strip_tags( $new_instance['title'] );
                $instance['number'] = strip_tags( $new_instance['number'] );
                $instance['date'] = (bool) $new_instance['date'];

                return $instance;
        }

        /**
         * Displays the widget settings controls on the widget panel.
         * Make use of the get_field_id() and get_field_name() function
         * when creating your form elements. This handles the confusing stuff.
         */
        function form( $instance ) {

                /* Set up some default widget settings. */
                $defaults = array( 'title' => __('last updated','lastupdated'), 'number' => 5 );
                $instance = wp_parse_args( (array) $instance, $defaults ); ?>

                <!-- Widget Title: Text Input -->
                <p>
                        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('title:','lastupdated'); ?></label>
                        <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
                </p>
                <p>
                        <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e('number (if empty, number will be 5):','lastupdated'); ?></label>
                        <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" style="width:100%;" />
                </p>
                
                <p>
							<input id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" type="checkbox" <?php checked(isset($instance['date']) ? $instance['date'] : 0); ?> /> 
							<label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e('Should the date be showen?','lastupdated'); ?></label>
					</p>

        <?php
        }
}

?>