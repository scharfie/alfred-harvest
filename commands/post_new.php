<?php
  require('functions.php');

  $query = trim($argv[1]);
  echo start_timer($query);
?>
