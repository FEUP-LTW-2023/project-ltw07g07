<?php
session_start();
require_once('connection.php');
$db = getDataBaseConnection();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $db->prepare("SELECT * FROM user WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch();

/*if ($user['status'] == 'Agent'){
  echo '<script>
    document.getElementById("agent").style.display = "block";
  </script>';
}*/

$stmt = $db->prepare("SELECT user.name as client_name, ticket.message, ticket.status, ticket.department as dep, ticket.id as ticket_id
                      FROM ticket
                      JOIN user ON ticket.client_id = user.id
                      WHERE user.id = :id");

$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$tickets = $stmt->fetchAll();


if (isset($_POST['reply'])) {

  $message = $_POST['message'];
  $ticket_id = $_POST['ticket_id'];

  $stmt = $db->prepare("INSERT INTO reply (client_id, message, ticket_id) VALUES (:client_id, :message,:ticket_id)");
  $stmt->bindParam(':client_id', $_SESSION['user_id']);
  $stmt->bindParam(':message', $message);
  $stmt->bindParam(':ticket_id', $ticket_id);

  $stmt->execute();

}


$stmt = $db->prepare("SELECT message, ticket_id, name FROM reply, user
                      WHERE user.id = client_id
                       ORDER BY reply.id ASC");
$stmt->execute();
$replies = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>Trouble Ticket Handler</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style2.css">

    <script>
        function showForm(ticketId) {
            document.getElementById("reply-" + ticketId).style.display = "block";
        }

    </script>
</head>

<body>
<header>
    <h1><a href="main.php">Trouble Ticket Handler</a></h1>
</header>

<h1 class="main">MAIN PAGE</h1>

<ul id="menu">
    <a href="ticket.php">
        <li class="first">Create new Ticket</li>
    </a>
    <a href="profile.php">
        <li class="last"><?= $user['name'] ?></li>
    </a>
</ul>

<nav id="menu">
    <ul>
        <li><a href="#" onclick="showDep1()">Accounting</a></li>
        <li><a href="#" onclick="showDep2()">Sales</a></li>
        <li><a href="#" onclick="showDep3()">Support</a></li>
    </ul>
</nav>

<a href="login.php" class="a-prof">Logout</a>

<?php foreach ($tickets as $ticket): ?>
  <div id="ticket">
    <h2><?= $ticket['client_name'] ?></h2>
    <p><?= $ticket['message'] ?></p>
    <p>Status: <?= $ticket['status'] ?></p>
  </div>


  <?php foreach ($replies as $reply):
    if ($reply['ticket_id'] == $ticket['ticket_id']): ?>
      <div id = "reply">
        <h3><?= $reply['name'] ?></h3>
        <p><?= $reply['message'] ?></p>
      </div>
    <?php endif;
  endforeach; ?>


  <a href="#" class="reply-ticket" onclick="showForm(<?= $ticket['ticket_id'] ?>)">Reply</a>
  <form id="reply-<?= $ticket['ticket_id'] ?>" style="display: none;" action="" method="post">
    <label for="message">Message:</label>
    <textarea id="message" name="message" rows="5" required></textarea>
    <input type="hidden" name="ticket_id" value="<?= $ticket['ticket_id'] ?>">
    <input type="submit" name="reply" value="Reply">
  </form>

<?php endforeach; ?>

  <div id = "agent" style = "display:none;">
    <p> AGENTE </p>
  </div>


<footer>
   Trouble Ticket
</footer>
</body>
</html>
