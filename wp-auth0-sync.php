<?php
/**
 * Plugin Name: WP Auth0 sync
 * Version: 0.0.1
 * Plugin URI:
 * Description:
 * Author: @cspilhere
 * Author URI:
 */

  include "enable-external-link.php";
  include "auth0Login.php";

  include "user-update-action.php";

//

  add_action('wp_ajax_nopriv_ajax_wp_auth0_sync', 'ajax_wp_auth0_sync');
  add_action('wp_ajax_ajax_wp_auth0_sync', 'ajax_wp_auth0_sync');

  function ajax_wp_auth0_sync() {
    die();
  } // function ajax_wp_auth0_sync

?>
