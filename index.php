<?php
//Get the file
$content = file_get_contents("http://www.google.co.in/intl/en_com/images/srpr/logo1w.png");
//Store in the filesystem.
$fp = fopen("/location/to/save/image.jpg", "w");
fwrite($fp, $content);
fclose($fp);
?>
