<?php
$cpu = shell_exec("cat /proc/stat | grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage "%"}'")
echo "<pre>$cpu</pre>";
?>