<?php
session_start();
$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: zaloguj.php");
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

$sql = "SELECT quiz_name, question_count FROM Quiz WHERE quiz_name = '$nazwa' LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $quizName = $row['quiz_name'];
    $questionCount = $row['question_count'];
} else {
    header("Location: lista.php");
    exit();
}

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
<main class="container mx-auto px-4 mt-8">
    <div class="text-center mb-4">
        <h1 class="text-4xl text-white mb-4"><?php echo $quizName; ?></h1>
        <p class="mb-4 text-white">Ilość pytań: <?php echo $questionCount; ?></p>
        <a href="quiz_start.php?nazwa=<?php echo $nazwa; ?>&test" class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3">
            Start
        </a>
    </div>
</main>

</body>
</html>
