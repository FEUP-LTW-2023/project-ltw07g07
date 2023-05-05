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

$stmt = $db->prepare("SELECT user.name as client_name, ticket.message, ticket.status
                      FROM ticket
                      JOIN user ON ticket.client_id = user.id
                      WHERE user.id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$tickets = $stmt->fetchAll();


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
        <li><a href="#" onclick="showDep1()">Accounting</a></li>
        <li><a href="#" onclick="showDep2()">Sales</a></li>
        <li><a href="#" onclick="showDep3()">Support</a></li>
      </ul>
    </nav>

    <a href="login.php">Logout</a>  


    <p id = "dep1" style="display: none;">dep1</p>
    <p id = "dep2" style="display: none;">dep2</p>
    <p id = "dep3" style="display: none;">dep3</p>

  
<?php foreach ($tickets as $ticket): ?>
  <div id = 'ticket'>
    <h2><?= $ticket['client_name'] ?></h2>
    <p><?= $ticket['message'] ?></p>
    <p>Status: <?= $ticket['status'] ?></p>
  </div>
<?php endforeach; ?>



  <footer>
    Trouble Ticket
  </footer>
  </body>
</html>