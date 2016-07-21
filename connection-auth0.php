<?php

  $authorization = "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJoc1I1aFp2Z0xybU14ekt0UWV0RncxcGNDMEZtbGFIVyIsInNjb3BlcyI6eyJ1c2VycyI6eyJhY3Rpb25zIjpbInJlYWQiXX19LCJpYXQiOjE0NjkwMjEzMzYsImp0aSI6ImFjNzlmNDk0NjMzZWEzNWNmMTAzNDQxN2U2ZjZjMGI1In0.vDTt7d1VH4VgtvG0k8epUj9LoTDdw03ei1LDeC2VPpk";

  $url = "https://agriness.auth0.com/api/v2/users?include_totals=true";

  $first_connection = curl_init($url);
  curl_setopt($first_connection, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
  curl_setopt($first_connection, CURLOPT_CUSTOMREQUEST, "GET");
  curl_setopt($first_connection, CURLOPT_RETURNTRANSFER, true);

  $first_result = curl_exec($first_connection);

  $auth0_result = array();
  $auth0_result_total = json_decode($first_result)->total;

  echo $auth0_result_total;

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

    $url = "https://agriness.auth0.com/api/v2/users?per_page=10&page=" . $i;

    $connection = curl_init($url);

    curl_setopt($connection, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($connection);

    for ($b = 0; $b < count(objectToArray(json_decode($result))); $b++) {
      array_push($auth0_result, objectToArray(json_decode($result))[$b]);
    }

    curl_close($connection);
  }

?>
