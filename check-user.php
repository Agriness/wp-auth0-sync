<?php

  function check_user($user_object) {
    if (email_exists($user_object->email)) {
      echo "<hr>Já existe";
    } else {
      $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
      wp_create_user($user_object->nickname, $random_password, $user_object->email);
      echo "<hr>Criado novo usuário";
    }
    return true;
  }

?>
