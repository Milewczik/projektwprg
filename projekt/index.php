<?php
session_start();

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;
$login = $_SESSION['login'];

if (!$isLoggedIn) {
    $showLimitedAccess = true;
} else {
    $user = $_SESSION['user'];
    $username = $user["login"];
    $showLimitedAccess = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quizzy</title>
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
<div class="container mx-auto px-4">

    <?php if ($showLimitedAccess): ?>
        <div class="text-center my-8" style="color: white; margin-top: 125px;">
            <form action="rejestracja.php" method="post">
                <button class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3" type="submit">
                    Zarejestruj
                </button>
            </form>
            <form action="logowanie.php" method="post">
                <button class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3" type="submit">
                    Zaloguj
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="text-center my-8" style="color: white; margin-top: 125px;">
            <form action="profil.php" method="get">
                <button class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3" type="submit" type="submit">
                    Twój profil
                </button>
            </form><br/>
            <a href="ranking.php" class="text-center text-3xl text-purple-700 py-4">Ranking</a><br/><br/>
            <a href="lista.php" class="text-center text-3xl text-purple-700 py-4">Lista Quizów</a><br/><br/>
            <a href="quiz_tworzenie.php" class="text-center text-3xl text-purple-700 py-4">Tworzenie quizu</a><br/><br/>
            <form action="wyloguj.php" method="post">
                <button class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3" type="submit">
                    Wyloguj
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>