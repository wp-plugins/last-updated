<?php
class mw_lastupdated_admin {

  protected $data_container;

  public function __construct( $data_container ) {
    $this->data_container = $data_container;
  }

  public function add_controls() {
    global $post;
    if ( $post->post_status === 'publish' && 0 != $post->ID ) {
      ?>
      <p>
        <fieldset>
          <div><?php _e('Is this a significant update?', 'mw-lastupdated' ) ?></div>
          <input type="radio" id="mw_significant_update_true" name="mw_significant_update" value="true"><label for="mw_significant_update_true"><?php _e('Yes', 'mw-lastupdated' ) ?></label>
          <input type="radio" id="mw_significant_update_false" name="mw_significant_update" value="false" checked="checked"><label for="mw_significant_update_false"><?php _e('No', 'mw-lastupdated' ) ?></label>
        </fieldset>
      </p>
      <?php
    }
  }

  public function save_date( $post_id, $post, $update ) {
    if ( isset( $_REQUEST['mw_significant_update'] ) && $_REQUEST['mw_significant_update'] === 'true' ) {
      $current_time = current_time( 'mysql' );
      update_post_meta( $post_id, 'mw_significant_update', $current_time );
      update_post_meta( $post_id, 'mw_significant_update_gmt', get_gmt_from_date($current_time) );
    }
  }

  public function add_tinyMCE_plugins() {
    $plugins_array = array();
    $plugins_array['mw_detect_significant_update'] = $this->data_container->get('URL') . 'js/mw_lastupdated_script.js';
    return $plugins_array;
  }

}
