<?php
session_start();
if(!isset($_SESSION['loggedIn'])){
    header('Location: index.php');
    exit();
}
if(isset($_POST['emailUU']))
{
  //udana walidacja:
    $allOK=true;
    $imie = $_POST['Imie'];
    $nazwisko = $_POST['Nazwisko'];
    $typKonta = $_POST['typKonta'];
    if(isset($_POST['nrTelefonu'])) {
        $nrTelefonu = $_POST['nrTelefonu'];
    }else{
        $nrTelefonu = 34;
    }
    //spr poprawn adr email
    $emailUU = $_POST['emailUU'];
    $emailBUU = filter_var($emailUU, FILTER_SANITIZE_EMAIL);
    if((filter_var($emailBUU, FILTER_VALIDATE_EMAIL)==false)||($emailBUU!=$emailUU)){
        $allOK=false;
        $_SESSION['eEmail']="Nieprawidłowy adres e-mail";
    }
    //poprawnosc hasła:
    $passwordUU = $_POST['passwordUU'];
    $password2UU = $_POST['password2UU'];

    if((strlen($passwordUU)<6)||(strlen($passwordUU)>31)){
        $allOK=false;
        $_SESSION['ePassword'] = "Hasło musi zawierać od 6 do 31 znaków";
    }

    if($passwordUU!=$password2UU){
        $allOK=false;
        $_SESSION['ePassword'] = "Hasła muszą być jednakowe";
    }

    $passwordHashUU = password_hash($passwordUU, PASSWORD_DEFAULT);



    $_SESSION['frImie'] = $imie;
    $_SESSION['frNazwisko'] = $nazwisko;
    $_SESSION['frEmail'] = $emailUU;
    $_SESSION['frPassword'] = $passwordUU;
    $_SESSION['frPassword2'] = $password2UU;




    require_once 'connect.php';
    mysqli_report(MYSQLI_REPORT_STRICT);

    try{
        $connection = new mysqli($host, $db_user, $db_password, $db_name);
        if($connection->connect_errno!=0){
            throw new Exception(mysqli_connect_errno());
        }else {
            //czy email juz istnieje
            $result = $connection->query("SELECT id FROM users WHERE email='$emailUU'");
            if (!$result) throw new Exception($connection->error);

            if ($result->num_rows > 0) {
                $allOK = false;
                $_SESSION['eEmail'] = "Istnieje już konto przypisane do tego adresu e-mail";
            }

            if ($allOK == true) {
                //testy zaliczone



                   if ($connection->query("INSERT INTO users VALUES(NULL, '$imie', '$nazwisko', '$emailUU', '$passwordHashUU', '$typKonta', '$nrTelefonu', NULL, NULL)")) {

                       $_SESSION['registrationOK'] = true;
                       header('Location: spradmin.php');
                   } else {
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
    <title>Dodawanie użytkownika</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <meta name="description" content="Opis zawartości strony dla wyszukiwarek">
    <meta name="keywords" content="słowa, kluczowe, opisujące, zawartość">
    <meta name="author" content="Jan Programista">

    <meta http-equiv="X-Ua-Compatible" content="IE=edge,chrome=1">
    <link rel="stylesheet" href="css/style.css">
    <!-- to ma być w style.css =-->
    <style>

        </style>
</head>
<div class="container">
    <nav>Program lojalnościowy firmy Pro Investment Kielce Sp. z o.o.</nav>
    <div id="nav2"><a href="logout.php" class="tilelink"> Wyloguj mnie</a></div>
    <main>

        <?php
        echo "Witaj ".$_SESSION['imie'].' '.$_SESSION['nazwisko'].PHP_EOL;
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
                <th>Typ konta</th>
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
                        sprintf("SELECT * FROM users ORDER BY id DESC LIMIT 10",
                            mysqli_real_escape_string($connection)))) {



                        if (($result->num_rows) > 0) {
                            $i=1;
                            while($row = $result->fetch_assoc()){
                                $sumaPunktow = $row['punktyNaliczone'] - $row['punktyWydane'];
                                echo "<td>".$i."</td><td>".$row['id']."</td><td>".$row['imie']."</td><td>".$row['nazwisko']."</td><td>".$row['typKonta']."</td><td>".$row['nrTelefonu']."</td><td>".$row['email']."</td><td>".$sumaPunktow."</td></tr>";
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
        <form method="post">
            Imię: <br/><input type="text" value="<?php
            if (isset($_SESSION['frImie']))
            {
                echo $_SESSION['frImie'];
                unset($_SESSION['frImie']);
            }
            ?>"name="Imie"/><br/>
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
            ?>"name="Nazwisko"/><br/>
            <?php
            if(isset($_SESSION['eImie'])){
                echo '<div class="error">'.$_SESSION['eImie'].'</div>';
                unset($_SESSION['eImie']);
            }
            ?>
            Email: <br/><input type="email" value="<?php
            if (isset($_SESSION['frEmail']))
            {
                echo $_SESSION['frEmail'];
                unset($_SESSION['frEmail']);
            }
            ?>" name="emailUU"/><br/>
            <?php
            if(isset($_SESSION['eEmail'])){
                echo '<div class="error">'.$_SESSION['eEmail'].'</div>';
                unset($_SESSION['eEmail']);
            }
            ?>
            Nr telefonu/klienta: <br/><input type="text" value="111111"name="nrTelefonu"/><br/>

            Hasło: <br/><input type="password" value="<?php
            if (isset($_SESSION['frPassword']))
            {
                echo $_SESSION['frPassword'];
                unset($_SESSION['frPassword']);
            }
            ?>" name="passwordUU"/><br/>
            <?php
            if(isset($_SESSION['ePassword'])){
                echo '<div class="error">'.$_SESSION['ePassword'].'</div>';
                unset($_SESSION['ePassword']);
            }
            ?>
            Powtórz hasło: <br/><input type="password" value="<?php
            if (isset($_SESSION['frPassword2']))
            {
                echo $_SESSION['frPassword2'];
                unset($_SESSION['frPassword2']);
            }
            ?>" name="password2UU"/><br/>
            <!--
            Podaj typ konta: <br/><input type="text" value="<?php
            if (isset($_SESSION['frtypKonta']))
            {
                echo $_SESSION['frtypKonta'];
                unset($_SESSION['frtypKonta']);
            }
            ?>" name="typKonta"/><br/>
-->

            <label for="typKonta">Wybierz uprawnienia:</label>
            <br>
            <select name="typKonta" id="typKonta">
                <option value="uczestnik">Uczestnik</option>
                <option value="redaktor">Redaktor</option>
                <option value="sprredaktor">SuperRedaktor</option>
                <option value="spradm">SuperUser</option>

            </select>

            <br>
            <input type="submit" class="btn"value="Dodaj usera">
        </form>
    </div>
    <div id="content1">Content1</div>
    <div id="content2">Content2</div>
    <div id="content3">Content3</div>
    <footer>Footer</footer>
</div>


</html>
