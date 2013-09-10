<?php

  $query = trim($argv[1]);

  if ( substr_count( $query, '→' ) == 0 ):

    $data_raw = file_get_contents('projects.txt');
    $data = json_decode($data_raw, true);

    $xml = "<?xml version=\"1.0\"?>\n<items>\n";

    foreach ($data["projects"] as $project){
      $name    = htmlspecialchars($project["name"]);
      $client  = htmlspecialchars($project["client"]);

      if ( !$query ) {
        $xml .= "<item valid=\"no\" autocomplete=\" $name → \">\n";
        $xml .= "<title>$name, $client</title>\n";
        $xml .= "<subtitle>View available tasks...</subtitle>\n";
        $xml .= "<icon>add.png</icon>\n";
        $xml .= "</item>\n";
      } elseif ( stripos($name . $client, $query) !== false ) {
        $xml .= "<item valid=\"no\" autocomplete=\" $name → \">\n";
        $xml .= "<title>$name, $client</title>\n";
        $xml .= "<subtitle>View available tasks...</subtitle>\n";
        $xml .= "<icon>add.png</icon>\n";
        $xml .= "</item>\n";
      }
    }

    $xml .= "</items>";
    echo $xml;

  elseif ( substr_count( $query, '→' ) == 1 ):

    $project_name = trim( $query, " → " );
    $data_raw = file_get_contents('projects.txt');
    $data = json_decode($data_raw, true);

    foreach ( $data["projects"] as $project ){

      if ( $project["name"] == $project_name ) {
        $project_tasks = $project["tasks"];
        $project_name = $project["name"];
        $project_id = $project["id"];
        $project_name_encoded = str_replace(" ", "_", htmlspecialchars($project_name));
      }
    }

    $xml = "<?xml version=\"1.0\"?>\n<items>\n";

    foreach ($project_tasks as $task){
      $task_name = htmlspecialchars($task["name"]);
      $task_id = $task["id"];

      $xml .= "<item arg=\"$project_id|$task_id|$project_name_encoded\">\n";
      $xml .= "<title>$task_name</title>\n";
      $xml .= "<subtitle>Start this task</subtitle>\n";
      $xml .= "<icon>go.png</icon>\n";
      $xml .= "</item>\n";
    }

    $xml .= "</items>";
    echo $xml;

  endif;

?>