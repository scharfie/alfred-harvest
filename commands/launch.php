<?php 
require('commands/auth.php');
$url = "https://$subdomain.harvestapp.com";

$xml = "<?xml version=\"1.0\"?>\n<items>\n";
$xml .= "<item uid=\"harvestaccount\" arg=\"$url\">\n";
$xml .= "<title>Launch Harvest account...</title>\n";
$xml .= "<subtitle>Visit $url</subtitle>\n";
$xml .= "<icon>icon.png</icon>\n";
$xml .= "</item>\n";
$xml .= "</items>";
echo $xml;
