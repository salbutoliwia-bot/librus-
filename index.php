<?php
session_start();
$blad = "";
if(isset($_POST['zaloguj']))
{
    require_once "dbconnect.php";
    $connection = mysqli_connect($host, $user, $password, $dbname);

    $login = $_POST['login'];
    $haslo = $_POST['haslo'];
    $query = "SELECT * FROM wychowawca WHERE imie='$login' AND nazwisko='$haslo'";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result)==1)
    {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['login'] = $row['imie'];
        $_SESSION['nazwisko'] = $row['nazwisko'];
        $_SESSION['id_klasy'] = $row['id_klasy'];
        header("Location:index.php");
        exit;
    }
    else $blad = "ZŁE DANE LOGOWANIA";
    mysqli_close($connection);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Dziennik szkolny</title>
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">
    <link rel="stylesheet" href="main.css">
</head>
<body>
<?php
if(!isset($_SESSION['login']))
{
    echo<<<END
    <form action="index.php" method="post">
        LOGIN<br>
        <input type="text" name="login" required><br>
        HASŁO<br>
        <input type="password" name="haslo" required><br>
        <input type="submit" name="zaloguj" value="ZALOGUJ">
    </form>
END;
    if($blad!="") echo $blad;
}
else
{
?>
    <form action="wyloguj.php" method="post">
        <input type="submit" value="WYLOGUJ">
    </form>
    <form action="index.php" method="post">
        <select name="klasa" onchange="this.form.submit();">
            <option>WYBIERZ KLASĘ</option>
<?php
        require_once "dbconnect.php";
        $connection = mysqli_connect($host, $user, $password, $dbname);
        $query_klasy = "SELECT klasa.nazwa, wychowawca.imie, wychowawca.nazwisko FROM klasa, wychowawca WHERE klasa.id=wychowawca.id_klasy";
        $result_klasy = mysqli_query($connection, $query_klasy);
        while($kl = mysqli_fetch_assoc($result_klasy))
        {
            echo '<option>'.$kl['nazwa'].' - '.$kl['imie'].' '.$kl['nazwisko'].'</option>';
        }
?>
        </select>
    </form>
<?php
    if(isset($_POST['klasa']) || isset($_COOKIE['klasa']))
    {
        if(isset($_POST['klasa'])) $klasa = substr($_POST['klasa'], 0, 2);
        else $klasa = $_COOKIE['klasa'];

        $query = "SELECT uczen.id, Imie, Nazwisko, Srednia_ocen, id_klasy FROM uczen, klasa WHERE nazwa='" . $klasa . "' AND klasa.id=uczen.id_klasy";
        $result = mysqli_query($connection, $query);

        $liczba_wierszy = mysqli_num_rows($result);
        if($liczba_wierszy==0)
        {
            echo '<span style="color:red;">Nie ma takiej klasy w szkole!</span>';
        }
        else
        {
            $srednia_ocen = 0;
            $id_klasy = 0;
            $czy_wychowawca = false;
            $sprawdz_klase = mysqli_fetch_assoc($result);
            $id_klasy = $sprawdz_klase['id_klasy'];
            mysqli_data_seek($result, 0);
            if($_SESSION['id_klasy']==$id_klasy) $czy_wychowawca = true;

            echo '<p><strong>KLASA '.$klasa.'</strong></p>';
            echo '<table><thead><tr><th>Imię</th><th>Nazwisko</th><th>Średnia ocen</th>';
            if($czy_wychowawca) echo '<th></th><th></th>';
            echo '</tr></thead><tbody>';
            while($row = mysqli_fetch_assoc($result))
            {
                echo "<tr>";
                echo "<td>".$row['Imie']."</td>";
                echo "<td>".$row['Nazwisko']."</td>";
                echo "<td>".$row['Srednia_ocen']."</td>";
                $id = $row['id'];
                if($czy_wychowawca)
                {
                    echo '<td><button onclick="window.location.href=\'edytuj_ucznia.php?id_ucznia=' . $id . '\'">EDYTUJ</button></td>';
                    echo '<td><button onclick="if(confirm(\'Czy chcesz usunąć ucznia ' . $row['Imie'] . ' ' . $row['Nazwisko'] . '?\')) window.location.href=\'usun_ucznia.php?id_ucznia=' . $id . '\'">KASUJ</button></td>';
                }
                echo "</tr>";
                $srednia_ocen += $row['Srednia_ocen'];
            }
            echo '</tbody></table>';
            echo "<p>Średnia ocen klasy: " . round($srednia_ocen/$liczba_wierszy, 2) . "</p><hr>";
            if($czy_wychowawca)
            {
                echo "<p><strong>DODAWANIE NOWEGO UCZNIA</strong><p>";
                echo<<<END
                <form action="dodaj_ucznia.php" method="post">
                    <input type="text" name="imie" placeholder="IMIE" required><br>
                    <input type="text" name="nazwisko" placeholder="NAZWISKO" required><br>
                    <input type="number" name="srednia_ocen" placeholder="ŚREDNIA OCEN" required><br>
                    <input type="hidden" name="id_klasy" value="$id_klasy"><br>
                    <input type="submit" name="dodaj_ucznia" value="ZAPISZ"><br>
                <form>
END;
            }
            setcookie("klasa", $klasa);
        }
    }
    mysqli_close($connection);
}
?>
</body>
</html>
