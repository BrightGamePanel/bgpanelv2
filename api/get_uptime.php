<?php
exec("uptime", $system); // get the uptime stats 
$string = $system[0]; // this might not be necessary 
$uptime = explode(" ", $string); // break up the stats into an array 

$up_days = $uptime[4]; // grab the days from the array 

$hours = explode(":", $uptime[7]); // split up the hour:min in the stats 

$up_hours = $hours[0]; // grab the hours 
$mins = $hours[1]; // get the mins 
$up_mins = str_replace(",", "", $mins); // strip the comma from the mins 

echo "The server has been up for " . $up_days . " days, " . $up_hours . " hours, and " . $up_mins . " minutes."; 
// echo the results  	
?>
