<?php
session_start();
if((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn']==true)) {
    header('Location: home.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <meta charset="utf-8">
    <title>ProInvestmentCashBack</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="description" content="Opis zawartości strony dla wyszukiwarek">


    <meta http-equiv="X-Ua-Compatible" content="IE=edge,chrome=1">


</head>

<div class="container">
    <nav>Program lojalnościowy firmy Pro Investment Kielce Sp. z o.o.</nav>
   <div id="nav2">
       <!--
       <a href="register.php" class="tilelink"> Zarejestruj się</a>
       -->
   </div>
    <main>
        <form action="zaloguj.php" method="post">
            E-mail:<br/><input type="text" name="email"/><br/>
            Hasło:<br/><input type="password" name="password"/><br/>
            <input type="submit" class="btn" value="Zaloguj się"/><br>
            <?php
            if(isset($_SESSION['error'])){
                echo $_SESSION['error'];
            }

            ?>
        </form>
    </main>
    <div id="sidebar">Content4</div>
    <div id="content1">Content1</div>
    <div id="content2">Content2</div>
    <div id="content3">Content3</div>
    <footer>Footer</footer>
</div>





</html>

