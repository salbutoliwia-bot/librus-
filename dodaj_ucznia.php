<?php
    session_start();
    if(!isset($_SESSION['login'])) { header("Location:index.php"); exit; }
    if(isset($_POST['dodaj_ucznia']))
    {
        $imie = $_POST['imie'];
        $nazwisko = $_POST['nazwisko'];
        $srednia_ocen = $_POST['srednia_ocen'];
        $id_klasy = $_POST['id_klasy'];

        require_once "dbconnect.php";

        $connection = mysqli_connect($host, $user, $password, $dbname);
        
        if($id_klasy == $_SESSION['id_klasy']) {
        $query = "INSERT INTO uczen VALUES (NULL, '$nazwisko', '$imie', '$srednia_ocen', '$id_klasy')";

        mysqli_query($connection, $query);
        }

        mysqli_close($connection);
    }
    
    header("Location:index.php");

?>