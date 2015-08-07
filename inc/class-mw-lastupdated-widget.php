<?php
/**
 * Adds lastupdated widget.
 */
class mw_lastupdated_widget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'mw_lastupdated_widget', // Base ID
      __( 'Last Updated', 'mw-lastupdated' ), // Name
      array( //Args
        'description' => __( 'Displays last updated posts', 'mw-lastupdated' ),
        'classname' => 'mw_lastupdated',
        )
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    global $wpdb;
    extract( $args );

    /* Our variables from the widget settings. */
    $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title'] ) : '';
    $amount = !empty($instance['amount']) ? $instance['amount'] : 5 ;
    $date_bool = !empty($instance['date']) ? $instance['date'] : false ;

    /* catch all possible post-type-names, look which are wanted and create a string for the sql-query */
    $post_types = get_post_types( '', 'names' );
    $i=0;
    $post_type_string = '(';
    foreach ( $post_types as $post_type ) {
      if( isset($instance['post-type-'.$post_type]) && $instance['post-type-'.$post_type] ) {
        if($i>0) $post_type_string .= ',';
        $post_type_string.= '"' . $post_type . '"';
        $i++;
      }
    }
    $post_type_string .= ')';

    if( $i>0 ) {

      $sql_significant_updated_posts = ' SELECT posts.ID,posts.post_title,pm1.mw_significant_update,pm2.mw_significant_update_gmt
                                  FROM ' . $wpdb->posts . ' posts
                                    INNER JOIN (
                                      SELECT postmeta.post_id,postmeta.meta_value AS mw_significant_update
                                      FROM ' . $wpdb->postmeta . ' postmeta
                                      WHERE postmeta.meta_key="mw_significant_update"
                                    ) AS pm1 ON posts.ID = pm1.post_id
                                    INNER JOIN (
                                      SELECT postmeta.post_id,postmeta.meta_value AS mw_significant_update_gmt
                                      FROM ' . $wpdb->postmeta . ' postmeta
                                      WHERE postmeta.meta_key="mw_significant_update_gmt"
                                    ) AS pm2 ON posts.ID = pm2.post_id
                                  WHERE posts.post_status = "publish" AND posts.post_type IN  ' . $post_type_string . '
                                  ORDER BY pm2.mw_significant_update_gmt DESC
                                  LIMIT ' . $amount;

      $significant_updated_posts = $wpdb->get_results($sql_significant_updated_posts);

      if ( $significant_updated_posts ) {
        /* Before widget (defined by themes). */
          echo $before_widget;

          /* Display the widget title if one was input (before and after defined by themes). */
         if ( $title )
           echo $before_title . $title . $after_title;

      echo '<ul>';

        foreach( $significant_updated_posts as $significant_updated_post ) { ?>
          <li>
            <a href="<?php echo get_permalink($significant_updated_post->ID)?>">
              <?php echo $significant_updated_post->post_title ?>
            </a>
            <?php if($date_bool) : ?>
              <time class="mw_lastupdated_time" datetime="<?php echo $significant_updated_post->mw_significant_update ?>">
                <?php
                  $date = strtotime($significant_updated_post->mw_significant_update);
                  echo date( get_option('date_format'), $date );
                ?>
              </time>
            <?php endif; ?>
          </li>
        <?php }

      echo '</ul>';

         /* After widget (defined by themes). */
         echo $after_widget;
      }
    }
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    global $wp_post_types;

    /* Set up some default widget settings. */
    $defaults = array( 'title' => __('last updated','mw-lastupdated'), 'amount' => 5, 'date' => true );
    $post_types = get_post_types( '', 'names' );
    foreach ( $post_types as $post_type ) {
      $defaults[ 'post-type-' . $post_type ] = false;
    }
    $defaults[ 'post-type-post' ] = true;
    reset( $post_types );
    $instance = wp_parse_args( (array) $instance, $defaults ); ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('title:', 'mw-lastupdated'); ?></label>
      <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"  class="widefat" >
     </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'amount' ); ?>"><?php _e('amount (standard is 5):','mw-lastupdated') ?></label>
      <input type="number" id="<?php echo $this->get_field_id( 'amount' ); ?>" name="<?php echo $this->get_field_name( 'amount' ); ?>" value="<?php echo $instance['amount']; ?>"  class="widefat" >
    </p>
    <p>
      <label><?php _e('post-types:', 'mw-lastupdated'); ?></label><br>
      <?php foreach ( $post_types as $post_type ) { ?>
        <input class="checkbox" type="checkbox" <?php checked( $instance['post-type-'.$post_type], true ); ?> id="<?php echo $this->get_field_id( 'post-type-'.$post_type ) ?>" name="<?php echo $this->get_field_name( 'post-type-'.$post_type ); ?>" />
        <label for="<?php echo $this->get_field_id( 'post-type-'.$post_type ) ?>"><?php echo $wp_post_types[$post_type]->labels->name ?></label><br>
      <?php } ?>
    </p>
    <p>
      <input class="checkbox" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" type="checkbox" <?php checked(isset($instance['date']) ? $instance['date'] : 0); ?> >
      <label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e('Should the date be showen?','mw-lastupdated'); ?></label>
    </p>

    <?php
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
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

} // class mw_lastupdated_widget

// register mw_lastupdated widget
class register_mw_lastupdated_widget {
  public function register() {
    register_widget( 'mw_lastupdated_widget' );
  }
}
