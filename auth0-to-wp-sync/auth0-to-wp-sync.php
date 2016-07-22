<!-- Validation #wtf -->
<?php if ($_GET['WPAuth0Sync'] !== 'sync') { return; } ?>

<?php

  include_once "auth0-connect.php";

  foreach (arrayToObject($auth0_result) as $user_key) {

    create_user_after_sync($user_key);

    foreach ($user_key as $key=>$meta) {
      if (!is_array($meta) and !is_object($meta)) {
        check_user_meta(get_wp_user_object($user_key->email)->ID, $key, $meta);
      } else {
        foreach ($meta as $inner_key=>$inner_meta) {
          check_user_meta(get_wp_user_object($user_key->email)->ID, $inner_key, $inner_meta);
        }
      }
    } // foreach ($user_key as $key=>$meta)

    $all_meta_for_user = array_map(function($a) { return $a[0]; }, get_user_meta(get_wp_user_object($user_key->email)->ID));

    foreach ($all_meta_for_user as $user_key=>$user_meta) {
      echo "<br>";
      echo $user_key . ": ";
      print_r($user_meta);
      echo "<br>";
    }

    echo "<hr>";

  } // foreach (arrayToObject($auth0_result) as $user_key)

?>
