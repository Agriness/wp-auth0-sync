<?php
/**
 * Plugin Name: WP Auth0 sync
 * Version: 0.0.1
 * Plugin URI:
 * Description:
 * Author: @cspilhere
 * Author URI:
 */


  include_once "_inc/helpers.php";

  include_once "admin/create-admin-interface.php";

  include_once "auth0-to-wp-sync/enable-external-link.php";

  include_once "wp-to-auth0-intercept/wp-to-auth0-intercept.php";


  add_action('wp_ajax_nopriv_ajax_wp_auth0_sync', 'ajax_wp_auth0_sync');
  add_action('wp_ajax_ajax_wp_auth0_sync', 'ajax_wp_auth0_sync');

  function ajax_wp_auth0_sync() {
    die();
  } // function ajax_wp_auth0_sync

?>
