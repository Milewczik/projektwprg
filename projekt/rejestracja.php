<?php

session_start();

if (isset($_POST['email'])) {
    $wszystko_OK = true;
    $login = $_POST['login'];

    if ((strlen($login) < 3) || (strlen($login) > 40)) {
        $wszystko_OK = false;
        $_SESSION['e_login'] = "Niepoprawna długość nazwy!";
    }

    if (ctype_alnum($login) == false) {
        $wszystko_OK = false;
        $_SESSION['e_login'] = "Nieprawidłowa nazwa(usuń polskie znaki i znaki specjalne)!";
    }
    $email = $_POST['email'];
    $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

    if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)) {
        $wszystko_OK = false;
        $_SESSION['e_email'] = "Podaj poprawny adres e-mail!";
    }
    $password = $_POST['password'];
    $samePassword = $_POST['samePassword'];

    if ((strlen($password) < 5) || (strlen($password) > 40)) {
        $wszystko_OK = false;
        $_SESSION['e_password'] = "Hasło musi być dłuższe niż 5 znaków!";
    }

    if ($password != $samePassword) {
        $wszystko_OK = false;
        $_SESSION['e_password'] = "Podane hasła są błędne!";
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    if (!isset($_POST['regulamin'])) {
        $wszystko_OK = false;
        $_SESSION['e_regulamin'] = "Akceptuj regulamin!";
    }

    $_SESSION['fr_login'] = $login;
    $_SESSION['fr_email'] = $email;
    $_SESSION['fr_password'] = $password;
    $_SESSION['fr_samePassword'] = $samePassword;
    if (isset($_POST['regulamin'])) $_SESSION['fr_regulamin'] = true;

    include_once "connect.php";
    mysqli_report(MYSQLI_REPORT_STRICT);

    try {
        global $host, $db_user, $db_password, $db_name;
        $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
        if ($polaczenie->connect_errno != 0) {
            throw new Exception(mysqli_connect_errno());
        } else {
            $rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");

            if (!$rezultat) throw new Exception($polaczenie->error);

            $ile_takich_maili = $rezultat->num_rows;
            if ($ile_takich_maili > 0) {
                $wszystko_OK = false;
                $_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu e-mail!";
            }

            $rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE login='$login'");

            if (!$rezultat) throw new Exception($polaczenie->error);

            $ile_takich_nickow = $rezultat->num_rows;
            if ($ile_takich_nickow > 0) {
                $wszystko_OK = false;
                $_SESSION['e_login'] = "Istnieje już użytkownik o takim nicku! Wybierz inny.";
            }

            if ($wszystko_OK == true) {
                if ($polaczenie->query("INSERT INTO uzytkownicy (login,password,email) VALUES ('$login', '$password_hash', '$email')")) {
                    $id = $polaczenie->insert_id;

                    $sql_insert_profile = "INSERT INTO profile (id_uzytkownicy, imie) VALUES ('$id', '$login')";
                    $query = "INSERT INTO ranking (login, punkty) VALUES ('$login', 0)";

                    if ($polaczenie->query($sql_insert_profile) === TRUE && $polaczenie->query($query) === TRUE) {
                        $polaczenie->commit();
                        $_SESSION['udanarejestracja'] = true;

                        header('Location: zarejestrowano.php');
                    } else {
                        throw new Exception($polaczenie->error);
                    }
                } else {
                    throw new Exception($polaczenie->error);
                }
            }
            $polaczenie->close();
        }

    } catch (Exception $e) {
        $informationAboutBase = '<span style="color:red;">Błąd serwera!</span>';
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
        form {
            max-width: 600px;
            margin: 0 auto;
        }

        html, body {
            height: 100%;
            margin: 0;
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
<form class="mx-auto" method="post">
    <label class="block text-white mb-2" for="name"><br>
        Login użytkownika
    </label>
    <input class="py-2 px-3" id="login" type="text" name="login" required value="<?php
    if (isset($_SESSION['fr_login'])) {
        echo $_SESSION['fr_login'];
        unset($_SESSION['fr_login']);
    }
    ?>">
    <h1>
        <?php
        if (isset($_SESSION['e_login'])) {
            echo '<div class="error">' . $_SESSION['e_login'] . '</div>';
            unset($_SESSION['e_login']);
        }
        ?>
    </h1>
    <div class="mb-4">
        <label class="block text-white mb-2" for="email"><br>
            Adres e-mail
        </label>
        <input class="py-2 px-3 " id="email" type="email" name="email" required value="<?php
        if (isset($_SESSION['fr_email'])) {
            echo $_SESSION['fr_email'];
            unset($_SESSION['fr_email']);
        }
        ?>">
        <h1>
            <?php
            if (isset($_SESSION['e_email'])) {
                echo '<div class="error">' . $_SESSION['e_email'] . '</div>';
                unset($_SESSION['e_email']);
            }
            ?>
        </h1>
    </div>
    <div class="mb-4">
        <label class="block text-white mb-2" for="password">
            Hasło
        </label>
        <input class="py-2 px-3 leading-tight" id="password" type="password" name="password" required value="<?php
        if (isset($_SESSION['fr_password'])) {
            echo $_SESSION['fr_password'];
            unset($_SESSION['fr_password']);
        }
        ?>">
        <h1>
            <?php
            if (isset($_SESSION['e_password'])) {
                echo '<div class="error">' . $_SESSION['e_password'] . '</div>';
                unset($_SESSION['e_password']);
            }
            ?>
        </h1>
    </div>
    <div class="mb-4">
        <label class="block text-white" for="email">
            Powtórz hasło
        </label>
        <input class="py-2 px-3 leading-tight" id="samePassword" type="password" name="samePassword" required value="<?php
        if (isset($_SESSION['fr_samePassword'])) {
            echo $_SESSION['fr_samePassword'];
            unset($_SESSION['fr_samePassword']);
        }
        ?>">
    </div>
    <div class="mb-4">
        <label class="block text-white mb-2" for="email">
            Akceptuję regulamin<input id="regulamin" type="checkbox" name="regulamin" required <?php
            if (isset($_SESSION['fr_regulamin'])) {
                echo "checked";
                unset($_SESSION['fr_regulamin']);
            }
            ?>>
            <h1>
                <?php
                if (isset($_SESSION['e_regulamin'])) {
                    echo '<div class="error">' . $_SESSION['e_regulamin'] . '</div>';
                    unset($_SESSION['e_regulamin']);
                }
                ?>
            </h1>
        </label>
    </div>

    <button class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3" type="submit">
        Zarejestruj się
    </button>
    <br>
    <h3><?php if (!(empty($informationAboutBase))) {
            echo $informationAboutBase;
        } ?></h3>
</form>
</body>
</html>