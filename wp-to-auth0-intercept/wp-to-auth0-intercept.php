<?php

  function check_auth0_user_meta($ID, $key, $value) {
    if (get_user_meta($ID, $key)) {
      if (get_user_meta($ID, $key) === $value) {
        return false;
      } else {
        return true;
      }
    } else {
      return true;
    }
  }

  function auth0_curl_patch($url, $post, $token) {
    $authorization = "Authorization: Bearer " . $token;
    $connection = curl_init($url);
    curl_setopt($connection, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
    curl_setopt($connection, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($connection);
    curl_close($connection);
    return $result;
  }

  add_action('user_register', 'update_meta_on_auth0');
  add_action('edit_user_profile_update', 'update_meta_on_auth0');
  add_action('personal_options_update', 'update_meta_on_auth0');
  add_action('profile_update', 'update_meta_on_auth0');

  function update_meta_on_auth0($user_id) {

    $auth0_user_id = get_user_meta($user_id, 'user_id');

    $wp_auth0_sync_options = get_option('wp_auth0_sync');
    $auth0_client = $wp_auth0_sync_options["wp_auth0_sync_client"];
    $auth0_token = $wp_auth0_sync_options["wp_auth0_sync_token"];
    $wp_auth0_sync_json = $wp_auth0_sync_options["wp_auth0_sync_json"];

    $url_to_get_user = "https://" . $auth0_client . ".auth0.com/api/v2/users/" . $auth0_user_id[0];


    $auth0_user_metadata = array();

    foreach (json_decode($wp_auth0_sync_json) as $wp_key=>$auth0_key) {
      if (!empty(get_user_meta($user_id, $wp_key))) {
        $auth0_user_metadata[$auth0_key] = get_user_meta($user_id, $wp_key)[0];
      }
    }

    $auth0_user_json["user_metadata"] = $auth0_user_metadata;

    $url_send = $url_to_get_user;
    $str_data = json_encode($auth0_user_json);

    update_user_meta($user_id, "wp_auth0_sync_debug_last_patch", auth0_curl_patch($url_send, $str_data, $auth0_token));

  } // function update_meta_on_auth0($user_id)

?>
