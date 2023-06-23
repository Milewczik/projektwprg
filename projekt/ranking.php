<?php
session_start();
$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: logowanie.php");
    exit();
}
require "connect.php";
global $host, $db_user, $db_password, $db_name;
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);

if ($polaczenie->connect_errno) {
    echo "Błąd połączenia z bazą danych: " . $polaczenie->connect_error;
    exit();
}

$query = "SELECT p.imie, r.punkty,r.login FROM ranking  r JOIN uzytkownicy u on r.login = u.login JOIN profile p on u.id = p.id_uzytkownicy ORDER BY punkty DESC;";
$result = $polaczenie->query($query);
if (!$result) {
    echo "Błąd zapytania: " . $polaczenie->error;
    exit();
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
    <h1 class="text-3xl text-white py-4 flex items-center justify-center">Ranking</h1>
    <div class="mx-auto">
        <table class="table-auto text-white w-full border border-gray-300">
            <thead>
            <tr>
                <th class="px-4 py-2 bg-black">Miejsce</th>
                <th class="bg-black">Użytkownik</th>
                <th class="bg-black">Punkty</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $place = 1;
            while ($row = $result->fetch_assoc()):
                $username = $row['imie'];
                $points = $row['punkty'];
                $login = $row['login'];
                ?>
                <?php if ($place === 1): ?>
                <tr>
            <?php elseif ($place === 2): ?>
                <tr>
            <?php elseif ($place === 3): ?>
                <tr>
            <?php else: ?>
                <tr>
            <?php endif; ?>
                <td class="border px-4 py-2"><?php echo $place; ?></td>
                <td class="border px-4 py-2">
                    <a href="profil.php?uzytkownik=<?php echo $login; ?>" class="text-purple-700 hover:underline">
                        <?php echo $username; ?>
                    </a>
                </td>
                <td class="border px-4 py-2"><?php echo $points; ?></td>
                </tr>
                <?php
                $place++;
            endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>

<?php
$result->free_result();
$polaczenie->close();
?>
