<?php
  require('functions.php');
  require('auth.php');

  $query = trim($argv[1]);
  
  if ( substr_count( $query, '→' ) == 1 ) {
    require('new.php');
    return;
  }

  $xml = '<?xml version="1.0"?><items>';

  if ( !$query ) {
    require('get_daily.php');

    $data = json_decode($response, true);

    $total_hours = 0;

    if ($data["day_entries"]) {
      foreach ($data["day_entries"] as $day_entry) {
        $project = htmlspecialchars(fetch_key($day_entry, "project"));
        $task    = htmlspecialchars(fetch_key($day_entry, "task"));
        $client  = htmlspecialchars(fetch_key($day_entry, "client"));
        $notes   = htmlspecialchars(fetch_key($day_entry, "notes"));
        $hours   = fetch_key($day_entry, "hours");
        $active  = fetch_key($day_entry, 'timer_started_at', false);
        $id      = fetch_key($day_entry, "id");

        $total_hours += (float)$hours;

        $hours   = decimal_to_hours($hours);

        if ( $active ) {
          $xml .= "<item uid=\"harvesttoggle-current\" arg=\"$id\">\n";
        } else {
          $xml .= "<item arg=\"$id\" uid=\"harvesttoggle-$id\">\n";
        }

        $xml .= "<title>$hours hours – $project</title>\n";

        if ( $notes ) {
          $xml .= "<subtitle>$client, $task – \"$notes\"</subtitle>\n";
        } else {
          $xml .= "<subtitle>$client, $task</subtitle>\n";
        }
        if ( $active ) {
          $xml .= "<icon>icons/stop.png</icon>\n";
        } else {
          $xml .= "<icon>icons/go.png</icon>\n";
        }

        $xml .= "</item>\n";
      }

      /* $xml .= "<item>\n"; */
      /* $xml .= "<title>Add new timer</title>\n"; */
      /* $xml .= "<subtitle>Press 'Enter' to select a new timer...</subtitle>\n"; */
      /* $xml .= "<icon>icons/add.png</icon>\n"; */
      /* $xml .= "</item>\n"; */

      $d = $total_hours;
      $total_hours = decimal_to_hours($total_hours);
      $xml .= "<item><title>$total_hours ($d) hours today</title></item>";
      
    /* } else { */
    /*   $xml .= "<item>\n"; */
    /*   $xml .= "<title>No timers yet today. Start one?</title>\n"; */
    /*   $xml .= "<subtitle>Press 'Enter' to select a new timer...</subtitle>\n"; */
    /*   $xml .= "<icon>icons/add.png</icon>\n"; */
    /*   $xml .= "</item>\n"; */
    }

  } else {

    $data_raw = file_get_contents($dir . 'projects.json');
    $data = json_decode($data_raw, true);

    $xml = "<?xml version=\"1.0\"?>\n<items>\n";

    if ($data["day_entries"]) {
      foreach ($data["day_entries"] as $day_entry){
        $project = htmlspecialchars($day_entry["project"]);
        $task    = htmlspecialchars($day_entry["task"]);
        $client  = htmlspecialchars($day_entry["client"]);
        $notes   = htmlspecialchars($day_entry["notes"]);
        $hours   = $day_entry["hours"];
        $active  = $day_entry["timer_started_at"];
        $id      = $day_entry["id"];

        if ( stripos($project . $task . $client . $notes, $query) !== false ) {
      
          if ( $active ) {
            $xml .= "<item uid=\"harvesttoggle-current\" arg=\"$id\">\n";
          } else {
            $xml .= "<item arg=\"$id\" uid=\"harvesttoggle-$id\">\n";
          }

          $xml .= "<title>$hours hours – $project</title>\n";

          if ( $notes ) {
            $xml .= "<subtitle>$client, $task – \"$notes\"</subtitle>\n";
          } else {
            $xml .= "<subtitle>$client, $task</subtitle>\n";
          }

          if ( $active ) {
            $xml .= "<icon>icons/stop.png</icon>\n";
          } else {
            $xml .= "<icon>icons/go.png</icon>\n";
          }

          $xml .= "</item>\n";
        }
      }

      /* $xml .= "<item arg=\"new:$query\">\n"; */
      /* $xml .= "<title>Add new timer</title>\n"; */
      /* $xml .= "<subtitle>Press 'Enter' to select a new timer...</subtitle>\n"; */
      /* $xml .= "<icon>icons/add.png</icon>\n"; */
      /* $xml .= "</item>\n"; */

    /* } else { */
    /*   $xml .= "<item arg=\"new:$query\">\n"; */
    /*   $xml .= "<title>No timers yet today. Start one?</title>\n"; */
    /*   $xml .= "<subtitle>Press 'Enter' to select a new timer...</subtitle>\n"; */
    /*   $xml .= "<icon>icons/add.png</icon>\n"; */
    /*   $xml .= "</item>\n"; */
    }
  }

  // add in 'new items' that match query

  foreach ($data["projects"] as $project){
    $name    = htmlspecialchars($project["name"]);
    $client  = htmlspecialchars($project["client"]);
    $id      = $project["id"];

    if ( !$query ) {
      $xml .= "<item valid=\"no\" uid=\"harvestnew-$id\" autocomplete=\"$name → \">\n";
      $xml .= "<title>$name, $client</title>\n";
      $xml .= "<subtitle>View available tasks...</subtitle>\n";
      $xml .= "<icon>icons/add.png</icon>\n";
      $xml .= "</item>\n";
    } elseif ( stripos($name . $client, $query) !== false ) {
      $xml .= "<item valid=\"no\" uid=\"harvestnew-$id\" autocomplete=\"$name → \">\n";
      $xml .= "<title>$name, $client</title>\n";
      $xml .= "<subtitle>View available tasks...</subtitle>\n";
      $xml .= "<icon>icons/add.png</icon>\n";
      $xml .= "</item>\n";
    }
  }

  // /end
  $xml .= "</items>";
  echo $xml;
?>
