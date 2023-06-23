<?php

	$host = "szuflandia.pjwstk.edu.pl";
	$db_user = "s27271";
	$db_password = "Paw.Mile";
	$db_name = "s27271";
    $conn = mysqli_connect($host, $db_user, $db_password, $db_name);
    if (!$conn) {
        die("Something went wrong;");
    }
global $host, $db_user, $db_password, $db_name, $conn;
?>