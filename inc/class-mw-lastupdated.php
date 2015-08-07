<?php
class mw_lastupdated {

  protected $loader;
  protected $data_container;

  public function __construct() {
    require_once plugin_dir_path( __FILE__ ) . 'class-mw-lastupdated-data-container.php';
    $this->data_container = new mw_lastupdated_data_container();
    $this->data_container->set('plugin_slug', 'mw-lastupdated');
    $this->data_container->set('DIR', plugin_dir_path( dirname(  __FILE__ ) ) );
    $this->data_container->set('URL', plugin_dir_url( dirname( __FILE__ ) ) );

    $this->load_dependencies();
    $this->define_widget_hooks();
    if ( is_admin() ) {
      $this->define_admin_hooks();
    }
  }

  private function load_dependencies() {
    if ( is_admin() ) {
      require_once $this->data_container->get('DIR') . 'inc/class-mw-lastupdated-admin.php';
    }
    require_once $this->data_container->get('DIR') . 'inc/class-mw-lastupdated-widget.php';
    require_once $this->data_container->get('DIR') . 'inc/class-mw-lastupdated-loader.php';
    $this->loader = new mw_lastupdated_loader();
  }

  private function define_widget_hooks() {
    $register_widget = new register_mw_lastupdated_widget();
    $this->loader->add_action( 'widgets_init', $register_widget, 'register' );
  }

  private function define_admin_hooks() {
    $admin = new mw_lastupdated_admin( $this->data_container );
    $this->loader->add_action( 'post_submitbox_start', $admin, 'add_controls' );
    $this->loader->add_action( 'mce_external_plugins', $admin, 'add_tinyMCE_plugins');
    $this->loader->add_action( 'save_post', $admin, 'save_date', 10, 3 );
  }

  public function run() {
    $this->loader->run();
  }
}
