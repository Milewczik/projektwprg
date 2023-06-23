<?php
session_start();

require "connect.php";
global $host, $db_user, $db_password, $db_name;
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);

if ($polaczenie->connect_errno) {
    echo "Błąd połączenia z bazą danych: " . $polaczenie->connect_error;
    exit();
}

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;
if ($isLoggedIn) {
    if (isset($_GET['uzytkownik'])) {
        $username = $_GET['uzytkownik'];
    } else {
        $user = $_SESSION['user'];
        $username = $user["login"];
        header("Location: profil.php?uzytkownik=$username");
        exit();
    }
}

if (isset($_GET['uzytkownik'])) {
    $username = $_GET['uzytkownik'];
} else {
    $user = $_SESSION['user'];
    $username = $user["login"];
}

$query = "SELECT p.imie, p.opis FROM profile p JOIN uzytkownicy u ON p.id_uzytkownicy = u.id WHERE u.login='$username';";

$result = $polaczenie->query($query);

if (!$result) {
    echo "Błąd zapytania: " . $polaczenie->error;
    exit();
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $imie = $row["imie"];
    $opis = $row["opis"];
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

<div class="mx-auto px-4">
    <?php if ($isLoggedIn && isset($_SESSION['user']) && $_SESSION['user']['login'] === $username): ?>
        <h1 class="text-4xl text-center my-8" style="color: white; margin-top: 20px;">Twój Profil</h1>
    <?php else: ?>
        <h1 class="text-4xl text-center my-8" style="color: white; margin-top: 20px;">Profil użytkownika <?php echo $username; ?></h1>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="flex justify-center text-white items-center">
            <div class="w-1/2 ml-8">
                <h2 class="text-2xl">Nazwa: <?php echo $imie; ?></h2>
                <p class="text-xl mt-4">Opis: <?php echo $opis; ?></p>
            </div>
            <?php if ($isLoggedIn && isset($_SESSION['user']) && $_SESSION['user']['login'] === $username): ?>
                <div class="flex justify-center mt-4">
                    <div class="ml-auto">
                        <a href="profil_edycja.php" class="text-center text-2xl text-purple-700 py-4">
                            Edytuj profil
                        </a>
                    </div>
                </div>
                <form action="profil_delete.php" method="post">
                    <button class="bg-gray-500 hover:bg-gray-700 text-2xl text-white py-1 px-2 rounded mr-2 mt-3" type="submit">Usuń konto
                    </button>
                </form>
                <form action="quiz_usuwanie.php" method="post">
                    <label class="text-white">Podaj nazwę quizu do usunięcia</label>
                    <input type="text" name="quizdel" class="text-black">
                    <button class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3" type="submit">Usuń quiz</button>
                </form>
            <?php endif; ?>
        </div>

        <?php
        $id = $_SESSION['id'];
        $query = "SELECT * FROM Quiz WHERE id_uzytkownika='$id'";
        $quizzes = $polaczenie->query($query);

        if (!$quizzes) {
            echo "Błąd zapytania: " . $polaczenie->error;
            exit();
        }

        if ($quizzes->num_rows > 0) {
            echo '<table class="mt-4 mx-auto text-white">';
            echo '<tr>';
            echo '<th class="px-4 py-2">Nazwa quizu</th>';
            echo '<th class="px-4 py-2">Liczba pytań</th>';
            echo '<th class="px-4 py-2">Data utworzenia</th>';
            echo '<th class="px-4 py-2">Liczba wykonań</th>';
            echo '</tr>';

            while ($quiz = $quizzes->fetch_assoc()) {
                echo '<tr>';
                echo '<td class="border px-4 py-2 text-purple-700 hover:underline "><a href="quiz.php?nazwa=' . $quiz['quiz_name'] . '">' . $quiz['quiz_name'] . '</a></td>';
                echo '<td class="border px-4 py-2">' . $quiz['question_count'] . '</td>';
                echo '<td class="border px-4 py-2">' . $quiz['creation_date'] . '</td>';
                echo '<td class="border px-4 py-2">' . $quiz['execution_count'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>

    <?php else: ?>
        <h1 class="text-6xl text-center my-8" style="color: white; margin-top: 125px;">Nie znaleziono użytkownika!</h1>
    <?php endif; ?>

</div>
</body>
</html>
