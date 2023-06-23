<?php
session_start();

require_once "connect.php";
global $host, $db_user, $db_password, $db_name;

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
}
$id = $_SESSION['id'];

$result1 = $conn->query("DELETE FROM profile WHERE id_uzytkownicy = '$id';");
$result2 = $conn->query("SELECT login FROM uzytkownicy WHERE id='$id';");
$login = $result2->fetch_assoc()['login'];
$result3 = $conn->query("DELETE FROM ranking WHERE login = '$login';");

$quizzes = $conn->query("SELECT quiz_name FROM Quiz WHERE id_uzytkownika='$id';");
if ($quizzes->num_rows > 0) {
    while ($quiz = $quizzes->fetch_assoc()) {
        $name = $quiz['quiz_name'];
        $result = $conn->query("DROP TABLE `$name`;");
    }
}
$result4 = $conn->query("DELETE FROM Quiz WHERE id_uzytkownika = '$id';");
$result5 = $conn->query("DELETE FROM uzytkownicy WHERE id= '$id';");
$_SESSION = array();
session_destroy();

header("Location: index.php");
exit();
?>
