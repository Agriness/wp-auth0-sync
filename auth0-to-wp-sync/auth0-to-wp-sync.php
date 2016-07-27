<?php if ($_GET['WPAuth0Sync'] !== 'sync') { return; } ?>

<?php
  include_once "auth0-connect.php";

  $wp_auth0_sync_options = get_option('wp_auth0_sync');
  $wp_auth0_sync_json = $wp_auth0_sync_options["wp_auth0_sync_json"];

  foreach (arrayToObject($auth0_result['data']) as $user_key) {

    create_user_after_sync($user_key);

    foreach (json_decode($wp_auth0_sync_json) as $wp_key=>$auth0_key) {

      foreach ($user_key as $key=>$value) {

        if (!is_array($value) and !is_object($value)) {

          check_user_meta(get_wp_user_object($user_key->email)->ID, $key, $value);

        } else {

          foreach ($value as $inner_key=>$inner_value) {

            // Verifica se a chave que vem do auth0 existe no json
            if ($auth0_key === $inner_key) {
              check_user_meta(get_wp_user_object($user_key->email)->ID, $wp_key, $inner_value);
            } else {

              // Se a meta mapeada no json existir no wordpress ela deve ser removida
              if (get_user_meta(get_wp_user_object($user_key->email)->ID, $wp_key)) {
                delete_user_meta(get_wp_user_object($user_key->email)->ID, $wp_key);
              } // if (get_user_meta(get_wp_user_object($user_key->email)->ID, $wp_key))

            } // if ($auth0_key === $inner_key)

          } // foreach ($value as $inner_key=>$inner_value)

        } // else

      } // foreach ($user_key as $key=>$value)

    } // foreach (json_decode($wp_auth0_sync_json) as $wp_key=>$auth0_key)

    // $all_meta_for_user = array_map(function($a) { return $a[0]; }, get_user_meta(get_wp_user_object($user_key->email)->ID));
    //
    // foreach ($all_meta_for_user as $user_key=>$user_meta) {
    //   echo "<br>";
    //   echo $user_key . ": ";
    //   print_r($user_meta);
    //   echo "<br>";
    // }
    //
    // echo "<hr>";

  } // foreach (arrayToObject($auth0_result) as $user_key)

  echo objectToArray($auth0_result['output']);
?>
