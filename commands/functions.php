<?php

function decimal_to_hours($decimal) {
  $parts = explode('.', $decimal . '');
  $parts[] = 0;

  $decimal = substr($parts[1] . '00', 0, 2);

  $minutes = sprintf("%02d", $decimal / 100 * 60);
  $hours   = $parts[0];

  return "$hours:$minutes"; # ($decimal)";
}

?>
