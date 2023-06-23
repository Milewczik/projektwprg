<?php

session_start();

if ((isset($_SESSION['zarejestrowany'])) && ($_SESSION['zalogowany'] == true)) {
    header('Location: zarejestrowano.php');
    exit();
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
    <h2 class="text-6xl text-center my-8" style="color: white; margin-top: 125px;">Pomyślnie zarejestrowano!</h2><br>
    <div class="text-center">
        <form action="logowanie.php" method="post">
            <button class="bg-gray-500 hover:bg-gray-700 text-2xl text-white py-1 px-2 rounded mr-2 mt-3" type="submit">
                Zaloguj
            </button>
        </form>
    </div>
</body>
</html>
