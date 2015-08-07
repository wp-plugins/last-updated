<?php
class mw_lastupdated_data_container {
  private $plugin_data = array();

  public function set($key, $value='') {
    $this->plugin_data[$key] = $value;
  }

  public function get($key) {
    return $this->plugin_data[$key];
  }
}
