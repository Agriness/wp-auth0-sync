<?php

  function get_wp_user_object($email) {
    return get_user_by('email', $email);
  }

  function check_user_meta($ID, $key, $value) {
    if (get_user_meta($ID, $key)) {
      update_user_meta($ID, $key, $value);
      return get_user_meta($ID, $key);
    } else {
      add_user_meta($ID, $key, $value);
      return get_user_meta($ID, $key);
    }
  }

  function create_user_after_sync($user_object) {

    if (!email_exists($user_object->email)) {
      $email = explode("@", $user_object->email);
      $nickname = $email[0].'_'.$email[1];

      if (!username_exists($nickname)) {
        $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
        $user_id = wp_create_user($nickname, $random_password, $user_object->email);
        $display_name = $user_object->user_metadata->nome ?: $user_object->name;
        
        wp_update_user(array( 'ID' => $user_id, 'display_name' => $display_name ) );
      }
    }
    return true;
  }

  function auth0_curl_get($url, $token) {
    $authorization = "Authorization: Bearer " . $token;
    $connection = curl_init($url);
    curl_setopt($connection, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($connection);
    curl_close($connection);
    return $result;
  } // function auth0_curl_get($url)


  // http://www.if-not-true-then-false.com/2009/php-tip-convert-stdclass-object-to-multidimensional-array-and-convert-multidimensional-array-to-stdclass-object/
  function objectToArray($d) {
    if (is_object($d)) {
      // Gets the properties of the given object
      // with get_object_vars function
      $d = get_object_vars($d);
    }

    if (is_array($d)) {
      /*
      * Return array converted to object
      * Using __FUNCTION__ (Magic constant)
      * for recursive call
      */
      return array_map(__FUNCTION__, $d);
    } else {
      // Return array
      return $d;
    }
  }

  function arrayToObject($d) {
    if (is_array($d)) {
      /*
      * Return array converted to object
      * Using __FUNCTION__ (Magic constant)
      * for recursive call
      */
      return (object) array_map(__FUNCTION__, $d);
    } else {
      // Return object
      return $d;
    }
  }

?>
