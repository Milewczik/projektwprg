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

$sql = "SELECT * FROM `$nazwa`";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $pytania = $result->fetch_all(MYSQLI_ASSOC);
    shuffle($pytania);
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
<main class="container mx-auto mt-8">
    <div class="max-w-2xl mx-auto p-8 text-white">
        <form action="wynik.php?nazwa=<?php echo $nazwa; ?>" method="post" class="space-y-4">
            <?php $i=1;
            foreach ($pytania as $pytanie): ?>
                <div class="mb-4">
                    <h2 class="text-2xl mb-2">Pytanie <?php echo $i; ?></h2>
                    <p class="mb-2"><?php echo "Treść pytania: " . $pytanie['question']; ?></p>
                    <div class="flex flex-col space-y-2"><br>
                        <?php $odpowiedzi = array($pytanie['answer_a'], $pytanie['answer_b'], $pytanie['answer_c'], $pytanie['answer_d']); ?>
                        <?php shuffle($odpowiedzi); ?>
                        <?php foreach ($odpowiedzi as $odpowiedz): ?>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="odpowiedz[<?php echo $pytanie['id']; ?>]"
                                       value="<?php echo $odpowiedz; ?>" class="mr-2">
                                <span><?php echo $odpowiedz; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php $i++;
            endforeach; ?>
            <div class="flex justify-end">
                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3">
                    Zakończ
                </button>
            </div>
        </form>
    </div>
</main>

</body>
</html>
