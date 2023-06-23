<?php
session_start();
$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: logowanie.php");
    exit();
}

$idu = $_SESSION['id'];

if (
    !isset($_SESSION['quiz_title']) ||
    !isset($_SESSION['quiz_description']) ||
    !isset($_SESSION['quiz_numQuestions'])
) {
    header("Location: quiz_tworzenie.php");
    exit();
}

$title = $_SESSION['quiz_title'];
$description = $_SESSION['quiz_description'];
$numQuestions = $_SESSION['quiz_numQuestions'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valid = true;
    $error = "";

    for ($i = 1; $i <= $numQuestions; $i++) {
        if (
            !isset($_POST['question_' . $i]) || empty($_POST['question_' . $i]) ||
            !isset($_POST['question_type_' . $i]) || empty($_POST['question_type_' . $i]) ||
            !isset($_POST['correct_answer_' . $i]) || empty($_POST['correct_answer_' . $i]) ||
            !isset($_POST['answer_a_' . $i]) || empty($_POST['answer_a_' . $i]) ||
            !isset($_POST['answer_b_' . $i]) || empty($_POST['answer_b_' . $i]) ||
            !isset($_POST['answer_c_' . $i]) || empty($_POST['answer_c_' . $i]) ||
            !isset($_POST['answer_d_' . $i]) || empty($_POST['answer_d_' . $i])
        ) {
            $valid = false;
            $error = "Wszystkie pola są wymagane.";
            break;
        }
    }

    if ($valid) {
        require_once "connect.php";
        global $host, $db_user, $db_password, $db_name;
        $conn = new mysqli($host, $db_user, $db_password, $db_name);

        if ($conn->connect_error) {
            die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
        }

        $table_name = str_replace(' ', '_', $title);
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            question TEXT NOT NULL,
            typ VARCHAR(50) NOT NULL,
            correct_answer VARCHAR(255) NOT NULL,
            answer_a VARCHAR(255) NOT NULL,
            answer_b VARCHAR(255) NOT NULL,
            answer_c VARCHAR(255) NOT NULL,
            answer_d VARCHAR(255) NOT NULL
        )";

        if ($conn->query($sql) === FALSE) {
            die("Błąd tworzenia tabeli: " . $conn->error);
        }

        for ($i = 1; $i <= $numQuestions; $i++) {
            $question = $_POST['question_' . $i];
            $question_type = $_POST['question_type_' . $i];
            $correct_answer = $_POST['correct_answer_' . $i];
            switch ($correct_answer) {
                case 'A':
                    $correct_answer = $_POST['answer_a_' . $i];
                    break;
                case 'B':
                    $correct_answer = $_POST['answer_b_' . $i];
                    break;
                case 'C':
                    $correct_answer = $_POST['answer_c_' . $i];
                    break;
                case 'D':
                    $correct_answer = $_POST['answer_d_' . $i];
                    break;

            }
            $answer_a = $_POST['answer_a_' . $i];
            $answer_b = $_POST['answer_b_' . $i];
            $answer_c = $_POST['answer_c_' . $i];
            $answer_d = $_POST['answer_d_' . $i];

            $sql = "INSERT INTO $table_name (question, typ, correct_answer, answer_a, answer_b, answer_c, answer_d) VALUES ( ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $question, $question_type, $correct_answer, $answer_a, $answer_b, $answer_c, $answer_d);
            $stmt->execute();
            $stmt->close();
        }

        $conn->close();
        $quiz_name = $_SESSION['quiz_title'];
        $question_count = $numQuestions;
        $creation_date = date('Y-m-d H:i:s');
        $execution_count = 0;

        require_once "connect.php";

        $conn = new mysqli($host, $db_user, $db_password, $db_name);
        if ($conn->connect_error) {
            die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
        }

        $sql = "INSERT INTO Quiz (id_uzytkownika, quiz_name, question_count, creation_date, execution_count)
        VALUES ('$idu','$quiz_name', '$question_count', '$creation_date', '$execution_count')";

        if ($conn->query($sql) === TRUE) {
            echo "Rekord został dodany do tabeli Quiz.";
        } else {
            echo "Błąd podczas dodawania rekordu: " . $conn->error;
        }

        $conn->close();
        header("Location: quiz_dodano.php");
        exit();
    } else {
        $error = "Wszystkie pola są wymagane.";
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
<main class="container mx-auto mt-8">
    <h1 class="text-3xl text-white text-center mb-4">Stwórz quiz</h1>
    <div class="max-w-2xl mx-auto">
        <form method="POST" enctype="multipart/form-data" class="max-w-2xl mx-auto">
            <?php for ($i = 1; $i <= $numQuestions; $i++): ?>
                <h3 class="text-xl mb-4 text-white">Pytanie <?php echo $i; ?>:</h3>

                <div class="mb-4">
                    <label class="block mb-2 text-white">Pytanie:</label>
                    <input type="text" name="question_<?php echo $i; ?>" required class="w-full px-3 py-2 border rounded"/>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-white">Typ pytania:</label>
                    <select name="question_type_<?php echo $i; ?>" required class="w-full px-3 py-2">
                        <option value="text">Jednokrotnego wyboru</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-white">Odpowiedź poprawna:</label>
                    <div class="flex">
                        <label class="mr-4">
                            <input type="radio" name="correct_answer_<?php echo $i; ?>" value="A" required> A
                        </label>
                        <label class="mr-4">
                            <input type="radio" name="correct_answer_<?php echo $i; ?>" value="B" required> B
                        </label>
                        <label class="mr-4">
                            <input type="radio" name="correct_answer_<?php echo $i; ?>" value="C" required> C
                        </label>
                        <label class="mr-4">
                            <input type="radio" name="correct_answer_<?php echo $i; ?>" value="D" required> D
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-white">Odpowiedź A:</label>
                    <input type="text" name="answer_a_<?php echo $i; ?>" required
                           class="w-full px-3 py-2 border rounded"/>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-white">Odpowiedź B:</label>
                    <input type="text" name="answer_b_<?php echo $i; ?>" required
                           class="w-full px-3 py-2 border rounded"/>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-white">Odpowiedź C:</label>
                    <input type="text" name="answer_c_<?php echo $i; ?>" required
                           class="w-full px-3 py-2 border rounded"/>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-white">Odpowiedź D:</label>
                    <input type="text" name="answer_d_<?php echo $i; ?>" required
                           class="w-full px-3 py-2 border rounded"/>
                </div>
            <?php endfor; ?>

            <div class="text-center">
                <input type="submit" value="Zapisz quiz" class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3"/>
            </div>
        </form>

    </div>
</main>
</body>
</html>