<?php

session_start();
require_once('connection.php');
$db = getDataBaseConnection();

if (!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$stmt = $db->prepare("SELECT * FROM user WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch();


?>










<!DOCTYPE html>
<html lang="en-US">
  <head>
    <title> Trouble Ticket Handler </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">

    <script>
        function showDep1() {
            document.getElementById("dep1").style.display = "block";
            document.getElementById("dep2").style.display = "none";
            document.getElementById("dep3").style.display = "none";
        }
        function showDep2() {
            document.getElementById("dep1").style.display = "none";
            document.getElementById("dep2").style.display = "block";
            document.getElementById("dep3").style.display = "none";
        }
        function showDep3() {
            document.getElementById("dep1").style.display = "none";
            document.getElementById("dep2").style.display = "none";
            document.getElementById("dep3").style.display = "block";
        }
    </script>

  </head>

    <body>
  <header>
    <h1> Trouble Ticket handler </h1>
    <h1> $ </h1>


  </header>

    <h1> MAIN PAGE </h1>

    <ul>
        <a href="ticket.php"><li> Create new Ticket</li></a>
        <a href="profile.php"><li>  <?= $user['name'] ?> </li></a>
    </ul>


    <nav id="menu">
      <!-- just for the hamburguer menu in responsive layout -->

      <ul>
        <li><a href="#" onclick="showDep1()">Show dep1</a></li>
        <li><a href="#" onclick="showDep2()">Show dep2</a></li>
        <li><a href="#" onclick="showDep3()">Show dep3</a></li>
      </ul>
    </nav>

    <a href="login.php">Logout</a>  


    <p id = "dep1" style="display: none;">dep1</p>
    <p id = "dep2" style="display: none;">dep2</p>
    <p id = "dep3" style="display: none;">dep3</p>

  




  <footer>
    LTW ticket project 2023
  </footer>
  </body>
</html>