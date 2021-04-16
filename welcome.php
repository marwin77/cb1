<?php
session_start();
if(!isset($_SESSION['registrationOK'])) {
    header('Location: index.php');
    exit();
}else{
    unset($_SESSION['registrationOK']);
}
if (isset($_SESSION['frLogin'])) unset($_SESSION['frLogin']);
if (isset($_SESSION['frEmail'])) unset($_SESSION['frEmail']);
if (isset($_SESSION['frPassword'])) unset($_SESSION['frPassword']);
if (isset($_SESSION['frPassword2'])) unset($_SESSION['frPassword2']);
if (isset($_SESSION['frRegulamin'])) unset($_SESSION['frRegulamin']);

//Usuwanie błędów rejestracji
if (isset($_SESSION['eLogin'])) unset($_SESSION['eLogin']);
if (isset($_SESSION['eEmail'])) unset($_SESSION['eEmail']);
if (isset($_SESSION['ePassword'])) unset($_SESSION['ePassword']);
if (isset($_SESSION['eRegulamin'])) unset($_SESSION['eRegulamin']);
//if (isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);
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
<h2>RoboEducation.PL</h2>
<h5>Dziękujemy za rejestrację w serwisie. Możesz już zalogować się na swoje konto!</h5>
<h4><a href="index.php">Zaloguj się na swoje konto</a></h4>

</body>

</html>

