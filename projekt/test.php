<?php
$filename = "wyniki/test.txt";
$liczba = 5;
$content = "3 / 5";
$file = fopen($filename, "w");
fwrite($file, $content);
fclose($file);