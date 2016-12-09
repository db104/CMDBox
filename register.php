<!DOCTYPE html>
<html>
<head><?php
    session_start();

    $dsn="mysql:host=localhost;dbname=u-db104";
    $dbuser="db104";
    $dbpass="anohk4Aepu";

    try {
        $db=new PDO($dsn,$dbuser,$dbpass);
    }
    catch(PDOException $e){
        echo $e->getMessage();
        die();
    }
    ?>
    <title>Registrierung</title>
</head>
<body>

<?php
$showFormular = true; //Variable zum Anzeigen des Formulares

if(isset($_GET['register'])) {
    $error = false;
    $username = $_POST ['username'];
    $email = $_POST['email'];
    $passwort = $_POST['passwort'];
    $passwort2 = $_POST['passwort2'];

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Bitte eine gültige E-Mail-Adresse eingeben<br>';
        $error = true;
    }
    if(strlen($passwort) == 0) {
        echo 'Bitte ein Passwort angeben<br>';
        $error = true;
    }
    if($passwort != $passwort2) {
        echo 'Passwörter stimmen nicht überein<br>';
        $error = true;
    }

    //Überprüfe, dass die username noch nicht vergeben ist
    if(!$error) {
        $statement = $db->prepare("SELECT * FROM person WHERE username = :username");
        $result = $statement->execute(array('username' => $username));
        $user = $statement->fetch();

        if($user !== false) {
            echo 'Diese E-Mail-Adresse ist bereits vergeben<br>';
            $error = true;
        }
    }

    //Keine Fehler, wir können den Nutzer registrieren
    if(!$error) {
        $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);

        $statement = $db->prepare("INSERT INTO person (username, email, passwort) VALUES (:username, :email, :passwort)");
        $result = $statement->execute(array('username' => $username, 'email' => $email, 'passwort' => $passwort_hash));

        if($result) {
            echo 'Du wurdest erfolgreich registriert. <a href="login.php">Zum Login</a>';
            $showFormular = false;
        } else {
            echo 'Beim Abspeichern ist leider ein Fehler aufgetreten<br>';
        }
    }
}

if($showFormular) {
    ?>

    <form action="?register=1" method="post">
        Username:<br/>
        <input type="text" size="40" maxlength="250" name="username"><br/><br/>

        E-Mail:<br>
        <input type="email" size="40" maxlength="250" name="email"><br><br>

        Dein Passwort:<br>
        <input type="password" size="40"  maxlength="250" name="passwort"><br>

        Passwort wiederholen:<br>
        <input type="password" size="40" maxlength="250" name="passwort2"><br><br>

        <input type="submit" value="Abschicken">
    </form>

    <?php
} //Ende von if($showFormular)
?>

</body>
</html>