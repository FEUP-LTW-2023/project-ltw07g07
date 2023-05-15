<?php

$ticket_id = 100;

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

if (isset($_POST['reply'])) {

  $message = $_POST['message'];

  $stmt = $db->prepare("INSERT INTO reply (client_id, message, ticket_id) VALUES (:client_id, :message,:ticket_id)");
  $stmt->bindParam(':client_id', $_SESSION['user_id']);
  $stmt->bindParam(':message', $message);
  $stmt->bindParam(':ticket_id', $ticket_id);

  $stmt->execute();

}


?>










<!DOCTYPE html>
<html lang="en-US">
  <head>
    <title> Trouble Ticket Handler </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style2.css">

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
        function showForm() {
            document.getElementById("reply").style.display = "block";
        }
    </script>

  </head>

  <body>

    <header>
      <h1> <a href="main.php"> Trouble Ticket Handler </a></h1>

    </header>

    <h1 class = "main"> MAIN PAGE </h1>

    <ul id = "menu">
        <a href="ticket.php"><li class="first"> Create new Ticket</li></a>
        <a href="profile.php"><li class="last">  <?= $user['name'] ?> </li></a>
    </ul>


    <nav id="menu">
      <!-- just for the hamburguer menu in responsive layout -->

      <ul>
        <li><a href="#" onclick="showDep1()">Accounting</a></li>
        <li><a href="#" onclick="showDep2()">Sales</a></li>
        <li><a href="#" onclick="showDep3()">Support</a></li>
      </ul>
    </nav>

    <a href="login.php" class = "a-prof">Logout</a>  


    <p id = "dep1" style="display: none;">dep1</p>
    <p id = "dep2" style="display: none;">dep2</p>
    <p id = "dep3" style="display: none;">dep3</p>

    
  <?php foreach ($tickets as $ticket):
     ?>
    <div id = 'ticket'>
      <h2><?= $ticket['client_name'] ?></h2>
      <p><?= $ticket['message'] ?></p>
      <p>Status: <?= $ticket['status'] ?></p>

    </div>
    <a href="#" class= "reply-ticket" onclick="showForm()">Reply</a>
      <form id="reply" style="display: none;" action="" method="post">
        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="5" required></textarea>
      <input type="submit" name = "reply" value="Reply">
        
    </form>
  <?php endforeach; ?>



    <footer>
      Trouble Ticket
    </footer>
  </body>
</html>