<?php

  add_action('init', 'WPAuth0Sync_init_external');

  function WPAuth0Sync_init_external() {
    global $wp_rewrite;
    $plugin_url = plugins_url('sync.php', __FILE__);
    $plugin_url = substr($plugin_url, strlen(home_url()) + 1);
    $wp_rewrite->add_external_rule('sync.php$', $plugin_url);
  }

  add_action('init', 'WPAuth0Sync_init_internal');
  function WPAuth0Sync_init_internal() {
    add_rewrite_rule('sync.php$', 'index.php?WPAuth0Sync=sync', 'top');
  }

  add_filter('query_vars', 'WPAuth0Sync_query_vars');
  function WPAuth0Sync_query_vars($query_vars) {
    $query_vars[] = 'WPAuth0Sync';
    return $query_vars;
  }

  add_action('parse_request', 'WPAuth0Sync_parse_request');
  function WPAuth0Sync_parse_request(&$wp) {
    if (array_key_exists('WPAuth0Sync', $wp->query_vars)) {
      include 'auth0-to-wp-sync.php';
      exit();
    }
    return;
  }

?>
