<?php
session_start();
//ten if bedzie na kazdej stronie dostepnej tylko dla zalogowanych:
if(!isset($_SESSION['loggedIn'])){
    header('Location: index.php');
    exit();
}
$type = $_SESSION['type'];

?>
<!DOCTYPE html>
<html lang="pl">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php

        echo '<link rel="stylesheet" href="css/style.css">';

    ?>

    <title>RoboEducation</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    <meta name="description" content="Opis zawartości strony dla wyszukiwarek">
    <meta name="keywords" content="słowa, kluczowe, opisujące, zawartość">
    <meta name="author" content="Jan Programista">

    <meta http-equiv="X-Ua-Compatible" content="IE=edge,chrome=1">

    <script src="skrypt.js"></script>




</head>
<div class="container">
    <nav>Program lojalnościowy firmy Pro Investment Kielce Sp. z o.o.</nav>
    <div id="nav2"><a href="logout.php" class="tilelink"> Wyloguj mnie</a></div>
    <main>


        <?php
        echo "Witaj ".$_SESSION['imie'].' '.$_SESSION['nazwisko'].PHP_EOL;
        ?><br>
        <?php
        echo "Tabela zawiera listę uczestników progrtamu lojalnościowego:</br><br>"
        ?>
        <table class="tabela">
           <thead>
           <tr>
               <th>Lp</th>
               <th>Id</th>
               <th>Imię</th>
               <th>Nazwisko</th>
               <th>Nr klienta</th>
               <th>E-mail</th>
               <th>Liczba punktów</th>
           </tr>
           </thead>
            <tbody>

        <?php


        require_once "connect.php";
        mysqli_report(MYSQLI_REPORT_STRICT);

        try {
        $connection = new mysqli($host, $db_user, $db_password, $db_name);

        if ($connection->connect_errno != 0) {
       throw new Exception(mysqli_connect_errno());
        } else {

              if ($result = $connection->query(
        sprintf("SELECT * FROM users WHERE typKonta= '%s' ORDER BY id DESC LIMIT 5",
        mysqli_real_escape_string($connection, 'uczestnik')))) {



        if (($result->num_rows) > 0) {
        $i=1;
        while($row = $result->fetch_assoc()){
            $sumaPunktow = $row['punktyNaliczone'] - $row['punktyWydane'];
            echo "<td>".$i."</td><td>".$row['id']."</td><td>".$row['imie']."</td><td>".$row['nazwisko']."</td><td>".$row['nrTelefonu']."</td><td>".$row['email']."</td><td>".$sumaPunktow."</td></tr>";
            $i++;
        }
        echo "</tbody>
        </table>
       
        ";

        if (1==1) {



        unset($_SESSION['error']);
        $result->free_result();


        } else {
        $_SESSION['error'] = '<span style="color:red">Nieprawidłowy email lub hasło!</span>';
        header('Location: index.php');
        }
        } else {
        throw new Exception($connection->error);
        echo "Brak rezultatów";
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
            </tbody>
        </table>
    </main>
    <div id="sidebar">
        <button id="Regulamin" class="btn2" onclick="myFunction('Regulamin')">Regulamin.</button><br>




        <script>


            function myFunction(id) {
                var temat = "docs/"+id+".pdf";
                sessionStorage.setItem("temat", temat);
                var temat2 = id;
                sessionStorage.setItem("temat2", id);

               // document.getElementById('udostepnij').id = id;

                var URL="pdfreader2.php";
                window.location.replace(URL);
            }
        </script>
    </div>
    <?php
    echo '
    <div id="content1">Wyszukiwarka</div>
    <div id="content2">Content2</div>
    <div id="content3">Content3</div>
    <footer>Footer</footer>
        ';
    ?>


</div>
<body>




<div class="top-bar">


</div>
</body>

</html>
