<?php
require('auth.php');

function decimal_to_hours($decimal) {
  $parts = explode('.', $decimal . '');
  $parts[] = 0;

  $decimal = substr($parts[1] . '00', 0, 2);

  $minutes = sprintf("%02d", $decimal / 100 * 60);
  $hours   = $parts[0];

  return "$hours:$minutes"; # ($decimal)";
}

function debug($message) {
  $formatted_message = sprintf("[%s] %s\n\n", date(DATE_ATOM), $message);
  $fp = fopen(__DIR__ . "/debug.log", "w+");
  if ($fp) {
    fwrite($fp, $formatted_message);
    fclose($fp);
  }
}

function start_timer($query) {
  require('auth.php');
  $url = "https://$subdomain.harvestapp.com/daily/add";

  // get id's from $query:
  $strings = explode( "|", $query);
  $project_id = $strings[0];
  $task_id = $strings[1];
  $project = str_replace("_", " ", $strings[2]);

  $default_tz = "UTC";
  $tz_file = readlink("/etc/localtime");
  $pos = strpos($tz_file, "zoneinfo");
  if ($pos) {
    $tz = substr($tz_file, $pos + strlen("zoneinfo/"));
  } else {
    $tz = $default_tz;
  }
  date_default_timezone_set( $tz );

  $date = date("D, d M Y");

  // set xml_data to post to Harvest API:
  $xml_data = "<request> <notes></notes> <hours> </hours> <project_id>$project_id</project_id> <task_id>$task_id</task_id> <spent_at>$date</spent_at> </request>";

  $headers = array (
    "Content-type: application/xml",
    "Accept: application/xml",
    "Authorization: Basic " . base64_encode($credentials)
  );

  debug($xml_data);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_TIMEOUT, 60);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
  curl_exec($ch); debug(json_encode(curl_getinfo($ch)));
  curl_close($ch);

  $query = "Started —" . " " . $project;
  return $query;
}

?>
