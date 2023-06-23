<?php
session_start();

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: logowanie.php");
    exit();
}

$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['title']) && !empty($_POST['title']) &&
        isset($_POST['description']) && !empty($_POST['description']) &&
        isset($_POST['numQuestions']) && !empty($_POST['numQuestions'])
    ) {
        $title = $_POST['title'];
        $title = str_replace(' ', '_', $title);
        $title = urlencode($title);

        $description = $_POST['description'];
        $numQuestions = (int)$_POST['numQuestions'];

        require_once "connect.php";
        global $host, $db_user, $db_password, $db_name;
        $conn = new mysqli($host, $db_user, $db_password, $db_name);

        if ($conn->connect_error) {
            die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
        }

        $sql_check = "SELECT quiz_name FROM Quiz WHERE quiz_name = '$title'";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            $error = "Nazwa quizu jest już zajęta. Wybierz inną nazwę.";
        } else {
            if ($numQuestions > 0 && $numQuestions <= 20) {
                $_SESSION['quiz_title'] = urldecode($title);
                $_SESSION['quiz_description'] = $description;
                $_SESSION['quiz_numQuestions'] = $numQuestions;

                header("Location: quiz_wypelnianie.php");
                exit();
            } else {
                $error = "Liczba pytań musi być między 1 a 20.";
            }
        }

        $conn->close();
    } else {
        $error = "Wszystkie pola są wymagane.";
    }
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
<main class="container mx-auto mt-8">
    <h1 class="text-3xl text-white text-center mb-4">Stwórz quiz</h1>
    <div class="text-center max-w-2xl mx-auto p-8">
        <form action="quiz_tworzenie.php" method="post">
            <?php if (isset($error)): ?>
                <p class="text-red-500 mb-4"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="mb-4">
                <label for="title" class="block text-white">Tytuł quizu:</label>
                <input type="text" id="title" name="title" class="w-full px-4 py-2" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-white">Opis quizu:</label>
                <textarea id="description" name="description" class="w-full px-4 py-2" required></textarea>
            </div>
            <div class="mb-4">
                <label for="numQuestions" class="block text-white">Liczba pytań (max 20):</label>
                <input type="number" id="numQuestions" name="numQuestions" class="w-full px-4 py-2" min="1" max="20" required>
            </div>
            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3">
                Przejdź do tworzenia quizu
            </button>
        </form>
    </div>
</main>

</body>
</html>
