<?php
  date_default_timezone_set('UTC');

  $timestamp_meta = 'wp_auth0_sync_timestamp';
  $today = date('Y-m-d\TH:i:s');

  $check_meta = get_user_meta(1, $timestamp_meta);

  if (empty($check_meta)) {
    echo "<hr>SALVOU PRIMEIRA<hr>";
    add_user_meta(1, $timestamp_meta, $today);
    $timestamp_meta_value = "2000-01-01T00:00:00";
  } else {
    $timestamp_meta_value = get_user_meta(1, $timestamp_meta);
  }

  $authorization = "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJiMTRzU3NGdHBQa1JwV2NZcFd2ZTJTUjljbzVPNUt0aCIsInNjb3BlcyI6eyJ1c2VycyI6eyJhY3Rpb25zIjpbInJlYWQiLCJ1cGRhdGUiXX19LCJpYXQiOjE0NjkwNDM5OTgsImp0aSI6ImZiMTFhNjViN2UzNDViYjVmZmQzNmM5MTMxNGNkYjhhIn0.9mC700rjuQ30SQKvFSqCOCu9LGe4Tmo95OJ0FR1Xbic";
  $url = "https://agriness-test.auth0.com/api/v2/users?include_totals=true&q=" . urlencode("updated_at:[" . $timestamp_meta_value[0] . " TO *]") . "&search_engine=v2";

  $first_connection = curl_init($url);
  curl_setopt($first_connection, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
  curl_setopt($first_connection, CURLOPT_CUSTOMREQUEST, "GET");
  curl_setopt($first_connection, CURLOPT_RETURNTRANSFER, true);

  $first_result = curl_exec($first_connection);

  $auth0_result = array();
  $auth0_result_total = json_decode($first_result)->total;

  curl_close($first_connection);


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


  for ($i = 0; $i < ceil($auth0_result_total / 100); $i++) {

    $url = "https://agriness-test.auth0.com/api/v2/users?per_page=100&page=" . $i . "&q=" . urlencode("updated_at:[" . $timestamp_meta_value[0] . " TO *]") . "&search_engine=v2";

    $connection = curl_init($url);

    curl_setopt($connection, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($connection);

    echo $result;

    for ($b = 0; $b < count(objectToArray(json_decode($result))); $b++) {
      array_push($auth0_result, objectToArray(json_decode($result))[$b]);
    }

    curl_close($connection);
  }


  if ($auth0_result_total > 0) {
    update_user_meta(1, $timestamp_meta, $today);
    $timestamp_meta_value = get_user_meta(1, $timestamp_meta);
  }

?>
