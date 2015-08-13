<?php
$output = shell_exec('free -g');
echo "<pre>$output</pre>";
?>