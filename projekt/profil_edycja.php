<?php
session_start();

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: logowanie.php");
    exit();
}

require_once "connect.php";
global $host, $db_user, $db_password, $db_name;
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);

if ($polaczenie->connect_errno) {
    echo "Błąd połączenia z bazą danych: " . $polaczenie->connect_error;
    exit();
}
$user = $_SESSION['user'];
$username = $user["login"];
$username = $_GET['uzytkownik'];

$id = $user["id"];

$result = $polaczenie->query("SELECT imie, opis FROM profile WHERE id_uzytkownicy='$id'");

if (!$result) {
    echo "Błąd zapytania: " . $polaczenie->error;
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $imie = $_POST["imie"];
    $opis = $_POST["opis"];
    $updateQuery = "UPDATE profile SET imie='$imie', opis='$opis' WHERE id_uzytkownicy='$id'";

    if ($polaczenie->query($updateQuery) === TRUE) {
        header("Location: profil.php");
    } else {
        echo "Błąd aktualizacji danych użytkownika: " . $polaczenie->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quizzy!</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css">
    <style>
        html {
            height: 100%;
            margin: 0;
            min-height: 100vh;
        }

        body {
            background-color: gray;
            background-size: cover;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }
    </style>
</head>
<body>
<a href="index.php" class="text-center text-3xl text-white py-4">Quizzy!</a>
<main>
    <div class="container mx-auto px-4">
        <h1 class="text-2xl text-white mt-8">Edytuj swój profil</h1>
        <form class="mx-auto mt-8" method="post" enctype="multipart/form-data">
            <div class="mt-4">
                <label class="block text-white mb-2" for="imie">Imię</label>
                <input class="w-full py-2 px-3" id="imie" type="text" name="imie" value="<?php echo isset($imie) ? $imie : ''; ?>" required>
            </div>
            <div class="mt-4">
                <label class="block text-white mb-2" for="opis">Opis</label>
                <textarea class="w-full py-2 px-3" id="opis" name="opis"><?php echo isset($opis) ? $opis : ''; ?></textarea>
            </div>
            <div class="mt-8">
                <button class="bg-gray-500 hover:bg-gray-700 text-2xl text-white py-1 px-2 rounded mr-2 mt-3" type="submit">Zapisz
                </button>
            </div>
        </form>
    </div>
</main>
</body>
</html>
