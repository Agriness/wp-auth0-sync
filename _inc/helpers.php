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
    if (email_exists($user_object->email)) {
      // User already exists
    } else {
      if (!username_exists($user_object->nickname)) {
        $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
        wp_create_user($user_object->nickname, $random_password, $user_object->email);
        // New user was created
      }
    }
    return true;
  }


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
