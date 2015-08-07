<?php
/*
 * Plugin Name: last updated
 * Plugin URI: http://www.martin.wudenka.de/wordpress-widget-zuletzt-aktualisierte-posts-anzeigen/
 * Description: Shows last updated posts. All post-types supported, custom as well.
 * Version: 2.0
 * Author: Martin Wudenka
 * Author URI: http://www.martin.wudenka.de
 */

/**
 * Security
 */
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Translation
 */
function mw_load_plugin_textdomain() {
  load_plugin_textdomain( 'mw-lastupdated', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'mw_load_plugin_textdomain' );

/**
 * Load the main file
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/class-mw-lastupdated.php';

/**
 * start the plugin
 */
function run_mw_lastupdated() {
  $plugin = new mw_lastupdated();
  $plugin->run();
}

run_mw_lastupdated();

/**
 * remove post meta on uninstall
 */
register_uninstall_hook( __FILE__ , 'mw_lastupdated_delete_post_meta');

function mw_lastupdated_delete_post_meta() {
  if ( ! current_user_can( 'activate_plugins' ) ) return;
  delete_post_meta_by_key( 'mw_significant_update' );
  delete_post_meta_by_key( 'mw_significant_update_gmt' );
}
