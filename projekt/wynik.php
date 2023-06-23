<?php
session_start();

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: logowanie.php");
    exit();
}

if (!isset($_GET['nazwa'])) {
    header("Location: lista.php");
    exit();
}

$nazwa = $_GET['nazwa'];

require_once "connect.php";
global $host, $db_user, $db_password, $db_name;

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
}

$sql = "SELECT * FROM `$nazwa`";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $pytania = $result->fetch_all(MYSQLI_ASSOC);
} else {
    header("Location: lista.php");
    exit();
}

$conn->close();

$poprawneOdpowiedzi = 0;

foreach ($pytania as $pytanie) {
    $idPytania = $pytanie['id'];

    if (isset($_POST['odpowiedz'][$idPytania])) {
        $odpowiedzUzytkownika = $_POST['odpowiedz'][$idPytania];
        $odpowiedzPoprawna = $pytanie['correct_answer'];

        if ($odpowiedzUzytkownika === $odpowiedzPoprawna) {
            $poprawneOdpowiedzi++;
        }
    }
}

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
}

$login = $_SESSION["user"]["username"];

$punkty = $poprawneOdpowiedzi * 100;

$sql = "UPDATE `ranking` SET `punkty` = `punkty` + $punkty WHERE `login` = '$login'";
$conn->query($sql);

$sql = "UPDATE `Quiz` SET `execution_count` = `execution_count` + 1 WHERE `quiz_name` = '$nazwa'";
$conn->query($sql);

$conn->close();

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
<main class="container mx-auto mt-8 text-white">
    <h1 class="text-3xl text-center mb-4">Wynik Quizu</h1>
    <div class="max-w-2xl mx-auto">
        <p class="mb-4 text-xl text-center">Twój wynik: <?php echo $poprawneOdpowiedzi; ?>/<?php echo count($pytania); ?></p>

        <h2 class="text-xl mb-2">Poprawne odpowiedzi:</h2>
        <ul>
            <?php foreach ($pytania as $pytanie): ?>
                <li><?php echo $pytanie['question']; ?> - <?php echo $pytanie['correct_answer']; ?></li>
            <?php endforeach; ?>
        </ul>

        <h2 class="text-xl mt-4 mb-2">Twoje odpowiedzi:</h2>
        <ul>
            <?php foreach ($pytania as $pytanie): ?>
                <?php
                $idPytania = $pytanie['id'];

                if (isset($_POST['odpowiedz'][$idPytania])) {
                    $odpowiedzUzytkownika = $_POST['odpowiedz'][$idPytania];
                    $odpowiedzPoprawna = $pytanie['correct_answer'];
                }
                ?>
                <li>
                    <?php echo $pytanie['question']; ?> - <?php echo $odpowiedzUzytkownika; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <br/>
        <?php
        $filename = "odpowiedzi/$login-$nazwa.txt";
        $liczba = count($pytania);
        $content = " Twoje odpowiedzi: $poprawneOdpowiedzi / Poprawne odpowiedzi: $liczba ";
        $file = fopen($filename, "w");
        if ($file) {
            if (fwrite($file, $content) !== false) {
                echo "Plik zapisany pomyślnie.";
            } else {
                echo "Wystąpił błąd podczas zapisywania pliku.";
            }
            fclose($file);
        } else {
            echo "Nie można otworzyć pliku do zapisu.";
        }
        ?>
        <br/><br/>

        <a href="lista.php" class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3">
            Wróć do listy quizów</a>
    </div>
</main>

</body>
</html>
