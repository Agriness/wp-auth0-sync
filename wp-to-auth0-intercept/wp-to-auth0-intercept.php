<?php

  $authorization = "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJiMTRzU3NGdHBQa1JwV2NZcFd2ZTJTUjljbzVPNUt0aCIsInNjb3BlcyI6eyJ1c2VycyI6eyJhY3Rpb25zIjpbInJlYWQiLCJ1cGRhdGUiXX19LCJpYXQiOjE0NjkwNDM5OTgsImp0aSI6ImZiMTFhNjViN2UzNDViYjVmZmQzNmM5MTMxNGNkYjhhIn0.9mC700rjuQ30SQKvFSqCOCu9LGe4Tmo95OJ0FR1Xbic";
  $url = "https://agriness-test.auth0.com/api/v2/users/";

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


  add_action('user_register', 'update_meta_on_auth0');
  add_action('edit_user_profile_update', 'update_meta_on_auth0');

  function update_meta_on_auth0($user_id) {

    $auth0_user_id = get_user_meta($user_id, 'user_id');

    $authorization = "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJiMTRzU3NGdHBQa1JwV2NZcFd2ZTJTUjljbzVPNUt0aCIsInNjb3BlcyI6eyJ1c2VycyI6eyJhY3Rpb25zIjpbInJlYWQiLCJ1cGRhdGUiXX19LCJpYXQiOjE0NjkwNDM5OTgsImp0aSI6ImZiMTFhNjViN2UzNDViYjVmZmQzNmM5MTMxNGNkYjhhIn0.9mC700rjuQ30SQKvFSqCOCu9LGe4Tmo95OJ0FR1Xbic";
    $url = "https://agriness-test.auth0.com/api/v2/users/" . $auth0_user_id[0];

    $user_auth0_connection = curl_init($url);
    curl_setopt($user_auth0_connection, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
    curl_setopt($user_auth0_connection, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($user_auth0_connection, CURLOPT_RETURNTRANSFER, true);
    $user_auth0 = curl_exec($user_auth0_connection);
    curl_close($user_auth0_connection);

    $auth0_user_data = "";
    $auth0_user_metadata = "";

    foreach ($user_auth0 as $meta_key=>$user_meta) {
      if (!is_array($user_meta) and !is_object($user_meta)) {
        // if (check_auth0_user_meta($user_id, $meta_key, $user_meta)) {
          $auth0_user_data = '"' . $meta_key . '": "' . $user_meta . '",';
        // }
      } else {
        foreach ($user_meta as $inner_meta_key=>$user_inner_meta) {
          // if (check_auth0_user_meta($user_id, $inner_meta_key, $user_inner_meta)) {
            $auth0_user_metadata = '"' . $inner_meta_key . '": "' . $user_inner_meta . '",';
          // }
        }
      }
    } // foreach ($user_auth0 as $meta_key=>$user_meta)

    $auth0_user_json = '{' . $auth0_user_data . '"user_metadata": {' . $auth0_user_metadata . '}}';

    update_user_meta($user_id, "meta_teste", $user_auth0);

    // -X PATCH  -H "Content-Type: application/json" -d
    // '{"user_metadata":{"profileCode":1479,"addresses":{"work_address":"100 Industrial Way","home_address":"742 Evergreen Terrace"}}}'
    //
    // $url_send ="http://api.payquicker.com/api/SendInvitation?authorizedKey=xxxxx";
    // $str_data = json_encode($data);
    //
    // function sendPostData($url, $post){
    //   $ch = curl_init($url);
    //   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //   curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
    //   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    //   $result = curl_exec($ch);
    //   curl_close($ch);  // Seems like good practice
    //   return $result;
    // }
    //
    // sendPostData($url_send, $str_data);

  } // function update_meta_on_auth0($user_id)

?>
