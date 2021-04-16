<?php
session_start();
if((!isset($_POST['email'])) || (!isset($_POST['password']))){
    header('Location: index.php');
    exit();
}


require_once "connect.php";
mysqli_report(MYSQLI_REPORT_STRICT);

try {
    $connection = new mysqli($host, $db_user, $db_password, $db_name);

    if ($connection->connect_errno != 0) {
        throw new Exception(mysqli_connect_errno());
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];

        //  $email = htmlentities($email, ENT_QUOTES, "UTF-8");

        if ($result = $connection->query(
            sprintf("SELECT * FROM users WHERE email= '%s'",
                mysqli_real_escape_string($connection, $email)))) {



            if (($result->num_rows) > 0) {


               $row = $result->fetch_assoc();

                if (password_verify($password, $row['password'])) {


                $_SESSION['loggedIn'] = true;
                $_SESSION['id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['imie'] = $row['imie'];
                $_SESSION['nazwisko'] = $row['nazwisko'];
                $_SESSION['typKonta'] = $row['typKonta'];
               // $_SESSION['typKonta'] = 'redaktor';
                unset($_SESSION['error']);
                $result->free_result();

                if($_SESSION['typKonta'] == 'spradmin'){
                   // echo $_SESSION['typKonta'];
                    header('Location: spradmin.php');
                }
                elseif($_SESSION['typKonta'] == 'redaktor'){
                    //echo $_SESSION['typKonta'];

                    header('Location: redaktor.php');
                }
                elseif($_SESSION['typKonta'] == 'sprredaktor'){
                    //echo $_SESSION['typKonta'];

                    header('Location: sprredaktor.php');
                }
                else{
                   // echo $_SESSION['typKonta']." jestem tutaj";
                    header('Location: home.php');
                }


            } else {
                $_SESSION['error'] = '<span style="color:red">Nieprawidłowy email lub hasło!</span>';
                header('Location: index.php');
            }
        } else {
            throw new Exception($connection->error);
            echo "tu jestem";
        }


                $connection->close();

            }
        }
    }

catch(Exception $e)
{
    echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o wizytę w innym terminie!</span>';
    echo '<br />Informacja developerska: '.$e;
}





?>
<!DOCTYPE html>
<html lang="pl">

<head>

    <meta charset="utf-8">
    <title>RoboEducation</title>

    <meta name="description" content="Opis zawartości strony dla wyszukiwarek">
    <meta name="keywords" content="słowa, kluczowe, opisujące, zawartość">
    <meta name="author" content="Jan Programista">

    <meta http-equiv="X-Ua-Compatible" content="IE=edge,chrome=1">
    <link rel="stylesheet" href="arkusz.css">
    <script src="skrypt.js"></script>

</head>
<body>

</body>

</html>
<?php

?>
