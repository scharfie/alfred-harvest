<?php
  require('functions.php');

  $query = trim($argv[1]);

  if ( substr_count( $query, '|' ) == 2 ) {
    echo start_timer($query);
    return;
  }

  if ($query && stripos($query, 'new:') === false) {
    require('auth.php');
    $url = "https://$subdomain.harvestapp.com/daily/timer/$query";

    $headers = array (
      "Content-type: application/json",
      "Accept: application/json",
      "Authorization: Basic " . base64_encode($credentials)
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data_raw = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($data_raw, true);

    $was_started = fetch_key($data, "timer_started_at", false);
    $project = fetch_key($data, "project");
    $client = fetch_key($data, "client");

    if ( $was_started ) {
      $query = "Started —" . " " . $client . "  " . $project;
    } else {
      $query = "Stopped —" . " " . $client . "  " . $project;
    }

    echo $query;

  } else {
    $id = null;
    $name = null;
    $xml = "<?xml version=\"1.0\"?>\n<items>\n";
    $xml .= "<item valid=\"no\" uid=\"harvestnew-$id\" autocomplete=\"$name → \">\n";
    $xml .= "<title>HELLO</title>\n";
    $xml .= "<subtitle>View available tasks...</subtitle>\n";
    $xml .= "<icon>icons/add.png</icon>\n";
    $xml .= "</item>\n";

    $xml .= "</items>";
    echo $xml;
  }

?>
