<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDYCJA UCZNIA</title>
</head>
<body>
<?php
session_start();
if(!isset($_SESSION['login'])) { header("Location:index.php"); exit; }

if(isset($_GET['id_ucznia']))
{
    $id_ucznia = $_GET['id_ucznia'];

    require_once "dbconnect.php";

    $connection = mysqli_connect($host, $user, $password, $dbname);
    
    $query = "SELECT * FROM uczen WHERE id=$id_ucznia AND id_klasy=".$_SESSION['id_klasy'];

    $result = mysqli_query($connection, $query);

    $row = mysqli_fetch_assoc($result);

    $nazwisko = $row['Nazwisko'];
    $imie = $row['Imie'];
    $srednia_ocen = $row['Srednia_ocen'];
    
    mysqli_close($connection);

    echo "<p><strong>EDYCJA UCZNIA</strong><p>";

echo<<<END
    <form action="edytuj_ucznia.php" method="post">
        <input type="text" name="imie" value="$imie" required><br>
        <input type="text" name="nazwisko" value="$nazwisko" required><br>
        <input type="number" name="srednia_ocen" value="$srednia_ocen" required><br>
        <input type="hidden" name="id_ucznia" value="$id_ucznia"><br>
        <input type="submit" name="edytuj_ucznia" value="ZAPISZ"><br>
    <form>
END;

}
elseif(isset($_POST['edytuj_ucznia']))
{
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $srednia_ocen = $_POST['srednia_ocen'];
    $id_ucznia = $_POST['id_ucznia'];
    
    require_once "dbconnect.php";

    $connection = mysqli_connect($host, $user, $password, $dbname);
    
    $query = "UPDATE uczen SET Nazwisko = '$nazwisko', Imie = '$imie', `Srednia_ocen` = '$srednia_ocen' WHERE id = $id_ucznia AND id_klasy = ".$_SESSION['id_klasy'];

    mysqli_query($connection, $query);

    mysqli_close($connection);

    header("Location:index.php");
}



?>
</body>
</html>