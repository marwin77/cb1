<?php
session_start();
//ten if bedzie na kazdej stronie dostepnej tylko dla zalogowanych:
if(!isset($_SESSION['loggedIn'])){
    header('Location: index.php');
    exit();
}
if(isset ($_POST['nrTelefonuU']))
{
    $allOK=true;
    $imieU = $_POST['imieU'];
    $nazwiskoU = $_POST['nazwiskoU'];

    $passwordHashU = $_POST['nrTelefonuU'];
    $typKontaU = 'uczestnik';
    $nrTelefonuU = $_POST['nrTelefonuU'];

    //spr poprawn adr email
    $emailU = $_POST['emailU'];
    $emailB = filter_var($emailU, FILTER_SANITIZE_EMAIL);
    if((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false)||($emailB!=$emailU)){
        $allOK=false;
        $_SESSION['eEmailU']="Nieprawidłowy adres e-mail";
    }
    //poprawnosc hasła:
    $passwordU = $_POST['passwordU'];
    $password2U = $_POST['password2U'];

    if((strlen($passwordU)<6)||(strlen($passwordU)>31)){
        $allOK=false;
        $_SESSION['ePasswordU'] = "Hasło musi zawierać od 6 do 31 znaków";
    }

    if($passwordU != $password2U){

       $allOK=false;
       $_SESSION['ePasswordU'] = "Hasła muszą być jednakowe";
    }

    $passwordHashU = password_hash($passwordU, PASSWORD_DEFAULT);

    require_once 'connect.php';
    mysqli_report(MYSQLI_REPORT_STRICT);
    try{
        $connection = new mysqli($host, $db_user, $db_password, $db_name);
        if($connection->connect_errno!=0){
            throw new Exception(mysqli_connect_errno());
        }else{
            //czy email juz istnieje

            $result = $connection->query("SELECT id FROM users WHERE email='$emailU'");
            if(!$result) throw new Exception($connection->error);

            if($result->num_rows>0){
                $allOK=false;
                $_SESSION['eEmailU'] = "Istnieje już konto przypisane do tego adresu email";
            }

            $result2 = $connection->query("SELECT id FROM users WHERE nrTelefonu='$nrTelefonuU'");
            if(!$result2) throw new Exception($connection->error);

            if($result2->num_rows>0){
                $allOK=false;
                $_SESSION['eNrTelefonuU'] = "Istnieje już konto przypisane do tego numeru telefonu";
            }

            if($allOK==true){
                //testy zaliczone

                if($connection->query("INSERT INTO users VALUES(NULL, '$imieU', '$nazwiskoU', '$emailU', '$passwordHashU', '$typKontaU', '$nrTelefonuU', NULL, NULL)"))
                {

                    $_SESSION['registrationOK'] = true;
                    header('Location: sprredaktor.php');
                }else{
                    throw new Exception($connection->error);
                }
                exit();
            }
            $connection->close();
        }
    }catch(Exception $e){
        echo '<span style = "color:red;"> Błąd serwera, spróbuj później</span>';
    }
}


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
        echo "Witaj superrdaktorze".$_SESSION['imie'].' '.$_SESSION['nazwisko'].PHP_EOL;
        ?><br>
        <?php
        echo "Tabela zawiera listę ostatnio dodanych uczestników programu lojalnościowego:</br><br>"
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
                        sprintf("SELECT * FROM users WHERE typKonta= '%s' ORDER BY id DESC LIMIT 10",
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
            }marwin

            ?>
            </tbody>
        </table>
    </main>
    <div id="sidebar">
        <form method="post">
            Imię: <br/><input type="text" value="<?php
            if (isset($_SESSION['frImie']))
            {
                echo $_SESSION['frImie'];
                unset($_SESSION['frImie']);
            }
            ?>"name="imieU"/><br/>
            <?php
            if(isset($_SESSION['eImie'])){
                echo '<div class="error">'.$_SESSION['eImie'].'</div>';
                unset($_SESSION['eImie']);
            }
            ?>

            Nazwisko: <br/><input type="text" value="<?php
            if (isset($_SESSION['frNazwisko']))
            {
                echo $_SESSION['frNazwisko'];
                unset($_SESSION['frNazwisko']);
            }
            ?>"name="nazwiskoU"/><br/>
            <?php
            if(isset($_SESSION['eImie'])){
                echo '<div class="error">'.$_SESSION['eImie'].'</div>';
                unset($_SESSION['eImie']);
            }
            ?>
            Nr telefonu/klienta: <br/><input type="text" value="<?php
            if (isset($_SESSION['frnrTelefonu']))
            {
                echo $_SESSION['frnrTelefonu'];
                unset($_SESSION['frnrTelefonu']);
            }
            ?>"name="nrTelefonuU"/><br/>
            <?php
            if(isset($_SESSION['eNrTelefonu'])){
                echo '<div class="error">'.$_SESSION['eNrTelefonu'].'</div>';
                unset($_SESSION['eNrTelefonu']);
            }
            ?>
            Email: <br/><input type="email" value="<?php
            if (isset($_SESSION['frEmail']))
            {
                echo $_SESSION['frEmail'];
                unset($_SESSION['frEmail']);
            }
            ?>" name="emailU"/><br/>
            <?php
            if(isset($_SESSION['eEmailU'])){
                echo '<div class="error">'.$_SESSION['eEmailU'].'</div>';
                unset($_SESSION['eEmailU']);
            }
            ?>
            Hasło: <br/><input type="password" value="<?php
            if (isset($_SESSION['frPassword']))
            {
                echo $_SESSION['frPassword'];
                unset($_SESSION['frPassword']);
            }
            ?>" name="passwordU"/><br/>
            <?php
            if(isset($_SESSION['ePasswordU'])){
                echo '<div class="error">'.$_SESSION['ePasswordU'].'</div>';
                unset($_SESSION['ePasswordU']);
            }
            ?>
            Powtórz hasło: <br/><input type="password" value="<?php
            if (isset($_SESSION['frPassword2']))
            {
                echo $_SESSION['frPassword2'];
                unset($_SESSION['frPassword2']);
            }
            ?>" name="password2U"/><br/>




            <br>
            <input type="submit" class="btn"value="Dodaj usera">
        </form>
    </div>

    <div id="content1">

    Wyszukiwarka po numerze klienta<br>
    <form action='sprredaktor.php' method="post">
            <br/><input type="text" name="nrKlienta"/><br/>

            <input type="submit" class="btn" value="Szukaj"/><br>

        </form>
    </div>
    <div id="content2">
        <button id="Regulamin" class="btn2" onclick="myFunction('id')">Karta klienta nr

        </button><br>
        <?php
        if(isset($_POST['nrKlienta'])){
            echo $_POST['nrKlienta'];
            $_SESSION['nrKlientaK'] = $_POST['nrKlienta'];
        }

        ?>



        <script>


            function myFunction(id) {

                var URL="kartaKlienta.php";
                window.location.replace(URL);
            }
        </script>
    </div>
    <div id="content3">Content3</div>
    <footer>

        <?php
        if(isset ($_POST['nrKlienta']))
        {

            $nrKlienta = $_POST['nrKlienta'];
            // echo $nrKlienta;

            $allOK=true;

            require_once 'connect.php';
            mysqli_report(MYSQLI_REPORT_STRICT);
            try {
                $connection = new mysqli($host, $db_user, $db_password, $db_name);

                if ($connection->connect_errno != 0) {
                    throw new Exception(mysqli_connect_errno());
                } else {

                    if ($result = $connection->query(
                        sprintf("SELECT * FROM users WHERE nrTelefonu= '%s'",
                            mysqli_real_escape_string($connection, $nrKlienta)))) {



                        if (($result->num_rows) > 0) {
                            echo '
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
            ';
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
echo'
                       </tbody>
        </table>     
';




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
             //   echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o wizytę w innym terminie!</span>';
                echo '<br />Nie znaleziono uczestnika o tym numerze klienta. Spróbuj ponownie';
            }

        }

        ?>
    </footer>
</div>
<body>




<div class="top-bar">


</div>
</body>

</html>
