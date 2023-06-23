<?php
session_start();

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    exit();
}
$user = $_SESSION['user'];
$username = $user["login"];
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
<div class="container mx-auto px-4">
    <h1 class="text-6xl text-center my-8" style="color: white; margin-top: 125px;">Dodano quiz</h1>
    <div class="text-center">
        <form action="index.php" method="post">
            <button class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3" type="submit">
                Kliknij mnie :)
            </button>
        </form>
    </div>
</body>
</html>
