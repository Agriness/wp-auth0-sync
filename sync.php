<!-- Validation #wtf -->
<?php if ($_GET['WPAuth0Sync'] !== 'sync') { return; } ?>

<?php

  include "connection-auth0.php";
  include "check-user.php";

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

  foreach (arrayToObject($auth0_result) as $user_key) {

    print_r(get_wp_user_object($user_key->email));
    echo check_user($user_key);

    foreach ($user_key as $key=>$meta) {
      if (!is_array($meta) and !is_object($meta)) {
        check_user_meta(get_wp_user_object($user_key->email)->ID, $key, $meta);
      } else {
        foreach ($meta as $key1=>$meta1) {
          check_user_meta(get_wp_user_object($user_key->email)->ID, $key1, $meta1);
        }
      }
    } // foreach ($user_key as $key=>$meta)

    $all_meta_for_user = array_map(function($a){ return $a[0]; }, get_user_meta(get_wp_user_object($user_key->email)->ID));

    foreach ($all_meta_for_user as $key=>$meta) {
      echo "<br>";
      echo $key . ": ";
      print_r($meta);
      echo "<br>";
    }

    echo "<br><hr>";

  } // foreach (json_decode($auth0_result) as $user_key)

?>

<script type="text/javascript">
  // jQuery(document).ready(function($) {
  //   var data = { action: 'ajax_wp_auth0_sync' };
  //   // jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function(response) {
  //   //   console.log( JSON.parse(response) );
  //   // });
  // });
</script>
