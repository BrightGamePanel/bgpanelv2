<?php
$output = shell_exec('free -m');
echo "<pre>$output</pre>";
?>