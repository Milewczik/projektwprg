<?php
session_start();

require "connect.php";
global $host, $db_user, $db_password, $db_name;
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);

if ($polaczenie->connect_errno) {
    echo "Błąd połączenia z bazą danych: " . $polaczenie->connect_error;
    exit();
}
if (isset($_POST['quizdel'])) {
    $nazwa_quizu = $_POST['quizdel'];
    $id = $_SESSION['id'];
    $result = $polaczenie->query("SELECT * FROM Quiz WHERE quiz_name='$nazwa_quizu' AND id_uzytkownika='$id';");
    if ($result->num_rows > 0) {
        $deleteQuery = "DELETE FROM Quiz WHERE quiz_name='$nazwa_quizu' AND id_uzytkownika='$id';";
        $deleteQuery2 = "DROP TABLE $nazwa_quizu ;";
        $deleteResult = $polaczenie->query($deleteQuery);
        $deleteResult2 = $polaczenie->query($deleteQuery2);
        if (!$deleteResult) {
            echo "Błąd zapytania: " . $polaczenie->error;
            exit();
        }
    }
    $user = $_SESSION['user'];
    $username = $user["login"];
}
else{
    echo "Nie ma takiej bazy do usunięcia";
}
header("Location: profil.php?uzytkownik=$username");
exit();
?>