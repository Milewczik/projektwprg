<?php
session_start();
$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: logowanie.php");
    exit();
}

require_once "connect.php";
global $host, $db_user, $db_password, $db_name;
$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
}

$sql = "SELECT p.imie, q.quiz_name, q.question_count, u.login, q.creation_date, q.execution_count FROM Quiz q JOIN uzytkownicy u on u.id = q.id_uzytkownika JOIN profile p on u.id = p.id_uzytkownicy;";

$result = $conn->query($sql);

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
    <h1 class="text-3xl text-white mb-4 text-center">Lista quizów</h1>
    <div class="mx-auto">
        <table class="table-auto text-white w-full border">
            <thead>
            <tr>
                <th class="px-4 py-2 bg-black">Nazwa quizu</th>
                <th class="bg-black">Liczba pytań</th>
                <th class="bg-black">Nazwa autora</th>
                <th class="bg-black">Data utworzenia</th>
                <th class="bg-black">Liczba wykonań</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($row = $result->fetch_assoc()):
                $nazwa = $row['quiz_name'];
                $login = $row['login'];
                $ilosc_pytan = $row['question_count'];
                $nazwa_uzytkownika = $row['imie'];
                $data = $row['creation_date'];
                $ilosc_wykonan = $row['execution_count'];
                ?>
                <tr>
                    <td class="border px-4 py-2">
                        <a href="quiz.php?nazwa=<?php echo $nazwa; ?>" class="text-purple-700 hover:underline">
                            <?php echo $nazwa; ?>
                        </a>
                    </td>
                    <td class="border px-4 py-2"><?php echo $ilosc_pytan; ?></td>
                    <td class="border px-4 py-2">
                        <a href="profil.php?uzytkownik=<?php echo $login; ?>" class="text-purple-700 hover:underline">
                            <?php echo $nazwa_uzytkownika; ?>
                        </a>
                    </td>
                    <td class="border px-4 py-2"><?php echo $data; ?></td>
                    <td class="border px-4 py-2"><?php echo $ilosc_wykonan; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <br/>
        <a href="quiz_nowy.php" class="text-center text-3xl text-purple-700 py-4">Najnowszy quiz</a><br/><br/>
        <a href="quiz_popularny.php" class="text-center text-3xl text-purple-700 py-4">Najpopularniejszy quiz</a><br/><br/>
    </div>
</main>
</body>
</html>
