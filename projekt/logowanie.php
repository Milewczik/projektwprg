<?php
session_start();

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if ($isLoggedIn) {
    header("Location: index.php");
    exit();
}

interface LoginInterface {
    public function loginUser($login, $password);
}

class Database {
    protected $conn;

    public function __construct($host, $db_user, $db_password, $db_name) {
        try {
            $dsn = "mysql:host=$host;dbname=$db_name";
            $this->conn = new PDO($dsn, $db_user, $db_password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}

class Login extends Database implements LoginInterface {
    private $errors;

    public function __construct($host, $db_user, $db_password, $db_name) {
        parent::__construct($host, $db_user, $db_password, $db_name);
        $this->errors = array();
    }

    public function loginUser($login, $password) {
        $sql = "SELECT * FROM uzytkownicy WHERE login = :login";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user["password"])) {
                $_SESSION["zalogowany"] = true;
                $_SESSION["user"] = $user;
                $_SESSION['user']['username'] = $login;
                $_SESSION['id'] = $user['id'];
                header("Location: index.php");
                exit();
            } else {
                $this->errors['password_error'] = "Podano nieprawidłowe hasło.";
            }
        } else {
            $this->errors['login_error'] = "Podano nieprawidłowy login.";
        }

        return $this->errors;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = $_POST["login"];
    $password = $_POST["password"];
    require_once "connect.php";
    global $host, $db_user, $db_password, $db_name;
    $loginObject = new Login($host, $db_user, $db_password, $db_name);
    $errors = $loginObject->loginUser($login, $password);

    if (isset($errors['password_error'])) {
        $password_error = $errors['password_error'];
    }

    if (isset($errors['login_error'])) {
        $login_error = $errors['login_error'];
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
<main class="mx-auto">
    <form class="mb-4" method="post">
        <label class="block text-white" for="login">Login użytkownika</label>
        <input class="py-2 px-3 leading-tight" id="login" type="text" name="login" required>
        <?php if (isset($login_error)): ?>
            <div class="text-red-500"><?php echo $login_error; ?></div>
        <?php endif; ?>
        <div class="mb-4">
            <label class="block text-white" for="password">Hasło</label>
            <input class="py-2 px-3 leading-tight" id="password" type="password" name="password" required>
            <?php if (isset($password_error)): ?>
                <div class="text-red-500"><?php echo $password_error; ?></div>
            <?php endif; ?>
        </div>
        <?php
        if (isset($_SESSION['blad'])) {
            echo '<div class="error">' . $_SESSION['blad'] . '</div>';
            unset($_SESSION['blad']);
        }
        ?>
        <?php if ($isLoggedIn): ?>
            <p>Zalogowany użytkownik: <?php echo $_SESSION["user"]["username"]; ?></p>
            <p>ID użytkownika: <?php echo $_SESSION["id"]; ?></p>
        <?php endif; ?>
            <button class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-2 rounded mr-2 mt-3" type="submit">Zaloguj się
            </button>
    </form>
</main>
</body>
</html>
