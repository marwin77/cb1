<?php
session_start();
//ten if bedzie na kazdej stronie dostepnej tylko dla zalogowanych:
if(!isset($_SESSION['loggedIn'])){
    header('Location: index.php');
    exit();
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

    <script>
        var nrKlienta = sessionStorage("nrKlienta");
    </script>




</head>
<div class="container">
    <nav>Program lojalnościowy firmy Pro Investment Kielce Sp. z o.o.</nav>
    <div id="nav2"><a href="logout.php" class="tilelink"> Wyloguj mnie</a></div>
    <main>


        <?php
        echo "Witaj ".$_SESSION['imie'].' '.$_SESSION['nazwisko'].PHP_EOL;
        ?><br>
        <?php
        echo "Strona zawiera dane uczestnika programu lojalnościowego"
        ?><br><br>



        <?php
        if(isset ($_SESSION['nrKlientaK']))
        {

            $nrKlienta = $_SESSION['nrKlientaK'];
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

                                $punktyWydane = $row['punktyWydane'];
                                $punktyNaliczone = $row['punktyNaliczone'];
                                $sumaPunktow = $punktyNaliczone - $punktyWydane;
                                $idK = $row['id'];
                                $imieK = $row['imie'];
                                $nazwiskoK = $row['nazwisko'];
                                $emailK = $row['email'];

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



    </main>
    <div id="sidebar">

    </div>

    <div id="content1">
     <?php
     echo $sumaPunktow
     ?>
    </div>
    <div id="content2">Odejmij punkty<br>

        Wyszukiwarka po numerze klienta<br>
        <form action='redaktor.php' method="post">
            <br/><input type="text" name="odejmijPunkty"/><br/>

            <input type="submit" class="btn" value="Szukaj"/><br>

        </form>
    </div>
    <div id="content2">
<?php
if (isset($_POST['odejmijPunkty'])){
    $odejmijPunkty = $_POST['odejmijPunkty'];
    if($sumaPunktow>=$odejmijPunkty){
        $nowePunktyWydane = $punktyWydane + $odejmijPunkty;

if(isset ($_POST['odejmijPunkty']))
{
    $allOK=true;

    require_once 'connect.php';
    mysqli_report(MYSQLI_REPORT_STRICT);
    try{
        $connection = new mysqli($host, $db_user, $db_password, $db_name);
        if($connection->connect_errno!=0){
            throw new Exception(mysqli_connect_errno());
        }else{

            if($allOK==true){
                //testy zaliczone

                if($connection->query("UPDATE users SET punktyWydane = '$nowePunktyWydane' WHERE id = '$idK'"))
                {
                    $_SESSION['registrationOK'] = true;
                    header('Location: kartaKlienta.php');
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


    }
    else {
        echo '<br>Brak wystarczającej liczby punktów na koncie klienta<br>';
    }
}
?>
        <?php
        if($_SESSION['typKonta']!='redaktor'){
            echo 'Zapłać punktami<br>
        <form action="kartaKlienta.php" method="post">
            <br/><input type="text" name="odejmijPunkty"/><br/>

            <input type="submit" class="btn" value="Zapłać"/><br>

        </form>';

        }
?>
    </div>
    <div id="content3">Edytuj dane klienta</div>


    <footer>
Edycja danych klienta<br>
        <?php
        if(isset ($_POST['Imie']))
        {
            $allOK=true;
            $imieU = $_POST['Imie'];
            $nazwiskoU = $_POST['Nazwisko'];
            $emailU = $_POST['Email'];
            $emailB = filter_var($emailU, FILTER_SANITIZE_EMAIL);
            if((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false)||($emailB!=$emailU)){
                $allOK=false;
                $_SESSION['eEmailU']="Nieprawidłowy adres e-mail";
            }


            require_once 'connect.php';
            mysqli_report(MYSQLI_REPORT_STRICT);
            try{
                $connection = new mysqli($host, $db_user, $db_password, $db_name);
                if($connection->connect_errno!=0){
                    throw new Exception(mysqli_connect_errno());
                }else{
                    //czy email juz istnieje
/*
                    $result = $connection->query("SELECT id FROM users WHERE email='$emailU'");
                    if(!$result) throw new Exception($connection->error);

                    if($result->num_rows>0){
                        $allOK=false;
                        $_SESSION['eEmailU'] = "Istnieje już konto przypisane do tego adresu email";
                    }
*/
                    /*
                                        $result2 = $connection->query("SELECT id FROM users WHERE nrTelefonu='$nrTelefonuU'");
                                        if(!$result2) throw new Exception($connection->error);

                                        if($result2->num_rows>0){
                                            $allOK=false;
                                            $_SESSION['eNrTelefonuU'] = "Istnieje już konto przypisane do tego numeru telefonu";
                                        }
                    */
                    if($allOK==true){
                        //testy zaliczone

                        if($connection->query("UPDATE users SET imie = '$imieU', nazwisko = '$nazwiskoU' WHERE id = '$idK'"))
                        {

                            $_SESSION['registrationOK'] = true;
                            header('Location: kartaKlienta.php');
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
        <form method="post">
            Imię: <input type="text" value="<?php
           echo $imieK;
            ?>"name="Imie"/>
            Nazwisko: <input type="text" value="<?php
            echo $nazwiskoK;
            ?>"name="Nazwisko"/>
<!--
            Email: <input type="email" value="<?php
           echo $emailK;
            ?>"name="Email"/>
-->
            <br>
            <input type="submit" class="btn"value="Zapisz dane">
        </form>

    </footer>



</div>
<body>




<div class="top-bar">


</div>
</body>

</html>
