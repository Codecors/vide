<?php
//Get the file
$content = file_get_contents("https://p-def1.pcloud.com/cfZAEHSMkZTdeXMkZTvUxZZeAYf67ZQ5ZZC5FZZQ4mROZ47ZbVZo7ZxVZFZnXZ2VZkXZXkZDXZdZ3ZD7Z3kZzucjSqm5YY8Gk7hkqXmmPBslCA37/Ae%20Dil%20Hai%20Mushkil%20%282016%29%20DVDRip%20720p%20Hindi%20Movie%20-%201.1%20GB%20-%20Drkoul-%20%28PerfectHDmovies.pw%29.mp4");
//Store in the filesystem.
$fp = fopen("image.mp4", "w");
fwrite($fp, $content);
fclose($fp);
echo 'hope it\'s done';
?>
