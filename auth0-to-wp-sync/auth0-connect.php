<?php

  function auth0_connect() {

    /**
     * Configurations
     */
    $wp_auth0_sync_options = get_option('wp_auth0_sync');

    $auth0_client = $wp_auth0_sync_options["wp_auth0_sync_client"];
    $auth0_token = $wp_auth0_sync_options["wp_auth0_sync_token"];

    /**
     * Create the timestamp meta
     */
    date_default_timezone_set('UTC');

    $timestamp_meta = 'wp_auth0_sync_timestamp';
    $today = date('Y-m-d\TH:i:s');

    $check_meta = get_user_meta(1, $timestamp_meta);

    if (empty($check_meta)) {
      add_user_meta(1, $timestamp_meta, $today);
      $timestamp_meta_value = "2000-01-01T00:00:00";
    } else {
      $timestamp_meta_value = get_user_meta(1, $timestamp_meta);
    }


    $url = "https://" . $auth0_client . ".auth0.com/api/v2/users?include_totals=true&q=" . urlencode("updated_at:[" . $timestamp_meta_value[0] . " TO *]") . "&search_engine=v2";

    $first_result = auth0_curl_get($url, $auth0_token);

    $statusCode = null;
    $output = array();
    $auth0_result = array();
    $first_result_total = 0;

    if (!empty(json_decode($first_result)->statusCode)) {

      $statusCode = json_decode($first_result);

    } else {

      $statusCode = 200;

      $first_result_total = json_decode($first_result)->total;

      if ($first_result_total > 0) {

        for ($i = 0; $i < ceil($first_result_total / 100); $i++) {
          $url = "https://" . $auth0_client . ".auth0.com/api/v2/users?per_page=100&page=" . $i . "&q=" . urlencode("updated_at:[" . $timestamp_meta_value[0] . " TO *]") . "&search_engine=v2";
          $result = auth0_curl_get($url, $auth0_token);
          for ($b = 0; $b < count(objectToArray(json_decode($result))); $b++) {
            array_push($auth0_result, objectToArray(json_decode($result))[$b]);
          }
        }

        update_user_meta(1, $timestamp_meta, $today);
        $timestamp_meta_value = get_user_meta(1, $timestamp_meta);

      } // if ($first_result_total > 0)

    }

    $message = array('status' => $statusCode, 'total' => $first_result_total);

    $output = array('data' => $auth0_result, 'output' => json_encode($message));

    return $output;

  } // function auth0_connect()

  $auth0_result = auth0_connect();

?>
